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
