<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/12/2016
 * Time: 17:12
 */

namespace WebSchema\Utils;


class Installer
{
    private $directory = WEB_SCHEMA_DIR . '/resources/migration';
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
     * @throws \RuntimeException
     * @return bool
     */
    public function runOnce()
    {
        $dbSchemas = include $this->directory . '/install.php';

        foreach ($dbSchemas as $name => $schema) {
            if (!$this->db->query($schema)) {
                continue;

                throw new \RuntimeException('Web Schema: Database table ' . $name . ' could not be created.');
            }
        }

        return true;
    }

    private function import()
    {
        //$data = json_decode(file_get_contents($this->directory . '/schema.json'), true);
    }
}