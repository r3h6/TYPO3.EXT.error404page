<?php
return array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:error404page/Resources/Private/Language/locallang_db.xlf:tx_error404page_domain_model_error',
		'label' => 'sha1',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,

		'enablecolumns' => array(

		),
		'searchFields' => 'sha1,url,reason,last_referer,counter,',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('error404page') . 'Resources/Public/Icons/tx_error404page_domain_model_error.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'sha1, url, reason, last_referer, counter',
	),
	'types' => array(
		'1' => array('showitem' => 'sha1, url, reason, last_referer, counter, '),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(

		'sha1' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:error404page/Resources/Private/Language/locallang_db.xlf:tx_error404page_domain_model_error.sha1',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'url' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:error404page/Resources/Private/Language/locallang_db.xlf:tx_error404page_domain_model_error.url',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim,required'
			)
		),
		'reason' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:error404page/Resources/Private/Language/locallang_db.xlf:tx_error404page_domain_model_error.reason',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim,required'
			)
		),
		'last_referer' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:error404page/Resources/Private/Language/locallang_db.xlf:tx_error404page_domain_model_error.last_referer',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim'
			)
		),
		'counter' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:error404page/Resources/Private/Language/locallang_db.xlf:tx_error404page_domain_model_error.counter',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int,required'
			)
		),
		
	),
);