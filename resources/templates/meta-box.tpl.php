<fieldset>
    <label for="web-schema-root-schema">Root Schema</label>

    <select name="web-schema[root-schema]" id="web-schema-root-schema">
        <option>Please select</option>

        <?php foreach ($schemas as $key => $schema): ?>
            <option value="<?php print $key; ?>" <?php print ($data['root-schema'] == $key) ? 'selected="selected"' : ''; ?>>
                <?php print $schema['label']; ?>
            </option>
        <?php endforeach ?>
    </select>
</fieldset>