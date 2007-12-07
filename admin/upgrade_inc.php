<?php

global $gBitSystem, $gUpgradeFrom, $gUpgradeTo;

$upgrades = array(

	'BWR1' => array(
		'BWR2' => array(
// de-tikify tables
array( 'DATADICT' => array(
	array( 'RENAMETABLE' => array(
		'tiki_security' => 'gatekeeper_security',
		'tiki_content_security_map' => 'gatekeeper_security_map',
	)),
	array( 'ALTER' => array(
		'gatekeeper_security' => array(
			'group_id' => array( '`group_id`', 'I4' ), // , 'NOTNULL' ),
		),
	)),
)),

// query: create a gatekeeper_security_id_seq and bring the table up to date with the current max security_id used in the gatekeeper_security table - this basically for mysql
array( 'PHP' => '
	$query = $gBitDb->getOne("SELECT MAX(security_id) FROM `'.BIT_DB_PREFIX.'gatekeeper_security`");
	$tempId = $gBitDb->mDb->GenID("`'.BIT_DB_PREFIX.'gatekeeper_security_id_seq`", $query);
' ),
		)
	),
);

if( isset( $upgrades[$gUpgradeFrom][$gUpgradeTo] ) ) {
	$gBitSystem->registerUpgrade( GATEKEEPER_PKG_NAME, $upgrades[$gUpgradeFrom][$gUpgradeTo] );
}
?>
