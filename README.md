# Web Schema v0.9b 

Web Schema is an open source tool created to make it easier for webmasters to markup their content with a collection of [schemas](http://schema.org/).

The tool has been created as a plugin for TinyMCE but will look into supporting other WYSIWYG editors in the future.

## Requirements
*	TinyMCE
*	jQuery
*	PHP
*	MYSQL
*	Wordpress

## Installation

At the moment, the tool is only available for Wordpress but I'll be working on a Drupal version very soon.

### Wordpress

All you have to do is pull the source code and add it to the plugin directory. Once that's done the plugin needs to be activated in the plugin admin interface.

Once the plugin has been activated, the plugin configuration page can be accessed via the ``Settings`` menu. The schemas needs to be upload and stored in the database, to do that click on the
``Update Records`` button.

##	How to use
The tool can be used on any page that contains a TinyMCE editor.

Once a selection is made in the editor, the schema type and property buttons will be active on the toolbar. To add a type, just click on the button and select the type. The same process 
goes for adding a property. A property can only be added within a selection where a type has aleady been created.


### Highlighting.
To make it easier to identifiy each markup, all markup content are highlighted and a tooltip is shown when the mouse moves over the highlighted content. Below is a list of colors used
to highlight each markup.

*	Type - <font color="#FF0033">Red</font>
*	Property - <font color="#335CFF">Blue</font>
*	Nested Type Property - <font color="#C94AC9">Purple</font>
