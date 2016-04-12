<?php

namespace R3H6\Page404\Utility;

class CustomPageUtility
{
    public static function addDoktype($extKey, $doktype, $iconName)
    {
        // Add new page type:
        $GLOBALS['PAGES_TYPES'][$doktype] = [
            'type' => 'web',
            'allowedTables' => '*',
        ];

        $identifier = 'apps-pagetree-' . strtolower($iconName);

        // Provide icon for page tree, list view, ... :
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon(
            $identifier,
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            [
                'source' => 'EXT:' . $extKey . '/Resources/Public/Icons/' . $identifier . '.svg',
            ]
        );
        $iconRegistry->registerIcon(
            $identifier . '-hideinmenu',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            [
                'source' => 'EXT:' . $extKey . '/Resources/Public/Icons/' . $identifier . '-hideinmenu.svg',
            ]
        );

        // Allow backend users to drag and drop the new page type:
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig(
            'options.pageTree.doktypesToShowInNewPageDragArea := addToList(' . $doktype . ')'
        );
    }

    public static function addDoktypeToPages($extKey, $doktype, $iconName, $alias = null)
    {
        $identifier = 'apps-pagetree-' . strtolower($iconName);
        $extRelPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($extKey);
        $customPageIcon = $extRelPath . 'Resources/Public/Icons/' . $identifier . '.svg';

        // Add new page type as possible select item:
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
            'pages',
            'doktype',
            [
                'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_be.xlf:pages.doktype.' . (($alias === null) ? $doktype: $alias),
                $doktype,
                $customPageIcon
            ],
            '1',
            'after'
        );

        // Add icon for new page type:
        \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
            $GLOBALS['TCA']['pages'],
            [
                'ctrl' => [
                    'typeicon_classes' => [
                        $doktype => $identifier,
                        $doktype . '-hideinmenu' => $identifier . '-hideinmenu',
                    ],
                ],
            ]
        );
    }

    public static function addDoktypeToPagesLanguageOverlay($extKey, $doktype, $iconName, $alias = null)
    {
        $identifier = 'apps-pagetree-' . strtolower($iconName);
        $extRelPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($extKey);
        $customPageIcon = $extRelPath . 'Resources/Public/Icons/' . $identifier . '.svg';

        // Add new page type as possible select item:
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
            'pages_language_overlay',
            'doktype',
            [
                'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_be.xlf:pages.doktype.' . (($alias === null) ? $doktype: $alias),
                $doktype,
                $customPageIcon
            ],
            '1',
            'after'
        );
    }
}
