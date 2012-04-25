<?php
class SchemaProperty {
	
	private static $_instance;
	
	private $properties;
	private $tableName;
	private $processProperties;
	
	public function __construct()
	{
		global $wpdb;
		
		$this->properties = array();
		$this->tableName = $wpdb->prefix . 'web_schema_property';
		
		register_activation_hook( 'webschema/schema.php', array( $this, 'install' ) );
	}
	
	/**
	*
	* Runs the installation process for the class.
	*  - Installs the database table
	*/
	public function install()
	{
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
		global $wpdb;
	
		if ( $wpdb->get_var( "SHOW TABLES LIKE '". $this->tableName ."'" ) != $this->tableName )
		{
			$sql = "CREATE TABLE " . $this->tableName . " (
						`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`comment` VARCHAR( 255 ) NULL,
						`name` VARCHAR( 50 ) NOT NULL,
						`label` VARCHAR( 50 ) NOT NULL,
						`ranges` LONGTEXT NULL,
						UNIQUE (  `name` ) );";
	
			$sql = str_replace( array( "\n", "\t" ), '', $sql );
	
			dbDelta( $sql );
		}
	}
	
	/**
	 * 
	 * Returns the property ID by name
	 * @param object $property
	 * @return int
	 */
	public function getPropertyIDByName( $property )
	{
		if ( array_key_exists( $property, $this->properties ) )
			return $this->properties[$property]['id'];
		
		global $wpdb;
		
		$property = $wpdb->_escape( $property );
		
		$sql = "SELECT * FROM $this->tableName WHERE name = '$property'";
		
		if ( $result = $wpdb->get_results( $sql, ARRAY_A ) )
			$this->properties[$property] = $result;
		
		return $result['id'];
	}
	
	/**
	 * 
	 * Public call to add a property to the database
	 * @param object $property
	 * @return boolean|number
	 */
	public function addProperty( $property )
	{
		if ( empty( $property ) ) return false;
		
		return $this->add( $this->processProperties->$property );
	}
	
	/**
	 * 
	 * Returns the table name.
	 * @return string
	 */
	public function getTableName()
	{
		return $this->tableName;
	}
	
	/**
	 * 
	 * Add a property to the database.
	 * @param object $property
	 * @return boolean|number
	 */
	private function add( $property )
	{
		global $wpdb;
		
		if ( empty( $property ) ) return false;
		
		$property->ranges = ( is_array( $property->ranges ) )? serialize( $property->ranges ) : $property->ranges;
		
		$property = $wpdb->_escape( get_object_vars( $property ) );
		
		extract( $property );
		
		$sql = "INSERT INTO $this->tableName ( comment, name, label, ranges ) VALUES ( '$comment', '$id', '$label', '$ranges' )";
		
		if ( $wpdb->query( $sql ) )
		{
			$this->properties[$id]['id'] = $wpdb->insert_id;
			
			return $wpdb->insert_id;
		}
		
		return false;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $properties
	 */
	public function setProcessProperties( $properties )
	{
		$this->processProperties = $properties;
	}
	
	/**
	*
	* Returns the table name
	* @return string
	*/
	public function truncateRecords()
	{
		return $this->truncate();
	}
	
	/**
	*
	* Public method to remove all records in the database.
	* @return mixed
	*/
	private function truncate()
	{
		global $wpdb;
	
		return $wpdb->query( "TRUNCATE $this->tableName" );
	}
	
	/**
	*
	* Create a static instance of the class
	* @return SchemaProperty
	*/
	public static function getInstance()
	{
		if ( empty( self::$_instance ) )
			self::$_instance = new SchemaProperty();
		
		return self::$_instance;
	}
}

?>