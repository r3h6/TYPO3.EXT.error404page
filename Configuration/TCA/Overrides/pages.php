<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\Monogon\Page404\Utility\CustomPageUtility::addDoktypeToPages(
    \Monogon\Page404\Configuration\ExtensionConfiguration::EXT_KEY,
    \Monogon\Page404\Configuration\ExtensionConfiguration::doktypePage404(),
    'Page404',
    '404'
);
