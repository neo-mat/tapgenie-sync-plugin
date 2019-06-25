<?php
/**
 * Crafted with love by Fantasktic Team.
 * User: Alex
 * Date: 20-Jun-19
 * Time: 12:18
 */

require_once('../../../wp-config.php');

global $wpdb;
$table_name = $wpdb->prefix . 'sync_installs';

// Select what's marked for updating
$core = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE core = 1", OBJECT );
$plugins = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE plugins = 1", OBJECT );
$themes = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE themes = 1", OBJECT );


// Check if we got core/plugins/themes to update and rsync the files, deleting the SQL record from DB after syncing
if (count((array)$core)){
	//echo "Need to update core\n";
	// magtap.key is used to authenticate via SSH
	exec('rsync -avP -e "ssh -i wpe_magtap.key" ~/sites/magtap2/core_updated.txt magtap3@magtap3.ssh.wpengine.net:~/sites/magtap3/');
	$wpdb->delete($table_name, array('core' => 1) );
}

// Plugins
if (count((array)$plugins)){
	//echo "Need to update plugins\n";
	exec('rsync -avP -e "ssh -i wpe_magtap.key" ~/sites/magtap2/plugins_updated.txt magtap3@magtap3.ssh.wpengine.net:~/sites/magtap3/');
	$wpdb->delete($table_name, array('plugins' => 1) );
}

// Themes
if (count((array)$themes)){
	//echo "Need to update themes\n";
	exec('rsync -avP -e "ssh -i wpe_magtap.key" ~/sites/magtap2/themes_updated.txt magtap3@magtap3.ssh.wpengine.net:~/sites/magtap3/');
	$wpdb->delete($table_name, array('themes' => 1) );
}