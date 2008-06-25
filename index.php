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
// $Id: index.php,v 1.3 2008/06/25 22:21:10 spiderr Exp $

require_once( '../bit_setup_inc.php' );

$gBitSystem->verifyPackage( 'gatekeeper' );

require_once( GATEKEEPER_PKG_PATH.'LibertyGatekeeper.php' );

$lists = $gGatekeeper->getSecurityList();
$gBitSmarty->assign_by_ref( 'securities', $lists );

$gBitSystem->display( 'bitpackage:gatekeeper/list_security.tpl', 'Security Lists' , array( 'display_mode' => 'display' ));

?>
