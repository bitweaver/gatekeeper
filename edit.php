<?php
/**
 * @version $Header$
 * @package gatekeeper
 * @subpackage functions
 * @author spider <spider@steelsun.com>
 */

// +----------------------------------------------------------------------+
// | Copyright (c) 2004, bitweaver.org
// +----------------------------------------------------------------------+
// | All Rights Reserved. See below for details and a complete list of authors.
// | Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
// |
// | For comments, please use phpdocu.sourceforge.net documentation standards!!!
// | -> see http://phpdocu.sourceforge.net/
// +----------------------------------------------------------------------+
// | Authors: spider <spider@steelsun.com>
// +----------------------------------------------------------------------+
//
// $Id$

/**
 * required setup
 */
require_once( '../kernel/includes/setup_inc.php' );

$gBitSystem->verifyPackage( 'gatekeeper' );
$gBitSystem->verifyPermission( 'p_gatekeeper_create' );

require_once( GATEKEEPER_PKG_CLASS_PATH.'LibertyGatekeeper.php' );

if( !empty( $_REQUEST['security_id']) ) {
	$secList = $gGatekeeper->getSecurityList( $gBitUser->mUserId, $_REQUEST['security_id'] );
	$sec = array_pop( $secList );
}

if( !empty( $_REQUEST['cancelsecurity'] ) ) {
	header( 'Location: '.GATEKEEPER_PKG_URL );
	die;
}
elseif( !empty( $sec ) ||
        !empty( $_REQUEST['savesecurity'] ) ||
        !empty( $_REQUEST['newsecurity'] ) ) {
	// we can not get in here unless we own the $security

	if( !empty( $_REQUEST['savesecurity'] ) ) {
		if( $gGatekeeper->storeSecurity( $_REQUEST ) ) {
			header( 'Location: '.GATEKEEPER_PKG_URL );
		} else {
			$gBitSmarty->assignByRef( 'errors', $gGatekeeper->mErrors );
			$_REQUEST['selected'] = $_REQUEST['access_level'];
			$gBitSmarty->assignByRef( 'security', $_REQUEST );
		}
	} elseif( !empty( $_REQUEST['deletesecurity'] ) ) {
		if( empty( $_REQUEST['confirm'] ) ) {
			$formHash['deletesecurity'] = $_REQUEST['deletesecurity'];
			$formHash['security_id'] = $_REQUEST['security_id'];
			$gBitSystem->confirmDialog( $formHash, 
				array( 
					'warning' => tra('Are you sure you want to delete this security list?') . ' ' . $sec['security_description']
				)
			);
		} elseif( $gGatekeeper->expungeSecurity( $sec['security_id'] ) ) {
			header( 'Location: '.GATEKEEPER_PKG_URL );
			die;
		}
	}
	if (empty( $_REQUEST['newsecurity'] ) && empty( $_REQUEST['savesecurity'] ) ) {
		if ($sec['access_answer']) {
			$sec['is_protected'] = 'y';
			$sec['selected'] = 'protected';
		} elseif( $sec['is_private'] == 'y' ) {
			$sec['selected'] = 'private';
		} elseif( $sec['is_hidden'] == 'y' ) {
			$sec['selected'] = 'hidden';
		}
	}

	$gBitSmarty->assignByRef( 'security', $sec );
	$gBitSystem->display( 'bitpackage:gatekeeper/edit_security.tpl', 'Edit Security' , array( 'display_mode' => 'edit' ));
} else {
	header( 'Location: '.GATEKEEPER_PKG_URL );
	die;
}

?>
