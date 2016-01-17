<?php

namespace Monogon\Page404\Utility;

class CustomPageUtility
{
    public static function addDoktype($extKey, $customPageDoktype, $alias = null)
    {
        $relativeExtensionPath = '../typo3conf/ext/' . $extKey . '/';
        if ($alias === null) {
            $alias = $customPageDoktype;
        }

        // Define a new doktype
        $customPageIcon = $relativeExtensionPath . 'Resources/Public/Icons/Page404.png';

        // Add the new doktype to the list of page types
        $GLOBALS['PAGES_TYPES'][$customPageDoktype] = array(
                'type' => 'web',
                'icon' => $customPageIcon,
                'allowedTables' => '*'
        );

        // Add the new doktype to the page type selector
        $GLOBALS['TCA']['pages']['columns']['doktype']['config']['items'][] = array(
                'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_be.xlf:pages.doktype.' . $alias,
                $customPageDoktype,
                $customPageIcon
        );

        // Also add the new doktype to the page language overlays type selector (so that translations can inherit the same type)
        $GLOBALS['TCA']['pages_language_overlay']['columns']['doktype']['config']['items'][] = array(
                'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_be.xlf:pages.doktype.' . $alias,
                $customPageDoktype,
                $customPageIcon
        );

        // Add the icon for the new doktype
        \TYPO3\CMS\Backend\Sprite\SpriteManager::addTcaTypeIcon('pages', $customPageDoktype, $customPageIcon);

        // Add the new doktype to the list of types available from the new page menu at the top of the page tree
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig(
            'options.pageTree.doktypesToShowInNewPageDragArea := addToList(' . $customPageDoktype . ')'
        );
    }
}
