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

class Installer
{
    private $directory = WEB_SCHEMA_DIR . '/resources/migration';

    /**
     * @var \wpdb
     */
    private $db;

    /**
     * Installer constructor.
     */
    public function __construct()
    {
        global $wpdb;

        $this->db = $wpdb;

        register_activation_hook('webschema/schema.php', array($this, 'runOnce'));
    }

    public static function boot()
    {
        return (new self());
    }

    /**
     * @param  bool $import
     * @throws \RuntimeException
     * @return bool
     */
    public function runOnce($import = true)
    {
        $dbSchemas = include $this->directory . '/install.php';

        foreach ($dbSchemas as $name => $schema) {
            if (!$this->db->query($schema)) {
                throw new \RuntimeException('Web Schema: Database table ' . $name . ' could not be created.');
            }
        }

        return ($import || $import === '') ? $this->import() : true;
    }

    /**
     * Import the data
     */
    private function import()
    {
        $data = json_decode(file_get_contents($this->directory . '/schema.json'), true);

        PropertyFactory::createOrUpdate($data['properties']);
        TypeFactory::createOrUpdate($data['types']);
        TypePropertyFactory::createOrUpdate($data['types']);

        return true;
    }
}