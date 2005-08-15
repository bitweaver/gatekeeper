<?php
global $gBitSystem, $gBitSmarty;
$gBitSystem->registerPackage( 'gatekeeper', dirname( __FILE__).'/' );

require_once( GATEKEEPER_PKG_PATH.'LibertyGatekeeper.php' );

if( $gBitSystem->isPackageActive( 'gatekeeper' ) ) {
	$gLibertySystem->registerService( LIBERTY_SERVICE_ACCESS_CONTROL, GATEKEEPER_PKG_NAME, array(
		'content_display_function' => 'gatekeeper_content_display',
		'content_edit_function' => 'gatekeeper_content_edit',
		'content_store_function' => 'gatekeeper_content_store',
		'content_load_function' => 'gatekeeper_content_load',
		'content_verify_access' => 'gatekeeper_content_verify_access',
		'content_edit_tpl' => 'bitpackage:gatekeeper/choose_security.tpl',
		'content_view_tpl' => 'bitpackage:gatekeeper/gatekeeper_content_display.tpl',
	) );
}

?>
