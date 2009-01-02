<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_gatekeeper/LibertyGatekeeper.php,v 1.29 2009/01/02 21:26:20 spiderr Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: LibertyGatekeeper.php,v 1.29 2009/01/02 21:26:20 spiderr Exp $
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
 *
 * created 2004/8/15
 *
 * @author spider <spider@steelsun.com>
 *
 * @version $Revision: 1.29 $ $Date: 2009/01/02 21:26:20 $ $Author: spiderr $
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
				// default name to security access level instead of throwing an error
				$pParamHash['security_store']['security_description'] = $pParamHash['access_level'];
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
			$sql = "DELETE FROM `".BIT_DB_PREFIX."gatekeeper_security_map`
					WHERE `content_id` = ?";
			$rs = $this->mDb->query( $sql, array( $pParamHash['content_id'] ) );
		}
		if( !empty( $pParamHash['access_level'] ) || ( @$this->verifyId( $pParamHash['security_id'] ) && $pParamHash['security_id'] != 'public') ) {
			if( $this->verifySecurity( $pParamHash ) && !empty( $pParamHash['security_store'] ) ) {
				trim_array( $pParamHash );
				if( !empty( $pParamHash['security_store'] ) ) {
					global $gBitUser;
					$table = BIT_DB_PREFIX."gatekeeper_security";
					if( !(@$this->verifyId( $pParamHash['security_id'] )) ) {
						$pParamHash['security_store']['user_id'] = $gBitUser->mUserId;
						$pParamHash['security_id'] = $this->mDb->GenID( 'gatekeeper_security_id_seq' );
						$pParamHash['security_store']['security_id'] = $pParamHash['security_id'];
						$result = $this->mDb->associateInsert( $table, $pParamHash['security_store'] );
					} else {
						$result = $this->mDb->associateUpdate( $table, $pParamHash['security_store'], array( "security_id" => $pParamHash['security_id']) );
					}
				}
			}

			if( @$this->verifyId( $pParamHash['content_id'] ) && @$this->verifyId( $pParamHash['security_id'] ) ) {
				$sql = "INSERT INTO `".BIT_DB_PREFIX."gatekeeper_security_map` ( `content_id`, `security_id` ) VALUES (?,?)";
				$rs = $this->mDb->query( $sql, array( $pParamHash['content_id'], $pParamHash['security_id'] ) );
			}
		}
		return( count( $this->mErrors ) == 0 );
	}

	function getSecurityList( $pUserId=NULL, $pSecurityId=NULL, $pSecurityDesc=NULL ) {
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

		if( $pSecurityDesc ) {
			$whereSql .= ' AND `security_description`=? ';
			array_push( $bindVars, $pSecurityDesc );
		}

		$query = "SELECT `security_id` AS `hash_id`, `security_id`, `user_id`, `security_description`, `is_private`, `is_hidden`, `access_question`, `access_answer` FROM `".BIT_DB_PREFIX."gatekeeper_security` WHERE `user_id`=? $whereSql";
		return $this->mDb->getAssoc( $query, $bindVars );
	}

	// guaranteeing pSecurityId is owned by someone else better happen upstream!
	function expungeSecurity( $pSecurityId ) {
		$ret = FALSE;
		if( @$this->verifyId( $pSecurityId ) ) {
			$this->mDb->StartTrans();

			$sql = "DELETE FROM `".BIT_DB_PREFIX."gatekeeper_security_map` WHERE security_id=?";
			$rs = $this->mDb->query( $sql, array( $pSecurityId ) );

			$sql = "DELETE FROM `".BIT_DB_PREFIX."gatekeeper_security` WHERE security_id=?";
			$rs = $this->mDb->query( $sql, array( $pSecurityId ) );

			$this->mDb->CompleteTrans();
			$ret = TRUE;
		}
		return $ret;
	}
}

