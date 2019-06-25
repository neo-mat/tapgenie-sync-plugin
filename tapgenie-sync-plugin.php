<?php

/*
Plugin Name: Tapgenie Files Sync Plugin
Plugin URI: https://fantasktic.com
Description: Tapgenie core/plugins/themes sync between the installs
Version: 1.0
Author: Fantasktic Team
Author URI: https://fantasktic.com
License: GNU/GPL2
*/

register_activation_hook( __FILE__, 'tapgenie_create_sync_table' );

// DOC: https://codex.wordpress.org/Plugin_API/Action_Reference/upgrader_process_complete
add_action( 'upgrader_process_complete', 'mark_files_for_upgrade_on_update',10, 2);

function tapgenie_create_sync_table() {

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'sync_installs';

    $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		core smallint(5) NULL,
		plugins smallint(5) NULL,
		themes smallint(5) NULL,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

function mark_files_for_upgrade_on_update($upgrader_object, $options) {

	global $wpdb;
	$table_name = $wpdb->prefix . 'sync_installs';

    // Mark core to be updated
    if( $options['action'] == 'update' && $options['type'] == 'core') {
    	$wpdb->insert($table_name, array('core' => '1', 'plugins' => '0', 'themes' => '0'));
    }
    // Mark plugins to be updated
    if( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
	    $wpdb->insert($table_name, array('core' => '0', 'plugins' => '1', 'themes' => '0'));
    }
    // Mark themes to be updated
    if( $options['action'] == 'update' && $options['type'] == 'theme') {
	    $wpdb->insert($table_name, array('core' => '0', 'plugins' => '0', 'themes' => '1'));
    }
}

add_action( 'admin_menu', 'tapgenie_sync_files_menu' );

// Add menu option for the plugin, inside "Settings"
function tapgenie_sync_files_menu() {
	add_options_page( 'TapGenie Files Sync', 'TapGenie Files Sync', 'manage_options', 'tapgenie-sync-files', 'tapgenie_sync_files_options' );
}

// Create an admin page for the plugin, where we can perform manual synchronizations
function tapgenie_sync_files_options() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<p>In order to manually synchronize the files across the installs, for example, after editing theme files, please click the appropriate button below:</p>';
	echo '<p><input type="submit" name="sync_core" class="button-primary" id="sync_core_button" value="Sync WP Core across the installs" /></p>';
	echo '<p><input type="submit" name="sync_plugins" class="button-primary" id="sync_plugins_button" value="Sync plugin\'s files across the installs" /></p>';
	echo '<p><input type="submit" name="sync_themes" class="button-primary" id="sync_themes_button" value="Sync theme\'s files across the installs" /></p>';
	echo '<p id="response"></p>';
	echo '</div>';
}

// Enqueue our small AJAX js script to perform manual updates
add_action( 'admin_enqueue_scripts', 'tapgenie_sync_files_js_enqueue' );
function tapgenie_sync_files_js_enqueue() {
	if ( $_SERVER["REQUEST_URI"] == '/wp-admin/options-general.php?page=tapgenie-sync-files' ) {
		wp_enqueue_script( 'ajax-script', plugins_url( '/js/ajax.js', __FILE__ ), array( 'jquery' ) );
	}
}