<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\R3H6\Error404page\Utility\CustomPageUtility::addDoktype(
    $_EXTKEY,
    \R3H6\Error404page\Configuration\ExtensionConfiguration::doktypeError404page(),
    'Error404page'
);
