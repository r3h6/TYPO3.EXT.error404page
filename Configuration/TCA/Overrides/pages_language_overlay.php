<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\R3H6\Page404\Utility\CustomPageUtility::addDoktypeToPagesLanguageOverlay(
    \R3H6\Page404\Configuration\ExtensionConfiguration::EXT_KEY,
    \R3H6\Page404\Configuration\ExtensionConfiguration::doktypePage404(),
    'Page404',
    '404'
);
