<?php
// +----------------------------------------------------------------------+
// | Copyright (c) 2004, bitweaver.org
// +----------------------------------------------------------------------+
// | All Rights Reserved. See copyright.txt for details and a complete list of authors.
// | Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
// |
// | For comments, please use phpdocu.sourceforge.net documentation standards!!!
// | -> see http://phpdocu.sourceforge.net/
// +----------------------------------------------------------------------+
// | Authors: spider <spider@steelsun.com>
// +----------------------------------------------------------------------+
//
// $Id: index.php,v 1.1 2005/06/19 04:48:53 bitweaver Exp $

require_once( '../bit_setup_inc.php' );

$gBitSystem->verifyPackage( 'gatekeeper' );

require_once( GATEKEEPER_PKG_PATH.'LibertyGatekeeper.php' );

$lists = $gGatekeeper->getSecurityList();
$smarty->assign_by_ref( 'securities', $lists );

$gBitSystem->display( 'bitpackage:gatekeeper/list_security.tpl', 'Upload Images' );

?>
