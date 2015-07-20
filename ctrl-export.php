<?php

/**
 * @wordpress plug-in
 * Plugin Name: Ctrl export
 * Plugin URI: http://www.willemdumee.nl
 * Description: Exports theme settings to child theme
 * Version 0.1.0
 * Author: Willem Dumee
 * Author URI: http://www.willemdumee.nl
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Copyright 2015 willemdumee (email : willemdumee AT gmail DOT com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
Class Ctrl_Export
{

    public function __construct()
    {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_notices', array( $this, 'admin_notice' ) );
        add_action( 'admin_menu', array( $this, 'settings_menu' ) );
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'wp_ajax_my_action', array( $this, 'my_action_callback' ) );

    }

    public function settings_menu()
    {
        add_menu_page( 'Export management', 'CTRL Export', 'manage_options', 'ctrl-export/views/ctrl-export-page.php',
            '' );
    }

    // Register functions to be called when bugs are saved
    public function admin_init()
    {
        add_action( 'admin_post_save_ctrl_export',
            array( $this, 'save_export' ) );
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script(
            'ctrl-themeexport-admin',
            plugins_url( 'ctrl-export/assets/js/admin.js' ),
            array( 'jquery' ),
            '0.1.0'
        );
    }

    public function enqueue_styles()
    {
        wp_enqueue_style(
            'ctrl-themeexport-admin',
            plugins_url( 'ctrl-export/assets/css/admin.css' ),
            '0.1.0'
        );
    }

    public function admin_notice()
    {
        if (( isset( $_GET['message'] ) ) && ( $_GET['message'] == 'exportsaved' ))  : ?>
            <div class="updated">
                <p><?php _e( 'Theme export is saved', 'ctrlexport-domain' ); ?></p>
            </div>
        <?php endif;
    }

    public function save_export()
    {
        // Check if user has proper security level
        if ( ! current_user_can( 'manage_options' )) {
            wp_die( 'Not allowed' );
        }

        // Check if nonce field is present for security
        check_admin_referer( 'ctrl_export_save' );
        $ctrl_theme_options_parent = (array) get_option( $_POST['parent-theme'] );

        if ($_POST['override_values'] != 'override') {
            $ctrl_theme_options = (array) get_option( $_POST['theme-mods-optionname'] );
            foreach ($ctrl_theme_options_parent as $setting => $val) {
                $ctrl_theme_options[$setting] = $val;
            }
            update_option( $_POST['theme-mods-optionname'], $ctrl_theme_options );
        } else {
            update_option( $_POST['theme-mods-optionname'], $ctrl_theme_options_parent );
        }


        // Redirect the page to the admin form
        wp_redirect( add_query_arg( array(
            'page'             => 'ctrl-export/views/ctrl-export-page.php',
            'message'          => 'exportsaved',
            'theme-mods'       => $_POST['theme-mods-value'],
            'theme-mods-value' => $_POST['theme-mods-optionname']
        ), admin_url( 'admin.php' ) ) );
        exit;
    }

    public function my_action_callback()
    {
        $count = 0;
        $options = get_option( $_POST['theme'] );
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


        wp_die(); // this is required to terminate immediately and return a proper response
    }
}

$ctrl_export = new Ctrl_Export();

