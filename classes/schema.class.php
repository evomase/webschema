<?php

class Schema
{

    private static $_instance;

    private $settings = array();
    private $tableName;
    private $formName = 'web_schema';
    private $schema = array();
    private $postMetaKey = '_web_schema';

    public function __construct()
    {
        register_activation_hook('webschema/schema.php', array($this, 'install'));

        global $wpdb;

        $this->tableName = $wpdb->prefix . 'web_schema';

        add_action('save_post', array($this, 'adminPostPageSave'), 10, 2);
        add_action('add_meta_boxes', array($this, 'adminPostPageInit'));

        add_action('admin_menu', array($this, 'menu'));
        add_action('admin_print_styles-settings_page_web_schema', array($this, 'enqueueStyle'));
        add_action('admin_print_styles-post.php', array($this, 'enqueueStyle'));
        add_action('admin_print_styles-post-new.php', array($this, 'enqueueStyle'));

        add_action('wp_ajax_schema_get_types', array($this, 'ajax'));
        add_action('wp_ajax_schema_get_types_html', array($this, 'ajax'));
        add_action('wp_ajax_schema_get_props_html', array($this, 'ajax'));

        add_filter('mce_external_plugins', array($this, 'registerTinyMCEPlugin'));
        add_filter('mce_buttons', array($this, 'addTinyMCEButtons'));
        add_filter('tiny_mce_before_init', array($this, 'tinyMCEOptions'));
    }

