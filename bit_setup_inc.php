<?php
global $gBitSystem, $gBitSmarty, $gLibertySystem;

$registerHash = array(
	'package_name' => 'gatekeeper',
	'package_path' => dirname( __FILE__ ).'/',
	'service' => LIBERTY_SERVICE_ACCESS_CONTROL
);
$gBitSystem->registerPackage( $registerHash );

require_once( GATEKEEPER_PKG_PATH.'LibertyGatekeeper.php' );

if( $gBitSystem->isPackageActive( 'gatekeeper' ) ) {
	$gLibertySystem->registerService( LIBERTY_SERVICE_ACCESS_CONTROL, GATEKEEPER_PKG_NAME, array(
		'content_display_function' => 'gatekeeper_content_display',
		'content_edit_function' => 'gatekeeper_content_edit',
		'content_store_function' => 'gatekeeper_content_store',
		'content_expunge_function' => 'gatekeeper_content_expunge',
		'content_load_sql_function' => 'gatekeeper_content_load',
		'content_list_sql_function' => 'gatekeeper_content_list',
		'content_verify_access' => 'gatekeeper_content_verify_access',
		'content_edit_mini_tpl' => 'bitpackage:gatekeeper/choose_security.tpl',
		'content_icon_tpl' => 'bitpackage:gatekeeper/gatekeeper_service_icon.tpl',
	) );
}
?>
