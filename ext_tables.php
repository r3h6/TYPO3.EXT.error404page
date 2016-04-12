<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\R3H6\Page404\Utility\CustomPageUtility::addDoktype(
    $_EXTKEY,
    \R3H6\Page404\Configuration\ExtensionConfiguration::doktypePage404(),
    'Page404'
);
