<div id="web_schema">
	<h2>Web Schema</h2>
	
	<form method="post" action="">
		<table class="form-table">
			<tbody>
				<tr class="form-field">
					<th scope="row">
						<label for="schema">Schema URL</label>
					</th>
					
					<td>
						<input id="schema" type="text" class="regular-text" name="web_schema[schema_json_url]" value="<?php echo $schema['schema_json_url']; ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		
		<p class="submit">
			<input class="button-primary" type="submit" value="Submit" name="submit" />
			<input class="button-primary" type="submit" value="Update Records" name="update_records" />
			<input class="button-primary" type="submit" value="Truncate Records" name="truncate_records" />
		</p>
	</form>
</div>