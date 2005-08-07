<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_gatekeeper/LibertyGatekeeper.php,v 1.1.1.1.2.7 2005/08/07 16:23:24 lsces Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: LibertyGatekeeper.php,v 1.1.1.1.2.7 2005/08/07 16:23:24 lsces Exp $
 * @package gatekeeper
 */

/**
 * required setup
 */
require_once( LIBERTY_PKG_PATH.'LibertyBase.php' );

/**
 * Gatekeeper class to illustrate best practices when creating a new bitweaver package that
 * builds on core bitweaver functionality, such as the Liberty CMS engine
 *
 * @package gatekeeper
 * @subpackage LibertyGatekeeper
 *
 * created 2004/8/15
 *
 * @author spider <spider@steelsun.com>
 *
 * @version $Revision: 1.1.1.1.2.7 $ $Date: 2005/08/07 16:23:24 $ $Author: lsces $
 */
class LibertyGatekeeper extends LibertyBase {
    /**
    * During initialisation, be sure to call our base constructors
	**/
	function LibertyGatekeeper( $pContentId=NULL ) {
		$this->mContentId = $pContentId;
		LibertyBase::LibertyBase();
	}

	function isValid() {
		return( is_numeric( $this->mContentId ) );
	}

	function verifySecurity( &$pParamHash ) {
		if( ($pParamHash['security_id'] != 'public') && !empty( $pParamHash['access_level'] ) ) {
			// if we have an access level, we know we are trying to save/update,
			// else perhaps we are just assigning security_id to content_id
			if( empty( $pParamHash['security_description'] ) && (empty( $pParamHash['security_id'] ) || $pParamHash['security_id'] == 'new' ) ) {
				$this->mErrors['security'] = tra( "You must enter a security description." );
			} elseif( !empty( $pParamHash['security_description'] ) ) {
				// we need to load the existing security_id to verify we user owns the security_id & if anything has changed
				$pParamHash['security_store']['security_description'] = substr( $pParamHash['security_description'], 0, 160 );
			}
			if( !empty( $pParamHash['access_level'] ) ) {
				$pParamHash['security_store']['is_hidden'] = ($pParamHash['access_level'] == 'hidden' ? 'y' : NULL);
				$pParamHash['security_store']['is_private'] = ($pParamHash['access_level'] == 'private' ? 'y' : NULL);
				// If we have an answer, store the question.
				if( $pParamHash['access_level'] == 'protected' && empty( $pParamHash['access_answer'] ) ) {
					$this->mErrors['security'] = tra( "You must enter an answer for your security question." );
				} else {
					$pParamHash['security_store']['access_question'] = !empty( $pParamHash['access_answer'] ) ? $pParamHash['access_question'] : NULL;
					$pParamHash['security_store']['access_answer'] = !empty( $pParamHash['access_answer'] ) ? trim( $pParamHash['access_answer'] ) : NULL;
				}
	//				$pParamHash['security_store']['group_id'] = !empty( $pParamHash['access_group_id'] ) ? $pParamHash['access_group_id'] : NULL;
			}
		}
		return( count( $this->mErrors ) == 0 );
	}

	function storeSecurity( &$pParamHash ) {
		if( !empty( $pParamHash['content_id'] ) ) {
			// We'll first nuke any security mappings for this content_id
			$sql = "DELETE FROM `".BIT_DB_PREFIX."tiki_content_security_map`
					WHERE `content_id` = ?";
			$rs = $this->mDb->query( $sql, array( $pParamHash['content_id'] ) );
		}
		if( !empty( $pParamHash['access_level'] ) || (!empty( $pParamHash['security_id'] ) && $pParamHash['security_id'] != 'public') ) {
			if( $this->verifySecurity( $pParamHash ) && !empty( $pParamHash['security_store'] ) ) {
				trim_array( $pParamHash );
				global $gBitUser;
				$table = BIT_DB_PREFIX."tiki_security";
				if( empty( $pParamHash['security_id'] ) || !is_numeric( $pParamHash['security_id'] ) ) {
					$pParamHash['security_store']['user_id'] = $gBitUser->mUserId;
					$pParamHash['security_id'] = $this->mDb->GenID( 'tiki_security_id_seq' );
					$pParamHash['security_store']['security_id'] = $pParamHash['security_id'];
					$result = $this->mDb->associateInsert( $table, $pParamHash['security_store'] );
				} else {
					$secId = array ( "name" => "security_id", "value" => $pParamHash['security_id'] );
					$result = $this->mDb->associateUpdate( $table, $pParamHash['security_store'], $secId );
				}
			}

			if( @$this->verifyId( $pParamHash['content_id'] ) && @$this->verifyId( $pParamHash['security_id'] ) ) {
				$sql = "INSERT INTO `".BIT_DB_PREFIX."tiki_content_security_map` ( `content_id`, `security_id` ) VALUES (?,?)";
				$rs = $this->mDb->query( $sql, array( $pParamHash['content_id'], $pParamHash['security_id'] ) );
			}
		}
		return( count( $this->mErrors ) == 0 );
	}

	function getSecurityList( $pUserId=NULL, $pSecurityId=NULL ) {
		if( empty( $pUserId ) ) {
			global $gBitUser;
			$pUserId = $gBitUser->mUserId;
		}
		$whereSql = NULL;
		$bindVars = array( $pUserId );
		if( !empty( $pSecurityId ) ) {
			$whereSql = ' AND `security_id`=? ';
			array_push( $bindVars, $pSecurityId );
		}

		$query = "SELECT `security_id` AS `hash_id`, `security_id`, `user_id`, `security_description`, `is_private`, `is_hidden`, `access_question`, `access_answer` FROM `".BIT_DB_PREFIX."tiki_security` WHERE `user_id`=? $whereSql";
		return $this->mDb->getAssoc( $query, $bindVars );
	}

	// guaranteeing pSecurityId is owned by someone else better happen upstream!
	function expungeSecurity( $pSecurityId ) {
		$ret = FALSE;
		if( !empty( $pSecurityId ) && is_numeric( $pSecurityId ) ) {
			$this->mDb->StartTrans();

			$sql = "DELETE FROM `".BIT_DB_PREFIX."tiki_content_security_map` WHERE security_id=?";
			$rs = $this->mDb->query( $sql, array( $pSecurityId ) );

			$sql = "DELETE FROM `".BIT_DB_PREFIX."tiki_security` WHERE security_id=?";
			$rs = $this->mDb->query( $sql, array( $pSecurityId ) );

			$this->mDb->CompleteTrans();
			$ret = TRUE;
		}
		return $ret;
	}
}

global $gGatekeeper;
$gGatekeeper = new LibertyGatekeeper();

?>
