<?php

namespace R3H6\Error404page\Utility\Compatibility6;

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 3 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

/**
 * CustomPageUtility.
 */
class CustomPageUtility
{
    private static function getCustomPageIcon($extKey, $iconName)
    {
        $identifier = 'apps-pagetree-'.strtolower($iconName);
        $relativeExtensionPath = '../typo3conf/ext/'.$extKey.'/';

        // Define a new doktype
        $customPageIcon = $relativeExtensionPath.'Resources/Public/Icons/'.$identifier.'.png';

        return $customPageIcon;
    }

    public static function addDoktype($extKey, $doktype, $iconName)
    {
        $customPageIcon = static::getCustomPageIcon($extKey, $iconName);

        // Add the new doktype to the list of page types
        $GLOBALS['PAGES_TYPES'][$doktype] = array(
                'type' => 'web',
                'icon' => $customPageIcon,
                'allowedTables' => '*',
        );

        // Add the icon for the new doktype
        \TYPO3\CMS\Backend\Sprite\SpriteManager::addTcaTypeIcon('pages', $doktype, $customPageIcon);

        // Add the new doktype to the list of types available from the new page menu at the top of the page tree
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig(
            'options.pageTree.doktypesToShowInNewPageDragArea := addToList('.$doktype.')'
        );
    }

    public static function addDoktypeToPages($extKey, $doktype, $iconName, $alias = null)
    {
        $customPageIcon = static::getCustomPageIcon($extKey, $iconName);

        if ($alias === null) {
            $alias = $doktype;
        }
        // Add the new doktype to the page type selector
        $GLOBALS['TCA']['pages']['columns']['doktype']['config']['items'][] = array(
                'LLL:EXT:'.$extKey.'/Resources/Private/Language/locallang_be.xlf:pages.doktype.'.$alias,
                $doktype,
                $customPageIcon,
        );
    }

    public static function addDoktypeToPagesLanguageOverlay($extKey, $doktype, $iconName, $alias = null)
    {
        $customPageIcon = static::getCustomPageIcon($extKey, $iconName);

        if ($alias === null) {
            $alias = $doktype;
        }
        // Also add the new doktype to the page language overlays type selector (so that translations can inherit the same type)
        $GLOBALS['TCA']['pages_language_overlay']['columns']['doktype']['config']['items'][] = array(
                'LLL:EXT:'.$extKey.'/Resources/Private/Language/locallang_be.xlf:pages.doktype.'.$alias,
                $doktype,
                $customPageIcon,
        );
    }
}
