<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE' && \R3H6\Error404page\Configuration\ExtensionConfiguration::get('enableErrorLog')) {

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:error404page/Configuration/TypoScript/setup.txt">'
    );
}
