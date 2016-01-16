<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

// \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Page 404');

$relativeExtensionPath = '../typo3conf/ext/page404/';
// $relativeExtensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY);

// Define a new doktype
$customPageDoktype = 104;
$customPageIcon = $relativeExtensionPath . 'Resources/Public/Icons/Page404.png';
// Add the new doktype to the list of page types
$GLOBALS['PAGES_TYPES'][$customPageDoktype] = array(
        'type' => 'web',
        'icon' => $customPageIcon,
        'allowedTables' => '*'
);

// Add the new doktype to the page type selector
$GLOBALS['TCA']['pages']['columns']['doktype']['config']['items'][] = array(
        'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be.xlf:pages.doktype.404',
        $customPageDoktype,
        $customPageIcon
);

// Also add the new doktype to the page language overlays type selector (so that translations can inherit the same type)
$GLOBALS['TCA']['pages_language_overlay']['columns']['doktype']['config']['items'][] = array(
        'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be.xlf:pages.doktype.404',
        $customPageDoktype,
        $customPageIcon
);

// Add the icon for the new doktype
\TYPO3\CMS\Backend\Sprite\SpriteManager::addTcaTypeIcon('pages', $customPageDoktype, $customPageIcon);


/** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
// $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

// $iconRegistry->registerIcon(
//     'app-page404',
//     \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
//     [
//         'source' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/Page404.png'
//     ]
// );
// $a = $iconRegistry->getIconConfigurationByIdentifier('app-page404');
// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($a);exit;

// Add the new doktype to the list of types available from the new page menu at the top of the page tree
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig(
        'options.pageTree.doktypesToShowInNewPageDragArea := addToList(' . $customPageDoktype . ')'
);