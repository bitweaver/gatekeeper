<?
	global $gGatekeeper;
	$gBitSmarty->assign( 'securities', $gGatekeeper->getSecurityList( $gBitUser->mUserId ) );
?>