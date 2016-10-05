<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Override local page not found handling configuration
$GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFound_handling'] = 'USER_FUNCTION:R3H6\\Error404page\\Domain\\Hook\\ErrorHandlerHook->pageNotFound';

if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['errorHandlers'])) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['errorHandlers'] = array();
}

// Cache configuration
if (!is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][\R3H6\Error404page\Domain\Cache\ErrorHandlerCache::IDENTIFIER])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][\R3H6\Error404page\Domain\Cache\ErrorHandlerCache::IDENTIFIER] = array();
}

// Debug log
if (\R3H6\Error404page\Configuration\ExtensionConfiguration::is('debug')) {
    $GLOBALS['TYPO3_CONF_VARS']['LOG']['R3H6']['Error404page']['writerConfiguration'] = array(
        \TYPO3\CMS\Core\Log\LogLevel::DEBUG => array(
            'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => array(
               'logFile' => 'typo3temp/logs/debug.log',
            ),
        ),
    );
}