function gatekeeper_content_load() {
	$ret = array(
		'select_sql' => ' ,gs.`security_id` AS `has_access_control`, gs.`security_id`, gs.`security_description`, gs.`is_private`, gs.`is_hidden`, gs.`access_question`, gs.`access_answer`  ',
		'join_sql' => " LEFT OUTER JOIN `".BIT_DB_PREFIX."gatekeeper_security_map` gsm ON ( lc.`content_id`=gsm.`content_id` )  LEFT OUTER JOIN `".BIT_DB_PREFIX."gatekeeper_security` gs ON ( gsm.`security_id`=gs.`security_id` ) ",
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
	if( !$gBitUser->isRegistered() || ( !empty( $pHash['user_id'] ) && $pHash['user_id'] != $gBitUser->mUserId )) {
		if( !$gBitUser->isAdmin() ) {
			if( $pContent->mDb->isAdvancedPostgresEnabled() && !empty( $pHash['content_id'] ) ) {
				global $gBitDb, $gBitSmarty;
				// This code makes use of the badass /usr/share/pgsql/contrib/tablefunc.sql
				// contribution that you have to install like: psql foo < /usr/share/pgsql/contrib/tablefunc.sql
				// This code pulls all branches for the current node and determines if there is a path from this content to the root
				// without hitting a security_id. If there is clear path it returns TRUE. If there is a security_id, then
				// it determines if the current user has permission
				$query = "SELECT branch,level,cb_item_content_id,cb_gallery_content_id,gs.*
						FROM connectby('`".BIT_DB_PREFIX."fisheye_gallery_image_map`', '`gallery_content_id`', '`item_content_id`', ?, 0, '/') AS t(`cb_gallery_content_id` int,`cb_item_content_id` int, `level` int, `branch` text)
							LEFT OUTER JOIN `".BIT_DB_PREFIX."gatekeeper_security_map` gsm ON (`cb_gallery_content_id`=gsm.`content_id`)
							LEFT OUTER JOIN `".BIT_DB_PREFIX."gatekeeper_security` gs ON (gs.`security_id`=gsm.`security_id`)
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
							$gBitSystem->setHttpStatus( 403 );
							$gBitSystem->fatalError( tra( $errorMessage ));
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
						$gBitSystem->fatalError( tra( $errorMessage ));
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
				$gBitSystem->display("bitpackage:gatekeeper/authenticate.tpl", "Password Required" , array( 'display_mode' => 'display' ));
				die;
			} else {
				$ret = '<h2>'.tra( "Password Required" ).'</h2>'.$gBitSmarty->fetch( "bitpackage:gatekeeper/authenticate.tpl" );
			}
		}
	}
	return $ret;
}


function gatekeeper_content_list( $pObject, $pParamHash ) {
/*
	global $gBitUser;
	$groups = array_keys($gBitUser->mGroups);
	$ret = array(
		'join_sql' => " LEFT JOIN `".BIT_DB_PREFIX."liberty_content_group_map` lcgm ON ( lc.`content_id`=lcgm.`content_id` ) LEFT OUTER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON ( ugm.`user_id`=".$gBitUser->mUserId." ) AND ( ugm.`group_id`=lcgm.`group_id` ) ",
		'where_sql' => " AND (lcgm.`content_id` IS NULL OR lcgm.`group_id` IN(". implode(',', array_fill(0, count($groups), '?')) ." ) OR ugm.`user_id`=?) ",
		'bind_vars' => array_merge( $groups, array( $gBitUser->mUserId ) ),
	);
//	$ret['bind_vars'] = array_merge( $groups, array( $gBitUser->mUserId ) );
//  $this->debug();
*/
	global $gBitSystem, $gGatekeeper, $gBitUser;
	
	if( is_object( $pObject ) && defined( 'FISHEYEIMAGE_CONTENT_TYPE_GUID' ) && method_exists ($pObject,"isContentType") && $pObject->isContentType( FISHEYEIMAGE_CONTENT_TYPE_GUID ) ) {
		if( $gBitSystem->mDb->isAdvancedPostgresEnabled() ) {
			$ret['where_sql'] = " AND (SELECT gks.`security_id` FROM connectby('fisheye_gallery_image_map', 'gallery_content_id', 'item_content_id', fi.`content_id`, 0, '/')  AS t(`cb_gallery_content_id` int, `cb_item_content_id` int, level int, branch text), `".BIT_DB_PREFIX."gatekeeper_security_map` cgm,  `".BIT_DB_PREFIX."gatekeeper_security` gks
					  WHERE gks.`security_id`=cgm.`security_id` AND cgm.`content_id`=`cb_gallery_content_id` LIMIT 1) IS NULL";
		} else {
			$ret = array(
			'select_sql' => ' ,gks.`security_id`, gks.`security_description`, gks.`is_private`, gks.`is_hidden`, gks.`access_question`, gks.`access_answer` ',
			'join_sql' => " LEFT OUTER JOIN `".BIT_DB_PREFIX."gatekeeper_security_map` cg ON (lc.`content_id`=cg.`content_id`) LEFT OUTER JOIN `".BIT_DB_PREFIX."gatekeeper_security` gks ON (gks.`security_id`=cg.`security_id` )  LEFT OUTER JOIN `".BIT_DB_PREFIX."fisheye_gallery_image_map` fgim ON (fgim.`item_content_id`=lc.`content_id`) LEFT OUTER JOIN `".BIT_DB_PREFIX."gatekeeper_security_map` tcs2 ON (fgim.`gallery_content_id`=tcs2.`content_id`) LEFT OUTER JOIN `".BIT_DB_PREFIX."gatekeeper_security` ts2 ON (ts2.`security_id`=tcs2.`security_id` )",
			'where_sql' => ' AND (tcs2.`security_id` IS NULL OR lc.`user_id`=?) ',
			'bind_vars' => array( $gBitUser->mUserId ),
			);
		}
	} else {
		$ret = array(
			'select_sql' => ' ,gks.`security_id`, gks.`security_description`, gks.`is_private`, gks.`is_hidden`, gks.`access_question`, gks.`access_answer` ',
			'join_sql' => " LEFT OUTER JOIN `".BIT_DB_PREFIX."gatekeeper_security_map` cg ON (lc.`content_id`=cg.`content_id`) LEFT OUTER JOIN `".BIT_DB_PREFIX."gatekeeper_security` gks ON (gks.`security_id`=cg.`security_id` )",
		); 
		if( !is_object( $pObject ) || !method_exists($pObject,"hasAdminPermission") || !$pObject->hasAdminPermission() ) {
			$ret['where_sql'] = ' AND (cg.`security_id` IS NULL OR lc.`user_id`=?) ';
			$ret['bind_vars'][] = $gBitUser->mUserId;
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
