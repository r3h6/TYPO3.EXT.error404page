<?php
return array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:error404page/Resources/Private/Language/locallang_db.xlf:tx_error404page_domain_model_error',
		'label' => 'url',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => true,
		'hideTable' => true,
		'enablecolumns' => array(

		),
		'searchFields' => 'url,url_hash,root_page,reason,counter,referer,ip,user_agent,',
		'iconfile' => 'EXT:error404page/Resources/Public/Icons/tx_error404page_domain_model_error.gif',
		'rootLevel' => -1
	),
	'interface' => array(
		'showRecordFieldList' => 'url, url_hash, root_page, reason, counter, referer, ip, user_agent',
	),
	'types' => array(
		'1' => array('showitem' => 'url, url_hash, root_page, reason, counter, referer, ip, user_agent, '),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(

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
		'url_hash' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:error404page/Resources/Private/Language/locallang_db.xlf:tx_error404page_domain_model_error.url_hash',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'root_page' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:error404page/Resources/Private/Language/locallang_db.xlf:tx_error404page_domain_model_error.root_page',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int,required'
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
		'counter' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:error404page/Resources/Private/Language/locallang_db.xlf:tx_error404page_domain_model_error.counter',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			)
		),
		'referer' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:error404page/Resources/Private/Language/locallang_db.xlf:tx_error404page_domain_model_error.referer',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim'
			)
		),
		'ip' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:error404page/Resources/Private/Language/locallang_db.xlf:tx_error404page_domain_model_error.ip',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'user_agent' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:error404page/Resources/Private/Language/locallang_db.xlf:tx_error404page_domain_model_error.user_agent',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),

	),
);