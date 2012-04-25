<html>
	<head>
		<link href="<?php echo plugins_url( 'webschema' ); ?>/files/css/reset.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo site_url(); ?>/wp-admin/load-styles.php?c=1&dir=ltr&load=global,wp-admin" rel="stylesheet" type="text/css" />
		<link href="<?php echo site_url(); ?>/wp-admin/css/colors-fresh.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo plugins_url( 'webschema' ); ?>/files/css/schema.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
	</head>
	
	<body>
		<div id="schema_html">
			<form>
				<div>
					<label>Select a type:</label>
					
					<select id="type_list">
					</select>
				</div>
				
				<div id="buttons">
					<button class="button-primary" id="add">Add</button>
					<button class="button-primary" id="remove">Remove</button>
				</div>
			</form>
		</div>
	</body>
</html>
