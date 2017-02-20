<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/12/2016
 * Time: 17:12
 */

namespace WebSchema\Utils;

use WebSchema\Factory\PropertyFactory;
use WebSchema\Factory\TypeFactory;
use WebSchema\Factory\TypePropertyFactory;
use WebSchema\Utils\Interfaces\Bootable;

class Installer implements Bootable
{
    private $directory = WEB_SCHEMA_DIR . '/resources/migration';

    /**
     * @var \wpdb
     */
    private $db;
    private $import = true;

    /**
     * Installer constructor.
     */
    public function __construct()
    {
        global $wpdb;

        $this->db = $wpdb;

        register_activation_hook('webschema/schema.php', array($this, 'runOnce'));
    }

    /**
     * @return Installer
     */
    public static function boot()
    {
        return (new self());
    }

    public static function shutdown()
    {
    }

    /**
     * @return $this
     */
    public function disableImport()
    {
        $this->import = false;

        return $this;
    }

    /**
     * @throws \RuntimeException
     * @return bool
     */
    public function runOnce()
    {
        $dbSchemas = include $this->directory . '/install.php';

        foreach ($dbSchemas as $name => $schema) {
            if (!$this->db->query($schema)) {
                throw new \RuntimeException('Web Schema: Database table ' . $name . ' could not be created.');
            }
        }

        return ($this->import) ? $this->import() : true;
    }

    /**
     * Import the data
     * @link http://schema.link.fish/downloads/all.json
     */
    private function import()
    {
        $data = json_decode(file_get_contents($this->directory . '/schema.json'), true);

        PropertyFactory::createOrUpdate($data['properties']);
        TypeFactory::createOrUpdate($data['types'] + $data['datatypes']);
        TypePropertyFactory::createOrUpdate($data['types']);

        file_put_contents(WEB_SCHEMA_BASE_DIR . '/wp-content/uploads/errors.txt', ob_get_contents());

        return true;
    }
}