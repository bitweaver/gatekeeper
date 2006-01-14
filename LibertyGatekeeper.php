<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_gatekeeper/LibertyGatekeeper.php,v 1.1.1.1.2.19 2006/01/14 16:44:28 spiderr Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: LibertyGatekeeper.php,v 1.1.1.1.2.19 2006/01/14 16:44:28 spiderr Exp $
 * @package gatekeeper
 */

/**
 * required setup
 */
require_once( LIBERTY_PKG_PATH.'LibertyBase.php' );
require_once( USERS_PKG_PATH.'bookmark_lib.php' );

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
 * @version $Revision: 1.1.1.1.2.19 $ $Date: 2006/01/14 16:44:28 $ $Author: spiderr $
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
		return( $this->verifyId( $this->mContentId ) );
	}

	function verifySecurity( &$pParamHash ) {
		if( ($pParamHash['security_id'] != 'public') && !empty( $pParamHash['access_level'] ) ) {
			// if we have an access level, we know we are trying to save/update,
			// else perhaps we are just assigning security_id to content_id
			if( empty( $pParamHash['security_description'] ) && ( empty( $pParamHash['security_id'] ) || $pParamHash['security_id'] == 'new' ) ) {
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
		if( @$this->verifyId( $pParamHash['content_id'] ) ) {
			// We'll first nuke any security mappings for this content_id
			$sql = "DELETE FROM `".BIT_DB_PREFIX."tiki_content_security_map`
					WHERE `content_id` = ?";
			$rs = $this->mDb->query( $sql, array( $pParamHash['content_id'] ) );
		}
		if( @$this->verifyId( $pParamHash['access_level'] ) || ( @$this->verifyId( $pParamHash['security_id'] ) && $pParamHash['security_id'] != 'public') ) {
			if( $this->verifySecurity( $pParamHash ) && !empty( $pParamHash['security_store'] ) ) {
				trim_array( $pParamHash );
				if( !empty( $pParamHash['security_store'] ) ) {
					global $gBitUser;
					$table = BIT_DB_PREFIX."tiki_security";
					if( @$this->verifyId( $pParamHash['security_id'] ) ) {
						$pParamHash['security_store']['user_id'] = $gBitUser->mUserId;
						$pParamHash['security_id'] = $this->mDb->GenID( 'tiki_security_id_seq' );
						$pParamHash['security_store']['security_id'] = $pParamHash['security_id'];
						$result = $this->mDb->associateInsert( $table, $pParamHash['security_store'] );
					} else {
						$secId = array ( "name" => "security_id", "value" => $pParamHash['security_id'] );
						$result = $this->mDb->associateUpdate( $table, $pParamHash['security_store'], $secId );
					}
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
		if( @$this->verifyId( $pSecurityId ) ) {
			$whereSql = ' AND `security_id`=? ';
			array_push( $bindVars, $pSecurityId );
		}

		$query = "SELECT `security_id` AS `hash_id`, `security_id`, `user_id`, `security_description`, `is_private`, `is_hidden`, `access_question`, `access_answer` FROM `".BIT_DB_PREFIX."tiki_security` WHERE `user_id`=? $whereSql";
		return $this->mDb->getAssoc( $query, $bindVars );
	}

	// guaranteeing pSecurityId is owned by someone else better happen upstream!
	function expungeSecurity( $pSecurityId ) {
		$ret = FALSE;
		if( @$this->verifyId( $pSecurityId ) ) {
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

function gatekeeper_content_load() {
	$ret = array(
		'select_sql' => ' ,ts.`security_id` AS `has_access_control`, ts.`security_id`, ts.`security_description`, ts.`is_private`, ts.`is_hidden`, ts.`access_question`, ts.`access_answer`  ',
		'join_sql' => " LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_content_security_map` tcs ON ( tc.`content_id`=tcs.`content_id` )  LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_security` ts ON ( tcs.`security_id`=ts.`security_id` ) ",
	);
	return $ret;
}

function gatekeeper_content_store( &$pObject, &$pParamHash ) {
	global $gBitSystem, $gGatekeeper;
	$errors = NULL;
	// If a content access system is active, let's call it
	if( $gBitSystem->isPackageActive( 'gatekeeper' ) ) {
		if( !$gGatekeeper->storeSecurity( $pParamHash ) ) {
			$errors['security'] = $gGatekeeper->mErrors['security'];
		}
	}
	return( $errors );
}

function gatekeeper_content_display( &$pContent, &$pParamHash ) {
	global $gBitSystem, $gBitSmarty;
	$pContent->hasUserPermission( $pParamHash['perm_name'] );
}

function gatekeeper_content_verify_access( &$pContent, &$pHash ) {
	global $gBitUser, $gBitSystem;

if( !count( $pHash ) ) {
	$pHash = &$pContent->mInfo;
}

	$error = NULL;
	if( !$gBitUser->isRegistered() || !($pHash['user_id'] == $gBitUser->mUserId) ) {
		if( !($gBitUser->isAdmin()) ) {
			if( $pContent->mDb->isAdvancedPostgresEnabled() ) {
				global $gBitDb, $gBitSmarty;
				// This code makes use of the badass /usr/share/pgsql/contrib/tablefunc.sql
				// contribution that you have to install like: psql foo < /usr/share/pgsql/contrib/tablefunc.sql
				// This code pulls all branches for the current node and determines if there is a path from this content to the root
				// without hitting a security_id. If there is clear path it returns TRUE. If there is a security_id, then
				// it determines if the current user has permission
				$query = "SELECT branch,level,cb_item_content_id,cb_gallery_content_id,ts.*
						FROM connectby('`".BIT_DB_PREFIX."tiki_fisheye_gallery_image_map`', '`gallery_content_id`', '`item_content_id`', ?, 0, '/') AS t(`cb_gallery_content_id` int,`cb_item_content_id` int, `level` int, `branch` text)
							LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_content_security_map` tcsm ON (`cb_gallery_content_id`=tcsm.`content_id`)
							LEFT OUTER JOIN `".BIT_DB_PREFIX."tiki_security` ts ON (ts.`security_id`=tcsm.`security_id`)
						ORDER BY branch
						";
		$gBitDb->setFatalActive( FALSE );
				$tree = $pContent->mDb->getAssoc( $query, array( $pHash['content_id'] ) );
		$gBitDb->setFatalActive( TRUE );
				if( $tree ) {
					// we will assume true here since the prevention cases can repeatedly flag FALSE
					$lastLevel = -1;
					foreach( $tree AS $branch => $node ) {
						if( $node['level'] <= $lastLevel ) {
							// we have moved followed a branch to the end and there is no security!
							unset( $errorMessage );
							break;
						}
						if( $node['security_id'] ) {
							$ret = FALSE;
							if( $node['is_hidden'] ) {
								if( !empty( $pHash['no_fatal'] ) ) {
									// We are on a listing, so we should hide this with an empty error message
									$errorMessage = '';
								}
							}
							if( $node['is_private'] ) {
								if( !empty( $pHash['no_fatal'] ) ) {
									// We are on a listing, so we should hide this with an empty error message
									$errorMessage = '';
								} else {
									$errorMessage = tra( 'You cannot view this' ).' '.strtolower( tra( $pHash['content_type']['content_description'] ) );
								}
							}
							if( !empty( $node['access_answer'] ) ) {
								$pContent->mInfo = array_merge( $pHash, $node );
								if( $valError = gatekeeper_authenticate( $node, empty( $pHash['no_fatal'] ) ) ) {
									$errorMessage = $valError;
								}
							}
						}
						$lastLevel = $node['level'];
					}

					if( isset( $errorMessage ) ) {
						if( empty( $pHash['no_fatal'] ) ) {
							$gBitSystem->fatalError( $errorMessage );
						} else {
							$error['access_control'] = $errorMessage;
						}
					}

				} elseif( !empty( $gBitDb->mDb->_errorMsg ) ) {
					if( $gBitUser->isOwner() ) {
						$gBitSmarty->assign( 'feedback', array( 'warning' => $gBitDb->mDb->_errorMsg.'<br/>'.tra( 'Please check the galleries to which this '.$pHash['content_description'].' belongs' ) ) );
					}
				}
			} elseif( !empty( $pHash['security_id'] ) ) {
				// order matters here!
				if( $pHash['is_hidden'] == 'y' ) {
					$ret = TRUE;
				}
				if( $pHash['is_private'] == 'y' ) {
					$errorMessage = tra( 'You cannot view this' ).' '.strtolower( tra( $pHash['content_type']['content_description'] ) );
					if( empty( $pHash['no_fatal'] ) ) {
						$gBitSystem->fatalError( $errorMessage );
					} else {
						$error['access_control'] = $errorMessage;
					}
				}
				if( !empty( $pHash['access_answer'] ) ) {
					if( !($valError = gatekeeper_authenticate( $pHash, empty( $pHash['no_fatal'] ) ) ) ) {
						$error['access_control'] = $valError;
					}
				}
			}
		}
	}
	return $error;
}


function gatekeeper_authenticate( &$pInfo, $pFatalOnError = TRUE ) {
	global $gBitSystem, $gBitSmarty;
	$ret = FALSE;

	if( empty( $_SESSION['gatekeeper_security'][$pInfo['security_id']] ) || ( $_SESSION['gatekeeper_security'][$pInfo['security_id']] != md5( $pInfo['access_answer'] ) ) ) {
		if( !empty( $_REQUEST['try_access_answer'] ) && strtoupper( trim( $_REQUEST['try_access_answer'] ) ) == strtoupper( trim($pInfo['access_answer']) ) ) {
			// we have a successful password entry. Set the session so we don't ask for it again
			$_SESSION['gatekeeper_security'][$pInfo['security_id']] = md5( $pInfo['access_answer'] );
		} else {
			if( $pFatalOnError ) {
				$gBitSystem->display("bitpackage:gatekeeper/authenticate.tpl", "Password Required" );
				die;
			} else {
				$ret = '<h2>'.tra( "Password Required" ).'</h2>'.$gBitSmarty->fetch( "bitpackage:gatekeeper/authenticate.tpl" );
			}
		}
	}
	return $ret;
}




function gatekeeper_content_edit( &$pContent ) {
	global $gGatekeeper, $gBitUser, $gBitSmarty;
	$gBitSmarty->assign( 'securities', $gGatekeeper->getSecurityList( $gBitUser->mUserId ) );
}

global $gGatekeeper;
$gGatekeeper = new LibertyGatekeeper();

?>
