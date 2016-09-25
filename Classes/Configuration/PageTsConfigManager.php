<?php

namespace R3H6\Error404page\Configuration;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * PageTsConfiguration
 *
 * API to access extension configuration (ext_conf_template.txt).
 */
class PageTsConfigManager implements \TYPO3\CMS\Core\SingletonInterface
{
    const TSCONFIG_KEY = 'tx_error404page';

    protected $pageTsConfig = [];

    /**
     * TypoScriptService
     *
     * @var \TYPO3\CMS\Extbase\Service\TypoScriptService
     * @inject
     */
    protected $typoScriptService;

    /**
     * Returns a PageTsConfig instance for a given page.
     *
     * @param  int $pageUid
     * @return R3H6\Error404page\Configuration\PageTsConfig
     */
    public function getPageTsConfig($pageUid)
    {
        $pageUid = (int) $pageUid;
        if (!isset($this->pageTsConfig[$pageUid])) {

            $pageTsConfig = $this->typoScriptService->convertTypoScriptArrayToPlainArray(BackendUtility::getPagesTSconfig($pageUid));
            $configuration = [];
            if (isset($pageTsConfig[self::TSCONFIG_KEY])) {
                $configuration = (array) $pageTsConfig[self::TSCONFIG_KEY];
            }

            $this->pageTsConfig[$pageUid] = GeneralUtility::makeInstance(PageTsConfig::class, $configuration, $pageUid);
        }
        return $this->pageTsConfig[$pageUid];
    }
}
