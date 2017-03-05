<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\R3H6\Error404page\Utility\CustomPageUtility::addDoktypeToPages(
    \R3H6\Error404page\Configuration\ExtensionConfiguration::EXT_KEY,
    \R3H6\Error404page\Configuration\ExtensionConfiguration::get('doktypeError404page'),
    'Error404page',
    '404'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
    'error404page',
    'Configuration/PageTS/Redirect403.txt',
    'EXT:error404page :: Redirect 403 error to login page'
);
