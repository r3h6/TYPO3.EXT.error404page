<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Override local page not found handling configuration
$GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFound_handling'] = 'USER_FUNCTION:R3H6\\Error404page\\Domain\\Hook\\ErrorHandlerHook->pageNotFound';

// Define global hooks array
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

if (version_compare(\TYPO3\CMS\Core\Utility\VersionNumberUtility::getCurrentTypo3Version(), '8.0.0', '<')) {
    /** @var \TYPO3\CMS\Extbase\Object\Container\Container $objectConainer */
    $objectContainer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class);
    $objectContainer->registerImplementation(\R3H6\Error404page\Service\HttpService::class, \R3H6\Error404page\Service\Compatibility7\HttpService::class);
    $objectContainer->registerImplementation(\R3H6\Error404page\Domain\Handler\DefaultErrorHandler::class, \R3H6\Error404page\Domain\Handler\Compatibility7\DefaultErrorHandler::class);
}
