<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFound_handling'] = 'USER_FUNCTION:R3H6\\Error404page\\Domain\\Hook\\ErrorHandlerHook->pageNotFound';

// Debug log
if (\TYPO3\CMS\Core\Utility\GeneralUtility::getApplicationContext()->isDevelopment()) {
    $GLOBALS['TYPO3_CONF_VARS']['LOG']['R3H6']['Error404page']['writerConfiguration'] = array(
        \TYPO3\CMS\Core\Log\LogLevel::DEBUG => array(
            'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => array(
               'logFile' => 'typo3temp/logs/debug.log',
            ),
        ),
    );
}
