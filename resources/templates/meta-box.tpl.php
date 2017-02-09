<div id="fields">
    <fieldset>
        <p>In this section, you can add more information can be generated on the page which could be used by services
            like Google AMP/Structured Data</p>

        <p class="post-attributes-label-wrapper">
            <label for="web-schema-data-type" class="post-attributes-label">Data Type</label>
        </p>

        <select name="web-schema[data-type]" id="web-schema-data-type">
            <option>Please select</option>

            <?php foreach ($types as $id => $type): ?>
                <option value="<?php print $id; ?>" <?php print ($data['data-type'] == $id) ? 'selected="selected"' : ''; ?>>
                    <?php print $type['label']; ?>
                </option>
            <?php endforeach ?>
        </select>
    </fieldset>
</div>

<div id="preview">
    <pre>
    </pre>
</div>