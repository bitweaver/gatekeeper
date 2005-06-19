<?php
global $gBitSystem, $smarty;
$gBitSystem->registerPackage( 'gatekeeper', dirname( __FILE__).'/' );

require_once( GATEKEEPER_PKG_PATH.'LibertyGatekeeper.php' );
//if( $gBitSystem->isPackageActive( 'gatekeeper' ) ) {

	// Gallery is visible to everyone with bit_p_view_fisheye permissions
//	define( 'GATEKEEPER_ACCESS_PUBLIC', 100);
	// The content will no longer show up in lists or searches
//	define( 'GATEKEEPER_ACCESS_PROTECTED_HIDDEN' , 200);	
	// Users must correctly answer a question to be able to view gallery
//	define( 'GATEKEEPER_ACCESS_PROTECTED', 300);		
	// Completely private gallery. Viewable only by the creator and members of an optional assigned group
//	define( 'GATEKEEPER_ACCESS_PRIVATE', 500);			

//	$smarty->assign('GATEKEEPER_ACCESS_PUBLIC', GATEKEEPER_ACCESS_PUBLIC);
//	$smarty->assign('GATEKEEPER_ACCESS_PROTECTED', GATEKEEPER_ACCESS_PROTECTED);
//	$smarty->assign('GATEKEEPER_ACCESS_PROTECTED_HIDDEN', GATEKEEPER_ACCESS_PROTECTED_HIDDEN);
//	$smarty->assign('GATEKEEPER_ACCESS_PRIVATE', GATEKEEPER_ACCESS_PRIVATE);

//	define ( 'GATEKEEPER_DEFAULT_ACCESS_LEVEL', GATEKEEPER_ACCESS_PUBLIC);
//	define ( 'GATEKEEPER_DEFAULT_ACCESS_QUESTION', 'Enter the password to view this gallery');

?>
