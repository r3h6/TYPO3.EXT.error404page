<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\Monogon\Page404\Utility\CustomPageUtility::addDoktype(
    $_EXTKEY,
    \Monogon\Page404\Configuration\ExtensionConfiguration::doktypePage404(),
    'Page404'
);
