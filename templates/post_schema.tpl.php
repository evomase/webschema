<table class="form-table">
	<tbody>
		<tr class="form-field">
			<th scope="row">
				<label>Type</label>
			</th>
			
			<td>
				<select name="web_schema[type]" id="post_schema_type_list">
					<option value="0">Please select....</option>
					
					<?php foreach( $schema as $key => $type ): ?>
						<option value="<?php echo $key?>" <?php if ( !empty( $postSchema['type'] ) && ( $postSchema['type'] == $key ) ) echo 'selected="selected"'?>>
							<?php echo $type['label']; ?>
						</option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
	</tbody>
</table>