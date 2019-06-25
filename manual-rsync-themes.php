<?php
/**
 * Crafted with love by Fantasktic Team.
 * User: Alex
 * Date: 25-Jun-19
 * Time: 05:38
 */

// Run a manual rsync of theme files
exec('rsync -avP -e "ssh -i wpe_magtap.key" ~/sites/magtap2/themes_updated.txt magtap3@magtap3.ssh.wpengine.net:~/sites/magtap3/');