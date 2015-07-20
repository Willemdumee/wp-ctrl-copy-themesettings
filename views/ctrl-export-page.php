<?php
/**
 * Created by PhpStorm.
 * User: CTRL
 * Date: 05-06-15
 * Time: 15:43
 */

$current_theme      = wp_get_theme();
$current_theme_name = $current_theme->name;
$template           = $current_theme->get_stylesheet();
$themes             = wp_get_themes();
$themes_array       = array();

foreach ($themes as $theme) {
    if ($current_theme_name == $theme->parent()) {
        $themes_array[] = $theme;
    }
};


?><!-- Top-level menu -->
<div id="ctrl-general" class="wrap">
    <h2>CTRL copy theme settings</h2>

    <p>You can copy the current theme options to a desired child theme.</p>

    <h3>Current theme: <?php echo $current_theme_name; ?></h3>

    <p>
        Current theme options:</p>
    <?php if (get_option( 'theme_mods_' . $template ) !== false) {

        $count = 0;
        $options = get_option( 'theme_mods_' . $template );
        echo '<ul>';
        foreach ($options as $key => $value) {
            if (is_object( $value )) {
                echo '<li>' . $key . '<ul>';
                foreach ($value as $key => $value) {
                    echo '<li>' . $key . ': ' . $value . '</li>';
                }
                echo '</ul></li>';
                $count++;
            } elseif (( $key == 'sidebars_widgets' ) || ( $key == '0' )) {
                continue;
            } elseif (is_array( $value )) {
                echo '<li>' . $key . '<ul>';
                foreach ($value as $key => $value) {
                    echo '<li>' . $key . ': ' . $value . '</li>';
                }
                echo '</ul></li>';
                $count++;
            } else {
                echo '<li>' . $key . ': ' . $value . '</li>';
                $count++;
            }
        }
        if ($count == 0 ) {
            echo "<li>This theme doesn't have custom options set</li>";
        }
        echo '</ul>';
    }
    ?>

    <h3>Children</h3>
    <?php if ( ! empty( $themes_array )) { ?>
        <form id="select-child" method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
            <input type="hidden" name="action" value="save_ctrl_export"/>
            <?php wp_nonce_field( 'ctrl_export_save' ); ?>
            <input type="hidden" id="parent-theme" name="parent-theme" value="theme_mods_<?php echo  $template; ?>" />

            <select name="" id="themeselect" required>
                <option value="">-- select theme --</option>

                <?php
                foreach ($themes as $theme) {
                    if ($current_theme_name == $theme->parent()) {
                        echo '<option value="' . $theme->get_stylesheet() . '">' . $theme->name . '</option>';
                    }
                }; ?>
            </select>
            <input type="checkbox" id="override_values" name="override_values" value="override"/><label
                for="override_values">Clear all options in Child theme that don't match</label>
            <input type="hidden" id="theme-mods-optionname" name="theme-mods-optionname" value=""/>
            <input type="submit" id="ctrl_export_settings_submit" name="ctrl_export_settings_submit"
                   value="update theme options"
                   class="button-primary"
                   title="update the selected child theme with the options of the current theme"/>
        </form>
        <p class="childtheme-options"></p>
    <?php } else { ?>
        <p>There are no children of this theme</p>
    <?php } ?>
</div>


<?php

?>