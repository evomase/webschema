<div class="wrap">
    <h1>Web Schema Settings</h1>

    <form action="options.php" method="post">
        <?php
        settings_fields(\WebSchema\Models\WP\Settings::NAME);
        do_settings_sections(\WebSchema\Models\WP\Settings::PAGE);
        submit_button(); ?>
    </form>
</div>