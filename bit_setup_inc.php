<?php
global $gBitSystem, $gBitSmarty;
$gBitSystem->registerPackage( 'gatekeeper', dirname( __FILE__).'/' );

require_once( GATEKEEPER_PKG_PATH.'LibertyGatekeeper.php' );

if( $gBitSystem->isPackageActive( 'gatekeeper' ) ) {
	$gLibertySystem->registerService( LIBERTY_SERVICE_ACCESS_CONTROL, GATEKEEPER_PKG_NAME, array(
		'edit_choose_tpl' => 'bitpackage:gatekeeper/choose_security.tpl',
		'edit_choose_php' => GATEKEEPER_PKG_PATH.'edit_choose_inc.php',
	) );
}

?>