    /**
     *
     * Create a static instance of the class
     * @return Schema
     */
    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new Schema();
        }

        return self::$_instance;
    }

    /**
     *
     * Runs the installation process for the class.
     *  - Installs the database table
     */
    public function install()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        global $wpdb;

        if ($wpdb->get_var("SHOW TABLES LIKE '" . $this->tableName . "'") != $this->tableName) {
            $sql = "CREATE TABLE " . $this->tableName . " (
						`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`type_id` INT NOT NULL,
						`property_id` INT NOT NULL,
						UNIQUE (  `type_id`, `property_id` ) );";

            $sql = str_replace(array("\n", "\t"), '', $sql);

            dbDelta($sql);
        }
    }

    /**
     * Used to add the configuration page to the settings menu
     */
    public function menu()
    {
        add_options_page('Web Schema', 'Web Schema', 'manage_options', 'web_schema', array($this, 'page'));
    }

    /**
     * Adds meta boxes to all content types
     */
    public function adminPostPageInit()
    {
        $contentTypes = get_post_types(array('public' => true));
        unset($contentTypes['attachment']);

        foreach ($contentTypes as $id => $contentType) {
            add_meta_box('web_schema', 'Web Schema', array($this, 'adminPostPage'), $id, 'side', 0);
        }
    }

    /**
     * This function is called when a post has been saved. It saves the post schema details
     * @param int      $postID
     * @param StdClass $post
     * @return int|boolean
     */
    public function adminPostPageSave($postID, $post)
    {
        if (empty($_POST[$this->formName])) {
            return;
        }

        $content = $_POST[$this->formName];

        $postSchema = get_post_meta($post->ID, $this->postMetaKey, true);

        if (empty($postSchema)) {
            return add_post_meta($post->ID, $this->postMetaKey, $content, true);
        } else {
            return update_post_meta($post->ID, $this->postMetaKey, $content);
        }
    }

    /**
     * Renders the meta box on the admin post interface.
     * @param StdClass $post
     */
    public function adminPostPage($post)
    {
        $schema = $this->getSchema();
        $postSchema = $this->getPostSchema($post->ID);

        require_once(WEB_SCHEMA_PLUGIN_DIR . '/templates/post_schema.tpl.php');
    }

    /**
     *
     * Returns the full schema record from the database.
     * @return array
     */
    public function getSchema()
    {
        global $wpdb;

        if ($this->schema) {
            return $this->schema;
        }

        $typeTable = SchemaType::getInstance()->getTableName();
        $propertyTable = SchemaProperty::getInstance()->getTableName();

        $sql = "SELECT st.id AS st_id, st.comment AS st_comment, st.name AS st_name, st.label AS st_label, st.url, st.parent, ";
        $sql .= "sp.id AS sp_id, sp.name AS sp_name, sp.label AS sp_label, sp.comment AS sp_comment, sp.ranges FROM $this->tableName AS s ";
        $sql .= "LEFT JOIN $typeTable AS st ON st.id = s.type_id ";
        $sql .= "LEFT JOIN $propertyTable AS sp ON sp.id = s.property_id ";
        $sql .= "ORDER BY st_name, sp.name ASC";

        $result = $wpdb->get_results($sql);

        if (empty($result)) {
            return $result;
        }

        $types = array();

        foreach ($result as $type) {
            if (!array_key_exists($type->st_id, $types)) {
                $types[$type->st_id] = array(
                    'id'         => $type->st_id,
                    'comment'    => htmlspecialchars($type->st_comment),
                    'name'       => $type->st_name,
                    'label'      => $type->st_label,
                    'url'        => $type->url,
                    'parent'     => $type->parent,
                    'properties' => array()
                );
            }

            if (!array_key_exists($type->sp_id, $types[$type->st_id]['properties']) && $type->sp_id) {
                $types[$type->st_id]['properties'][$type->sp_id] = array(
                    'id'      => $type->sp_id,
                    'name'    => $type->sp_name,
                    'label'   => $type->sp_label,
                    'comment' => htmlspecialchars($type->sp_comment),
                    'ranges'  => (is_serialized($type->ranges)) ? unserialize($type->ranges) : array()
                );
            }
        }

        return $this->schema = $types;
    }

    /**
     * Returns the post schema
     * @param int $postID
     * @return array
     */
    public function getPostSchema($postID)
    {
        $postSchema = get_post_meta($postID, $this->postMetaKey, true);

        if (empty($postSchema) || empty($postSchema['type'])) {
            return array();
        }

        $postSchema['url'] = SchemaType::getInstance()->getURL($postSchema['type']);

        return $postSchema;
    }

    /**
     * Renders the configuration page and handles all request from the it too.
     */
    public function page()
    {
        if ($_POST['submit']) {
            update_option('web_schema', $_POST[$this->formName]);
        }

        $this->settings = get_option('web_schema', array('schema_json_url' => 'http://schema.rdfs.org/all.json'));

        if ($_POST['update_records']) {
            $this->updateRecords();
        }

        if ($_POST['truncate_records']) {
            $this->truncateRecords();
        }

        $schema = $this->settings;

        include_once(WEB_SCHEMA_PLUGIN_DIR . '/templates/info.tpl.php');
    }

    /**
     *
     * Process the schema records.
     * @return boolean
     */
    private function updateRecords()
    {
        $url = $this->settings['schema_json_url'];

        if (empty($url) || !preg_match('/http:\/\/[\w\.\-\/]+/i', $url)) {
            return false;
        }

        if (!(($schema = file_get_contents($url)) && ($schema = json_decode($schema)))) {
            return false;
        }

        SchemaType::getInstance()->setProcessTypes($schema->types);
        SchemaProperty::getInstance()->setProcessProperties($schema->properties);

        return SchemaType::getInstance()->process();
    }

    /**
     *
     * Removes all records from all the schema tables.
     * @return true
     */
    public function truncateRecords()
    {
        $this->truncate();
        SchemaType::getInstance()->truncateRecords();
        SchemaProperty::getInstance()->truncateRecords();

        return true;
    }

    /**
     *
     * Removes all record from the schema table
     */
    private function truncate()
    {
        global $wpdb;

        return $wpdb->query("TRUNCATE $this->tableName");
    }

    /**
     *
     * Adds a stylesheet to the global stylesheet array
     */
    public function enqueueStyle()
    {
        wp_enqueue_style('schema', plugins_url('webschema') . '/files/css/schema.css');
    }

    /**
     *
     * Links a type and property together and adds it to the database.
     * @param int $typeID
     * @param int $propertyID
     * @return int
     */
    public function addTypeProperties($typeID, $propertyID)
    {
        global $wpdb;

        $sql = $wpdb->prepare("INSERT INTO $this->tableName ( type_id, property_id ) VALUES ( %d, %d )", $typeID,
            $propertyID);

        return $wpdb->query($sql);
    }

    /**
     *
     * Checks to see if a property exists within a type
     * @param int $typeID
     * @param int $propertyID
     * @return int
     */
    public function typePropertyExists($typeID, $propertyID)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT id FROM $this->tableName WHERE type_id = %d AND property_id = %d", $typeID,
            $propertyID);

        return $wpdb->get_var($sql);
    }

    /**
     *
     * Registers the tinyMCE schema plugin
     * @param array $plugins
     * @return array
     */
    public function registerTinyMCEPlugin(Array $plugins)
    {
        $plugins['schema'] = plugins_url('webschema') . '/files/js/tinymce/schema.js';

        return $plugins;
    }

    /**
     *
     * Adds the tinyMCE schema buttons
     * @param array $buttons
     * @return array
     */
    public function addTinyMCEButtons(Array $buttons)
    {
        return array_merge($buttons, array('schema_type', 'schema_prop'));
    }

    /**
     *
     * Sets the tinyMCE custom options
     * @param array $options
     * @return array
     */
    public function tinyMCEOptions(Array $options)
    {

        $elements = ',@[itemtype|itemscope|itemprop|id|class|style|title|dir<ltr?rtl|lang|xml::lang|onclick|ondblclick|onmousedown|onmouseup|onmouseover|onmousemove';
        $elements .= '|onmouseout|onkeypress|onkeydown|onkeyup],a[rel|rev|charset|hreflang|tabindex|accesskey|type|name|href|target|title|class|onfocus|onblur],strong/b,em/i,strike,u,';
        $elements .= '#p,-h1,-h2,-h3,-h4,img[longdesc|usemap|src|border|alt=|title|hspace|vspace|width|height|align],-ul[type|compact],ol,-li,';
        $elements .= 'object[classid|width|height|codebase|*],-strong,em,param[name|value|_value],-span,-div';

        $options['extended_valid_elements'] .= $elements;

        return $options;
    }

    /**
     *
     * The ajax method is called when ever an ajax request is made to the Schema class.
     */
    public function ajax()
    {
        switch ($_GET['action']) {
            case 'schema_get_types':
                $types = $this->getSchema();
                $content = json_encode($types);
                $content_type = 'application/json';
                break;

            case 'schema_get_types_html':
                include_once(WEB_SCHEMA_PLUGIN_DIR . '/templates/types.tpl.php');
                $content_type = 'text/html';
                break;

            case 'schema_get_props_html':
                include_once(WEB_SCHEMA_PLUGIN_DIR . '/templates/props.tpl.php');
                $content_type = 'text/html';
                break;
        }

        @header('Content-type: ' . $content_type);
        @header('Cache-Control: no-cache, must-revalidate, no-store');
        @header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        @header('Pragma: no-cache');
        echo $content;
        exit;
    }
}