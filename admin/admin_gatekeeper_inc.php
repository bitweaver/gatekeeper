<?php
// $Header: /cvsroot/bitweaver/_bit_gatekeeper/admin/admin_gatekeeper_inc.php,v 1.1 2005/06/19 04:48:53 bitweaver Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
if (isset($_REQUEST["gatekeeperset"]) && isset($_REQUEST["homeSample"])) {
	$gBitSystem->storePreference("home_gatekeeper", $_REQUEST["homeSample"]);
	$smarty->assign('home_gatekeeper', $_REQUEST["homeSample"]);
}

require_once( GATEKEEPER_PKG_PATH.'LibertyGatekeeper.php' );


$gGatekeeper = new LibertyGatekeeper( !empty( $_REQUEST['gatekeeper_id'] ) ? $_REQUEST['gatekeeper_id'] : NULL );

if( !empty( $_REQUEST['savegatekeeper'] ) ) {
	if( $gGatekeeper->store( $_REQUEST ) ) {
		header( 'Location: '.KERNEL_PKG_URL.'admin/index.php?page=gatekeeper' );
		die;
	} else {
		$saveError = TRUE;
		$smarty->assign_by_ref( 'gatekeeperErrors', $gGatekeeper->mErrors );
	}
} elseif( !empty( $_REQUEST['assigngatekeeper'] ) ) {
	foreach( array_keys( $_REQUEST ) as $key ) {
		if( preg_match( '/^gatekeeper_group_([-0-9]*)/', $key, $match ) ) {
			$groupId = $match[1];
			$gGatekeeper->assignGatekeeperToGroup( $_REQUEST[$key], $groupId );
//vd( $match );
		}
	}
}
// $gGatekeeper->load();
if( $gGatekeeper->isValid() || isset( $_REQUEST['newgatekeeper'] ) || !empty( $saveError ) ) {
	$smarty->assign_by_ref('gGatekeeper', $gGatekeeper);
} else {
	$gatekeepers = $gGatekeeper->getList();
	$systemGroups = $gGatekeeper->getGatekeeperGroups();
	$smarty->assign_by_ref('systemGroups', $systemGroups );
foreach( array_keys( $systemGroups ) as $groupId ) {
	$groupGatekeeper[$groupId] = $gGatekeeper->getGatekeeperMenu( 'gatekeeper_group_'.$groupId, $systemGroups[$groupId]['gatekeeper_id'] );
}
	$smarty->assign_by_ref('groupGatekeeper', $groupGatekeeper );
	$smarty->assign_by_ref('gatekeeperList', $gatekeepers);
}

?>
