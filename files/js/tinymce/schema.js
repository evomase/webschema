( function( $ ){
	Schema = function(){
		SchemaType.prototype = this;
		SchemaProp.prototype = this;
		
		this.editor
		this.iframe
		this.iframeID
		this.window
		this.selection
		this.tip
		this.types;
		
		this.tipShown = false;
		
		/**
		 * Initiates the schema options
		 * 	- adds the tooltip
		 */
		this.init = function( ed ){
			if ( !this.tip )
			{
				//SETUP
				
				this.tip = $( '<span id="schema_tip">' ).css( { 
					'position' : 'absolute',
					'display' : 'none',
					'border' : '1px solid #000000',
					'background-color' : '#FFFF99',
					'z-index' : '1',
					'padding' : '0px 3px',
					'font-size' : '0.85em',
					'box-sizing' : 'border-box'
				});
				
				$( 'body' )
					.prepend( this.tip )
					.css( { 'position' : 'relative' } );		
			}
			
			this.bindTip();
		};
		
		/**
		 * Hides the tooltip
		 */
		this.hideTip = function(){
			this.tipShown = false;
			this.tip.hide();
		};
		
		/**
		 * Adds the mouseover, mousemove and mouseout events to the elements which the tooltip acts upon
		 */
		this.bindTip = function( selector ){
			var self = this;
			var editor = $( '#' + this.editor.id + '_ifr' ).contents();
			
			selector = ( selector )? $( selector ) : $( '[itemtype],[itemprop]', editor );
			
			selector.bind( 'mouseover, mousemove', function( e ){
				e.stopPropagation();
				self.setTipText( this, e );
			});
			
			selector.bind( 'mouseout', function( e ){
				if ( schema.tipShown )
					self.hideTip();
			});
		};
		
		/**
		 * Sets the text of the tooltip
		 */
		this.setTipText = function( node, e ){
			var text = [];
			var typeURL = node.getAttribute( 'itemtype' );
			var prop = node.getAttribute( 'itemprop' );
			
			if ( typeURL && !prop ) //<span itemtype=""></span>
				text.push( 'Schema [ <strong>Type</strong>: ' + schemaType.getTypeByURL( typeURL )['label'] + ' ] ' );
			
			if ( prop )
			{
				propTypeURL = $( node ).parents( '[itemtype]:first' ).attr( 'itemtype' );
				
				type = schemaType.getTypeByURL( propTypeURL );
				
				if ( !text.length ) //<span itemtype="" itemprop=""></span>
					text.push( 'Schema [ <strong>Type</strong>: ' + type['label'] );
				
				for ( var id in type['properties'] )
				{
					if ( type['properties'][id]['name'] != prop ) continue;
					
					prop = type['properties'][id]['label'];
					
					break;
				}
				
				text.push( '<strong>Property</strong>: ' + prop + ' ] ' );
			}
			
			this.toolTip( text.join( ', ' ), e );
		};
		
		/**
		 * Renders the tooltip
		 */
		this.toolTip = function( text, e ){
			var editor = $( '#' + this.editor.id + '_ifr' );
			var editorWidth = editor.width();
			
			var top = ( editor.offset().top + e.clientY ) - 30;
			var left = editor.offset().left + e.clientX;
			
			var width = left + this.tip.width();
			
			if ( width > editorWidth )
				left -= ( width - editorWidth );
			
			this.tip
				.html( text )
				.css( { 'top' : top, 'left' : left } )
				.show();
			
			this.tipShown = true;
		};
		
		/**
		 * Callback function used for when the buttons iframes are shown
		 * @params object o
		 */
		this.onWindowLoad = function( o ){
			var self = this;
			var window_id = this.editor.windowManager.params.mce_window_id;
			var iframe = $( '#' + window_id + '_ifr' );
			
			$( iframe ).load( function(){
				self.iframe = $( this ).contents();
				self.iframeID = iframe;
				self.window = schema.editor.windowManager.windows[window_id];
				
				$( 'body', self.iframe ).css( { 'min-width' : '0' } );
				
				o.render();
			});
		};
		
		/**
		 * Callback function to render the button iframe html
		 */
		this.render = function(){};
		
		/**
		 * Returns the selection node
		 * 
		 * @returns HTMLElement
		 */
		this.getNode = function(){
			return this.selection.getNode();
		};
		
		/**
		 * Retrieves all the schema types/properties via ajax
		 * 
		 * @returns json
		 */
		this.getTypes = function(){
			if ( this.types )
				return this.types;
			
			self = this;
			
			$.ajax({
				url: ajaxurl,
				data: $.param( { 'action' : 'schema_get_types'} ),
				dataType: 'json',
				async: false,
				success: function( data ){
					self.types = data;
				}
			});
			
			return this.types;
		};
	};
	
	SchemaType = function(){
		this.postSchemaList = '#post_schema_type_list';
		
		/**
		 * Initiates the schema type button and options
		 */
		this.init = function( ed, url ){
			var self = this;
			
			//Commands
			ed.addCommand( 'schema_type', function(){
				self.onCommand( ed );
			});			
			
			//Buttons
			ed.addButton( 'schema_type', {
				title: 'Add/Edit Schema Type',
				cmd: 'schema_type'
			});
			
			//Disable/Active button
			ed.onNodeChange.add( function( ed, cm, n, co ){
				cm.setDisabled( 'schema_type', co || ed.selection.getNode().tagName.toLowerCase() == 'body' );
				cm.setActive( 'schema_type', co || ed.selection.getNode().getAttribute( 'itemtype' ) );
			});
			
			ed.onLoadContent.add( function( ed, cm ){
				if ( ed === tinymce.editors[0] )
					self.postSchema();
			});
		};
		
		/**
		 * Opens an iframe once the schema type button is pressed.
		 */
		this.onCommand = function( ed ){
			schema.editor = ed;
			schema.selection = ed.selection;
			
			ed.windowManager.open({
				file: ajaxurl + '?action=schema_get_types_html',
				width: 232,
				height: 124,
				inline: true
			});
			
			this.onWindowLoad( this );
		};
		
		/**
		 * Sets the post schema
		 */
		this.postSchema =  function(){
			var self = this;
			var editor = $( '#' + this.editor.id + '_ifr' ).contents();
			var type = this.getPostSchemaType();
			
			$( this.postSchemaList ).bind( 'change', function(){
				self.changePostSchema();
			});
			
			if ( !$.isEmptyObject( type ) )
				$( 'body', editor ).attr( 'itemtype', type['url'] );
		};
		
		/**
		 * Callback function for when the post schema is changed. Also sets the post schema
		 */
		this.changePostSchema = function(){
			var editor = $( '#' + tinymce.editors[0].id + '_ifr' ).contents();
			var type = this.getPostSchemaType();
			
			var title_prop_list = $( '#post_schema_prop_list' );
			
			if ( !type || $.isEmptyObject( type['properties'] ) ) {
				$( 'body', editor ).removeAttr( 'itemtype' );
				
				return;				
			}
			
			title_prop_list.html( '<option value="0">Please select....</option>' );
			
			for ( var id in type['properties'] )
			{
				var prop = type['properties'][id];
				var option = $( '<option>', { 'value' : id } );
				option.html( prop.label );
				
				title_prop_list.append( option );
			}
			
			$( 'body', editor ).attr( 'itemtype', type['url'] );
		};
		
		/**
		 * Returns the post schema type
		 * @returns json
		 */
		this.getPostSchemaType = function(){
			var id = $( 'option:selected', this.postSchemaList ).val();
			var type = ( !$.isEmptyObject( this.types[id] ) )? this.types[id] : null;
			
			return type;
		};
		
		/**
		 * Callback function to render the button iframe html
		 */
		this.render = function(){
			var self = this;
			var iframe = this.iframe;
			var list = $( '#type_list', iframe );
			var nodeType = this.getType();
			
			for( var id in this.types )
			{
				var type = this.types[id];
				var selected = '';
				
				if ( nodeType && ( nodeType == type.url ) )
					selected = ' selected="selected"';
				
				var option = $( '<option' + selected + '>' + type.label + '</option>' );
				option.data( { 'id' : id } );
				
				list.append( option );
			}
			
			if ( nodeType )
				$( '#add', iframe ).html( 'Update' );
			
			$( 'button', iframe ).bind( 'click', function( e ){
				e.preventDefault();
				
				switch( $( this ).attr( 'id' ) )
				{
					case 'add':
						self.add();
						break;
						
					case 'remove':
						self.remove();
						break;
				}
				
				self.editor.windowManager.close( self.window.id );
			});
		};
		
		/**
		 * Adds a schema type
		 */
		this.add = function( id ){
			id = ( id )? id : $( '#type_list option:selected', this.iframe ).data( 'id' );
			var type = this.types[id];
			var node = this.getNode();
			var content = this.selection.getContent();
			
			if ( $( node ).html() == content || $( node ).text() == content )
			{
				node.setAttribute( 'itemscope', 'itemscope' );
				node.setAttribute( 'itemtype', type.url );
			}
			else
			{
				content = '<span id="' + id + '" itemscope="itemscope" itemtype="' + type.url + '">' + content + '</span>';
				this.selection.setContent( content );
				
				node = $( '#' + id, node );
				node.removeAttr( 'id' );
			}
			
			this.bindTip( node );
		};
		
		/**
		 * Adds a schema nested type - range
		 */
		this.addRangeType = function( id, node ){
			var type = this.types[id];
			
			node.setAttribute( 'itemscope', 'itemscope' );
			node.setAttribute( 'itemtype', type.url );
			
			this.bindTip( node );
		}
		
		/**
		 * Removes a schema type
		 */
		this.remove = function(){
			var node = this.getNode();
			
			schemaProp.removeAll( node );
			
			$( node ).find( '[itemscope]' ).each( function(){
				_remove( this );
			});
			
			_remove( node );	
		};
		
		/**
		 * Returns a schema type by URL
		 */
		this.getTypeByURL = function( url ){
			for ( var id in this.types )
			{
				if ( this.types[id]['url'] == url )
					return this.types[id];
			}
			
			return null;
		};
		
		/**
		 * Returns a schema type by Name
		 */
		this.getTypeByName = function( name ){
			for ( var id in this.types )
			{
				if ( this.types[id]['name'] == name )
					return this.types[id];
			}
			
			return null;
		};
		
		/**
		 * Returns a selection node's schema type
		 */
		this.getType = function(){
			if ( type = this.getNode().getAttribute( 'itemtype' ) )
				return type;
			
			return null;
		};
		
		/**
		 * Removes a schema type - private function
		 */
		_remove = function( node ){
			if ( $( node ).is( 'span' ) )
				schemaType.editor.dom.remove( node, true );
			else
			{
				node.removeAttribute( 'itemscope' );
				node.removeAttribute( 'itemtype' );
			}	
		};
	};
	
	SchemaProp = function(){
		
		/**
		 * Initiates the schema property button and options
		 */
		this.init = function( ed, url ){
			var self = this;
			
			//Commands
			ed.addCommand( 'schema_prop', function(){
				self.onCommand( ed );
			});
			
			//Buttons
			ed.addButton( 'schema_prop', {
				title: 'Add/Edit Schema Property',
				cmd: 'schema_prop'
			});
			
			//Disable/Active button
			ed.onNodeChange.add( function( ed, cm, n, co ){
				cm.setDisabled( 'schema_prop', co || ed.selection.getNode().tagName.toLowerCase() == 'body' );
				cm.setActive( 'schema_prop', co || ed.selection.getNode().getAttribute( 'itemprop' ) );
			});
		};
		
		/**
		 * Opens an iframe once the schema type button is pressed.
		 */
		this.onCommand = function( ed ){
			schema.editor = ed;
			schema.selection = ed.selection;
			
			if ( !this.getTypeNode() )
			{
				alert( 'Please add a type before adding a property' );
				return;
			}
			
			ed.windowManager.open({
				file: ajaxurl + '?action=schema_get_props_html',
				width: 181,
				height: 124,
				inline: true
			});
			
			this.onWindowLoad( this );
		};
		
		/**
		 * Callback function to render the button iframe html
		 */
		this.render = function() {
			var self = this;
			var iframe = this.iframe;
			var list = $( '#type_list', iframe ); 
			var node = this.getNode();
			var type = {};
			var nodeProp = node.getAttribute( 'itemprop' );
			var content = this.selection.getContent();
			var edit = ( nodeProp && ( content == $( node ).text() || content == $( node ).html() ) )? true : false;
			
			if ( ( ( content == $( node ).text() ) || content == $( node ).html() ) )
				type = schemaType.getTypeByURL( this.getTypeNode( $( node ).parent() ).attr( 'itemtype' ) );
			else
				type = schemaType.getTypeByURL( this.getTypeNode().attr( 'itemtype' ) );
			
			if ( $.isEmptyObject( type['properties'] ) ) return;
			
			for( var id in type['properties'] )
			{
				var prop = type['properties'][id];
				var selected = '';
				
				if ( nodeProp && ( nodeProp == prop.name ) )
					selected = ' selected = "selected"';
					
				var option = $( '<option' + selected + '>' + prop.label + '</option>' );
				option.data( { 'id' : id, 'type' : type } );
				
				list.data( { 'id' : type['id'] } );
				list.append( option );
			}
			
			if ( edit )
				$( '#add', iframe ).html( 'Update' );
			
			list.bind( 'change', function(){
				schemaProp.renderRanges( $( this ) );
			});
			
			$( 'button', iframe ).bind( 'click', function( e ){
				e.preventDefault();
				
				switch( $( this ).attr( 'id' ) )
				{
					case 'add':
						if ( edit )
							node = self.edit();
						else
							node = self.add();
						
						if ( ( propType = $( '#prop_type select option:selected', self.iframe ) ).length )
						{
							rangeID = propType.data( 'id' );
							
							if ( rangeID )
								schemaType.addRangeType( rangeID, node );
						}
						break;
						
					case 'remove':
						self.remove( null, true );
						break;
				}
				
				self.editor.windowManager.close( self.window.id );
			});
			
			//render property ranges
			this.renderRanges( list );
		};
		
		/**
		 * Renders a property's range in the button iframe
		 */
		this.renderRanges = function( list ){
			var propID = $( 'option:selected', list ).data( 'id' );
			var typeID = list.data( 'id' );
			var list = $( '#prop_type select', this.iframe );
			var selected = '';
			
			list.html( '' );
			
			if ( !( ranges = this.types[typeID]['properties'][propID]['ranges'] ).length ) return;
			
			for ( id in ranges ){
				var select = '';
				
				range = ranges[id];
				
				if ( range == 'Thing' ) continue;
				
				if ( !( type = schemaType.getTypeByName( range ) ) ) continue;
				
				if ( this.getNode().getAttribute( 'itemtype' ) == type['url'] )
					select = selected = ' selected = "selected"';
				
				var option = $( '<option' + select + '>' + type['label'] + '</option>' );
				option.data( { 'id' : type['id'] } );
				list.append( option );
			}
			
			if ( !list.html() )
			{
				$( this.iframeID )
					.css( { 'height' : '124px' } )
					.closest( '[role]' )
					.css( { 'height' : '153px' } );
				
				list.parent().hide();
				
				return;
			}
			
			if ( !selected )
				list.prepend( '<option selected="selected">Please select.....</option>' );
			
			$( this.iframeID )
				.css( { 'height' : '178px' } )
				.closest( '[role]' )
				.css( { 'height' : '207px' } );
			
			list.parent().show();
		};
		
		/**
		 * Returns the schema type node for a specific property node
		 * @returns HTMLElement|boolean
		 */
		this.getTypeNode = function( node ){
			node = ( node )? node : $( this.getNode() );
			
			if ( ( result = ( node ).closest( '[itemtype]' ) ) && result.length ) return $( result[0] );
			
			return false;
		};
		
		/**
		 * Adds a schema property
		 */
		this.add = function(){
			var selected = $( '#type_list option:selected', this.iframe );
			var id = selected.data( 'id' );
			var type = selected.data( 'type' );
			var prop = type['properties'][id];
			var content = this.selection.getContent();
			var node = this.getNode();
			
			if ( !$( node ).attr( 'itemtype' ) && ( $( node ).html() == content || $( node ).text() == content ) )
				node.setAttribute( 'itemprop', prop.name );
			else
			{
				this.selection.setContent( '<span itemprop="' + prop.name + '" id="' + id + '">' + content + '</span>' );
				node = $( '#' + id, node );
				node.removeAttr( 'id' );
			}
			
			this.bindTip( node );
			
			return node;
		};
		
		/**
		 * Removes a schema property
		 */
		this.remove = function( node, bugfix ){
			node = ( node )? $( node ) : $( this.getNode() );
			
			if ( !node.is( '[itemprop]' ) ) return;
			
			if ( node.is( '[itemtype]' ) )
			{
				//If <span itemtype=""></span>
				node.removeAttr( 'itemprop' );
				return;
			}
			
			this.editor.dom.remove( node[0], true );
			
			//Added this to fix a bug where the selection object is currupted - found when removing prop and adding prop again on same selected range!
			if ( bugfix )
				this.editor.setContent( this.editor.getContent() );
		};
		
		/**
		 * Removes all schema property recursively
		 */
		this.removeAll = function( node ){
			var self = this;
			
			$( node ).find( '[itemprop]' ).each( function(){
				self.remove( $( this ) );
			});
			
			this.remove( node );
		};
		
		/**
		 * Edits a schema property
		 */
		this.edit = function(){
			var selected = $( '#type_list option:selected', this.iframe );
			var id = selected.data( 'id' );
			var type = selected.data( 'type' );
			
			var prop = type['properties'][id];
			
			this.getNode().setAttribute( 'itemprop', prop.name );
		};
	};
	
	tinymce.create( 'tinymce.plugins.Schema', {
		/**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
		init: function( ed, url ){
			schema.editor = ed;
			schema.getTypes();
			
			schemaType.init( ed, url );
			schemaProp.init( ed, url );
			
			//Add css file 
			if ( ed.settings.content_css !== false )
				ed.contentCSS.push( url + '/css/content.css?v=1.02' );
			
			ed.onLoadContent.add( function( ed, cm ){
				schema.init( ed );
			});
		}
	});
	
	//Register plugin
	tinymce.PluginManager.add( 'schema', tinymce.plugins.Schema );
	
	schema = new Schema();
	schemaType = new SchemaType();
	schemaProp = new SchemaProp();
	
})( jQuery );