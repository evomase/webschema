<?php 
class SchemaType {
	
	private static $_instance;
	
	private $tableName;
	private $processTypes;
	private $types = array();
	
	public function __construct()
	{
		global $wpdb;
		
		$this->tableName = $wpdb->prefix . 'web_schema_type';
		
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
						`url` VARCHAR( 255 ) NOT NULL,
						`parent` INT NULL,
						UNIQUE (  `name`, `parent` ) );";
	
			$sql = str_replace( array( "\n", "\t" ), '', $sql );
	
			dbDelta( $sql );
		}
	}
	
	/**
	 * 
	 * Adds a type to the database
	 * @param object $type
	 * @return true
	 */
	private function add( $type )
	{
		global $wpdb;
		
		$parent = $this->getParentID( $type->id );
		
		$type = $wpdb->_escape( get_object_vars( $type ) );
		
		extract( $type );
		
		$sql = "INSERT INTO $this->tableName ( comment, name, label, url, parent ) VALUES ( '$comment', '$id', '$label', '$url', $parent )";
		
		if ( !$wpdb->query( $sql ) ) return false;
		
		$id = $wpdb->insert_id;
		
		if ( !empty( $properties ) )
			$this->processProperties( $id, $properties );
		
		return true;
	}
	
	/**
	 * 
	 * Gets a type parents ID
	 * @param object $type
	 * @return int
	 */
	private function getParentID( $type )
	{
		$parentID = 0;
		
		if ( !empty( $this->processTypes ) )
		{			
			if ( ( $ancestors = $this->processTypes->$type->ancestors  ) )
			{
				$parent = end( $ancestors );
			
				if ( !( $parentID = $this->exists( $parent ) ) )
					$parentID = $this->add( $this->processTypes->$parent );
			}
		}
		
		return $parentID;
	}
	
	/**
	 * 
	 * Update a type in the database
	 * @param int $typeID
	 * @param object $type
	 * @return true
	 */
	private function update( $typeID, $type )
	{
		global $wpdb;
		
		$parent = $this->getParentID( $type->id );
		
		$type = $wpdb->_escape( get_object_vars( $type ) );
		extract( $type );  
		
		$sql = "UPDATE $this->tableName SET comment = '$comment', name = '$id', label = '$label', url = '$url', parent = '$parent' WHERE id = $typeID ";
		
		$wpdb->query( $sql ); 
		
		if ( !empty( $properties ) )
			$this->processProperties( $id, $properties );
		
		return true;
	}
	
	/**
	 * 
	 * Process all properties for a type.
	 * @param int $id
	 * @param array $properties
	 */
	private function processProperties( $id, Array $properties = array() )
	{
		foreach( $properties as $property )
		{
			if ( !( $propertyID = SchemaProperty::getInstance()->getPropertyIDByName( $property ) ) )
				$propertyID = SchemaProperty::getInstance()->addProperty( $property );
			
			if ( !Schema::getInstance()->typePropertyExists( $id, $propertyID ) )
				Schema::getInstance()->addTypeProperties( $id, $propertyID );
		}
	}
	
	/**
	 * 
	 * Sets the schema types for processing
	 * @param object $types
	 */
	public function setProcessTypes( $types )
	{
		$this->processTypes = $types;
	}
	
	/**
	 * 
	 * Starts the processing of all types in the schema record for storing.
	 */
	public function process()
	{
		foreach( $this->processTypes as $type )
		{
			if ( !( $id = $this->exists( $type->id ) ) )
				$this->add( $type );
		}
		
		return true;
	}
	
	/**
	 * 
	 * Returns the table name
	 * @return string
	 */
	public function getTableName()
	{
		return $this->tableName;
	}
	
	/**
	 * 
	 * Public method to remove all records in the database.
	 * @return mixed
	 */
	public function truncateRecords()
	{
		return $this->truncate();
	}
	
	/**
	 * 
	 * Removes all records in the database;
	 */
	private function truncate()
	{
		global $wpdb;
	
		return $wpdb->query( "TRUNCATE $this->tableName" );
	}
	
	/**
	 * 
	 * Check to see if the type already exists in the database
	 * @param object $type
	 */
	private function exists( $type )
	{
		if ( $id = array_search( $type, $this->types ) )
			return $id;
		
		global $wpdb;
		
		$type = $wpdb->_escape( $type );
		
		$sql = "SELECT id FROM $this->tableName WHERE name = '$type'";
		
		if ( $id = $wpdb->get_var( $sql ) )
		{
			$this->types[$id] = $type;
			return $id;
		}
		
		return 0;
	}
	
	/**
	*
	* Create a static instance of the class
	* @return SchemaType
	*/
	public static function getInstance()
	{
		if ( empty( self::$_instance ) )
			self::$_instance = new SchemaType();
		
		return self::$_instance;
	}
}
?>