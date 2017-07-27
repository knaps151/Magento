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

$installer->endSetup();
