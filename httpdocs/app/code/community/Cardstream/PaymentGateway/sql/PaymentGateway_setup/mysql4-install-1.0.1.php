<?php

$installer = $this;

$columns = array(
	'cardstream_transaction_unique' => array(
		'type' => 'VARCHAR',
		'length' => '255'
	),
	'cardstream_order_ref' => array(
		'type' => 'VARCHAR',
		'length' => '255'
	),
	'cardstream_xref' => array(
		'type' => 'VARCHAR',
		'length' => '255'
	),
	'cardstream_amount_received' => array(
		'type' => 'VARCHAR',
		'length' => '64'
	),
	'cardstream_response_message' => array(
		'type' => 'VARCHAR',
		'length' => '256',
	),
	'cardstream_authorisation_code' => array(
		'type' => 'VARCHAR',
		'length' => '256'
	)
);
function getMissingTableColumns($installer, $tableName, $columns) {
	$currentColumns = array_keys($installer->getConnection()->describeTable($installer->getTable($tableName)));
	$missing = array();
	foreach (array_keys($columns) as $key) {
		if (!in_array($key, $currentColumns)) {
			$missing[$key] = $columns[$key];
		}
	}
	return $missing;
}
//Make sure to only add columns that are not there...
foreach (array('sales/quote_payment', 'sales/order_payment') as $table) {
	$missing = getMissingTableColumns($installer, $table, $columns);
	if (!empty($missing)) {
		foreach ($missing as $key => $value) {
			$installer->run(
				"ALTER TABLE `{$installer->getTable($table)}` ADD `$key` {$value['type']}({$value['length']}) NOT NULL"
			);
		}
	}
}
$installer->startSetup();

$tableName = $this->getTable('CardstreamTransactions');

$installer->run("CREATE TABLE IF NOT EXISTS `". $tableName ."` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`customerid` varchar(255) DEFAULT NULL,
`orderid` varchar(255) DEFAULT NULL,
`transactionunique` varchar(255) DEFAULT NULL,
`amount` bigint(20) DEFAULT NULL,
`xref` varchar(255) DEFAULT NULL,
`responsecode` varchar(255) DEFAULT NULL,
`message` varchar(255) DEFAULT NULL,
`ctime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
`mtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
`ip` varchar(255) DEFAULT NULL,
`threedsenrolled` varchar(255) DEFAULT NULL,
`threedsauthenticated` varchar(255) DEFAULT NULL,
`lastfour` varchar(4) DEFAULT NULL,
`cardtype` varchar(255) DEFAULT NULL,
`quoteid` varchar(255) DEFAULT NULL,
PRIMARY KEY (`id`));
");
/*
ALTER TABLE `{$installer->getTable('sales/quote_payment')}`
ADD `cs_owner` VARCHAR( 255 ) NOT NULL,
ADD `cs_number` VARCHAR( 255 ) NOT NULL,
ADD `cs_exp_month` SMALLINT( 5 ) NOT NULL,
ADD `cs_exp_year` SMALLINT( 5 ) NOT NULL;

ALTER TABLE `{$installer->getTable('sales/order_payment')}`
ADD `cs_owner` VARCHAR( 255 ) NOT NULL,
ADD `cs_number` VARCHAR( 255 ) NOT NULL,
ADD `cs_exp_month` SMALLINT( 5 ) NOT NULL,
ADD `cs_exp_year` SMALLINT( 5 ) NOT NULL;
*/
$installer->endSetup();
