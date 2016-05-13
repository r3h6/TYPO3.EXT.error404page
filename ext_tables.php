<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\R3H6\Error404page\Utility\CustomPageUtility::addDoktype(
    $_EXTKEY,
    \R3H6\Error404page\Configuration\ExtensionConfiguration::get('doktypeError404page'),
    'Error404page'
);
if (TYPO3_MODE === 'BE' && \R3H6\Error404page\Configuration\ExtensionConfiguration::get('enableErrorLog')) {

	/**
	 * Registers a Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'R3H6.' . $_EXTKEY,
		'web',	 // Make module a submodule of 'web'
		'statistic',	// Submodule key
		'',						// Position
		array(
			'Error' => 'dashboard, list, deleteAll',

		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.svg',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_statistic.xlf',
		)
	);

}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_error404page_domain_model_error', 'EXT:error404page/Resources/Private/Language/locallang_csh_tx_error404page_domain_model_error.xlf');
