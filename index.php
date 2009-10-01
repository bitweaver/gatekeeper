<?php
// +----------------------------------------------------------------------+
// | Copyright (c) 2004, bitweaver.org
// +----------------------------------------------------------------------+
// | All Rights Reserved. See copyright.txt for details and a complete list of authors.
// | Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
// |
// | For comments, please use phpdocu.sourceforge.net documentation standards!!!
// | -> see http://phpdocu.sourceforge.net/
// +----------------------------------------------------------------------+
// | Authors: spider <spider@steelsun.com>
// +----------------------------------------------------------------------+
//
// $Id: index.php,v 1.5 2009/10/01 13:45:36 wjames5 Exp $

require_once( '../bit_setup_inc.php' );

$gBitSystem->verifyPackage( 'gatekeeper' );
$gBitSystem->verifyPermission( 'p_gatekeeper_create' );

require_once( GATEKEEPER_PKG_PATH.'LibertyGatekeeper.php' );

$lists = $gGatekeeper->getSecurityList();
$gBitSmarty->assign_by_ref( 'securities', $lists );

$gBitSystem->display( 'bitpackage:gatekeeper/list_security.tpl', 'Security Lists' , array( 'display_mode' => 'display' ));

?>
