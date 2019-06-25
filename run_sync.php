<?php
/**
 * Crafted with love by Fantasktic Team.
 * User: Alex
 * Date: 21-Jun-19
 * Time: 04:25
 */

/*
 * Really fat note here:
 * PHPSeclib doesn't work with WPEngine, key exchange doesn't happen
 * SSH2 extension doesn't work either, same issue with key exchange
 * That's why you see that class down below
 */

class ExecuteRemote
{
	private static $host;
	private static $username;
	//private static $password;
	//private static $key;
	private static $error;
	private static $output;

	public static function setup($host, $username=NULL)
	{
		self::$host = $host;
		self::$username = $username;
		//self::$password = $password;
		//self::$key = $key;
		// NOTE: Key is hardcoded down below in $cmd variable of executeScriptSSH() function
	}

	public static function executeScriptSSH($script)
	{
		// Setup connection string
		$connectionString = self::$host;
		$connectionString = (empty(self::$username) ? $connectionString : self::$username.'@'.$connectionString);

		// Execute script
		// NOTE: key is hardcoded down below in $cmd
		$cmd = "ssh -i wpe_magtap.key $connectionString $script 2>&1";
		self::$output['command'] = $cmd;
		exec($cmd, self::$output, self::$error);

		if (self::$error) {
			throw new Exception ("\nError sshing: ".print_r(self::$output, true));
		}

		return self::$output;
	}
}

// Hostname or IP of the main installs, where from we will execute php cli
$server = "magtap2.ssh.wpengine.net";
// Username to authenticate on SSH gateway with
$username = "magtap2";
// Authentication key, which has to be added in WPEngine User Portal
//$key = "wpe_magtap.key";

/* Example of setting connection up, followed by command execution
 *
 * ExecuteRemote::setup($server, $username);
 * ExecuteRemote::executeScriptSSH($command);
 *
 */

// Detect a type of sync and execute a manual rsync via SSH on WPEngine via RSync (duh)
// This applies only to GET requests sent via AJAX in wp-admin
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	switch ( $_SERVER['QUERY_STRING'] ) {
		case "sync_core":
			//$command = "php -f /sites/magtap2/wp-content/plugins/tapgenie-sync-plugin/manual-rsync-core.php";
			//ExecuteRemote::setup( $server, $username );
			//ExecuteRemote::executeScriptSSH( $command );
			echo "Manual WP Core Synchronization started and will be finished shortly.";
			break;
		case "sync_plugins":
			//$command = "php -f /sites/magtap2/wp-content/plugins/tapgenie-sync-plugin/manual-rsync-plugins.php";
			//ExecuteRemote::setup( $server, $username );
			//ExecuteRemote::executeScriptSSH( $command );
			echo "Manual WP Plugins Synchronization started and will be finished shortly.";
			break;
		case "sync_themes":
			//$command = "php -f /sites/magtap2/wp-content/plugins/tapgenie-sync-plugin/manual-rsync-themes.php";
			//ExecuteRemote::setup( $server, $username );
			//ExecuteRemote::executeScriptSSH( $command );
			echo "Manual WP Themes Synchronization started and will be finished shortly.";
			break;
		default:
			echo "Invalid type of sync";
			break;
	}
}
// That's where we perform a cron based execution of this script, to connect to the main install over SSH
// and run rsync-files.php on the install directly via PHP cli, which handles checking DB if any updates are pending
else {
	$command = "php -f /sites/magtap2/wp-content/plugins/tapgenie-sync-plugin/rsync-files.php";
	ExecuteRemote::setup( $server, $username );
	ExecuteRemote::executeScriptSSH( $command );
}