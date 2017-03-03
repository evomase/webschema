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
use WebSchema\Models\Traits\HasData;
use WebSchema\Utils\Interfaces\Bootable;

class Installer implements Bootable
{
    use HasData;

    const MIGRATION_DIRECTORY = WEB_SCHEMA_DIR . '/resources/migration';
    const OPTION_SCHEMA_PATH = 'schema-path';

    /**
     * @var \wpdb
     */
    private $db;
    private $import = true;

    private $data = [
        self::OPTION_SCHEMA_PATH => self::MIGRATION_DIRECTORY . '/schema.json'
    ];

    /**
     * Installer constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        global $wpdb;

        $this->db = $wpdb;
        $this->fill($options);

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
        $dbSchemas = include self::MIGRATION_DIRECTORY . '/install.php';

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
        $data = json_decode(file_get_contents($this->data[self::OPTION_SCHEMA_PATH]), true);

        PropertyFactory::createOrUpdate($data['properties']);
        TypeFactory::createOrUpdate($data['types'] + $data['datatypes']);
        TypePropertyFactory::createOrUpdate($data['types']);

        return true;
    }
}