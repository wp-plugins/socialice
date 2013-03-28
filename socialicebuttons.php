<?php
/*
Plugin Name: Socialice
Plugin URI: http://www.noveres.nl/category/wordpress/socialice/
Description: Socialbuttons that link to your pages on Facebook, Twitter and Google+.
Author: Noveres
Version: 0.2.0
Author URI: http://www.noveres.nl
License: GPL3
*/

/*  Copyright (C) 2013 Noveres

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
 
function socialice_install() {
    add_option( 'socialice_facebook', '', '', 'yes' );
    add_option( 'socialice_twitter', '', '', 'yes' );
    add_option( 'socialice_google', '', '', 'yes' );
    add_option( 'socialice_size', '', '', 'yes' );
    add_option( 'socialice_blank', '', '', 'yes');
    add_option('activation_redirect', true);
}
function plugin_redirect() {
    if (get_option('activation_redirect', false)) {
        delete_option('activation_redirect');
        $adminUrl = admin_url();
        wp_redirect($adminUrl.'/options-general.php?page=socialice-settings');
    }
}


register_activation_hook(__FILE__,'socialice_install');

add_action('admin_init', 'plugin_redirect');

function socialice_deactivate() {
    delete_option( 'socialice_facebook', '', '', 'yes' );
    delete_option( 'socialice_twitter', '', '', 'yes' );
    delete_option( 'socialice_google', '', '', 'yes' );
    delete_option( 'socialice_size', '', '', 'yes' );
    delete_option( 'socialice_blank', '', '', 'yes' );
}
register_deactivation_hook( __FILE__, 'socialice_deactivate' );

add_action('admin_menu', 'socialice_menu');

function socialice_menu() {
    add_options_page('Socialice options', 'Socialice', 'manage_options', 'socialice-settings', 'socialice_options');
}

function socialice_options() {
    if(!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    $opt_facebook = 'socialice_facebook';
    $opt_twitter = 'socialice_twitter';
    $opt_google = 'socialice_google';
    $opt_size = 'socialice_size';
    $opt_blank = 'socialice_blank';
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_facebook = 'socialice_facebook';
    $data_field_twitter = 'socialice_twitter';
    $data_field_google = 'socialice_google';
    $data_field_size = 'socialice_size';
    $data_field_blank = 'socialice_blank';

    $opt_val_facebook = get_option('socialice_facebook');
    $opt_val_twitter = get_option('socialice_twitter');
    $opt_val_google = get_option('socialice_google');
    $opt_val_size = get_option('socialice_size');
    $opt_val_blank = get_option('socialice_blank');

    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        $opt_val_facebook = $_POST[ $data_field_facebook ];
        $opt_val_twitter = $_POST[ $data_field_twitter ];
        $opt_val_google = $_POST[ $data_field_google ];
        $opt_val_size = $_POST[ $data_field_size ];
        $opt_val_blank = $_POST[ $data_field_blank ];

        $facebook_regex = '/^(http\:\/\/|https\:\/\/)?(?:www\.)?facebook\.com\/(?:(?:\w\.)*#!\/)?(?:pages\/)?(?:[\w\-\.]*\/)*([\w\-\.]*)/';
        if (preg_match($facebook_regex, $opt_val_facebook) || empty($opt_val_facebook)) {
            update_option( $opt_facebook, $opt_val_facebook );
        }
        else {
            $formerror= 'The URL you entered for Facebook is wrong, please review.';
        }
        update_option( $opt_twitter, $opt_val_twitter);
        update_option( $opt_google, $opt_val_google);
        update_option( $opt_size, $opt_val_size);
        update_option( $opt_blank, $opt_val_blank);
    }

    echo '<div class="wrap">';
    echo '<h2>'.__('Socialice settings', 'socialice-menu').'</h2>';
    ?>
    <form name="form1" method="post" action="">
        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

        <p><?php _e("Facebook url:", 'socialice-menu' ); ?>
            <input type="text" name="<?php echo $data_field_facebook; ?>" value="<?php echo $opt_val_facebook; ?>" size="20">
        </p>
        <p><?php _e("Twitter url:", 'socialice-menu' ); ?>
            <input type="text" name="<?php echo $data_field_twitter; ?>" value="<?php echo $opt_val_twitter; ?>" size="20">
        </p>
        <p><?php _e("Google+ url:", 'socialice-menu' ); ?>
            <input type="text" name="<?php echo $data_field_google; ?>" value="<?php echo $opt_val_google; ?>" size="20">
        </p>
        <p><?php _e("Size:", 'socialice-menu'); ?>
            <input type="radio" name="<?php echo $data_field_size; ?>" value="32" <?php if ($opt_val_size == '32') { echo 'checked'; } ?>>32px
            <input type="radio" name="<?php echo $data_field_size; ?>" value="64" <?php if ($opt_val_size == '64') { echo 'checked'; } ?>>64px
        </p>
        <p><?php _e("Open in new window?:", 'socialice-menu' ); ?>
            <input type="checkbox" name="<?php echo $data_field_blank; ?>" value="yes" <?php if ($opt_val_blank=='yes') { echo 'checked'; } ?>> Yes
        </p>
        <hr />
        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
        </p>
        <p class="error">
            <?php echo $formerror; ?>
        </p>
    </form>
    </div>

<?php
}

function your_widget_display($args) {
    extract($args);
    echo $before_widget;
    if (get_option('socialice_facebook')) {
        echo '<a href="'.get_option('socialice_facebook').'"';
        if (get_option('socialice_blank')=='yes') { echo 'target="_blank"'; }
        echo '><img src="'.plugins_url().'/'.plugin_basename(__DIR__).'/img/'.get_option('socialice_size').'px/facebook.png" class="socialice" /></a>';
    }
    if (get_option('socialice_twitter')) {
        echo '<a href="'.get_option('socialice_twitter').'"';
        if (get_option('socialice_blank')=='yes') { echo 'target="_blank"'; }
        echo '><img src="'.plugins_url().'/'.plugin_basename(__DIR__).'/img/'.get_option('socialice_size').'px/twitter.png" class="socialice" /></a>';
    }
    if (get_option('socialice_google')) {
        echo '<a href="'.get_option('socialice_google').'"';
        if (get_option('socialice_blank')=='yes') { echo 'target="_blank"'; }
        echo '><img src="'.plugins_url().'/'.plugin_basename(__DIR__).'/img/'.get_option('socialice_size').'px/google.png" class="socialice" /></a>';
    }
    ?>
    <style type="text/css">
        .socialice {
            margin-right: 5px;
        }
    </style>
    <?php
    echo $after_widget;
}

wp_register_sidebar_widget(
    'socialice_widget',
    'Socialice',
    'your_widget_display',
    array(
        'description' => 'Display socialice buttons in your sidebar.'
    )
);
?>
