<?php

namespace Monogon\Page404\Domain\Repository;

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
use Monogon\Page404\Configuration\ExtensionConfiguration;
use Monogon\Page404\Domain\Repository\DomainRepository;

/**
 * PageRepository
 */
class PageRepository implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var Monogon\Page404\Domain\Repository\DomainRepository
     * @inject
     */
    protected $domainRepository;

    /**
     * @var TYPO3\CMS\Frontend\Page\PageRepository
     * @inject
     */
    protected $pageRepository;

    /**
     * @var Monogon\Page404\Configuration\ExtensionConfiguration
     * @inject
     */
    protected $extensionConfiguration;

    /**
     * Constructor
     */
    // public function __construct()
    // {
    //     $this->domainRepository = GeneralUtility::makeInstance(DomainRepository::class);
    //     $this->extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
    //     $this->pageRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\Page\PageRepository::class);
    //     $this->pageRepository->init(false);
    //     $this->pageRepository->sys_language_uid = $GLOBALS['TSFE']->sys_language_uid;
    // }

    public function initializeObject()
    {
        $this->pageRepository->init(false);
        $this->pageRepository->sys_language_uid = $GLOBALS['TSFE']->sys_language_uid;
    }

    /**
     * Finds a error page for a host (domain).
     *
     * @param  string $host The domain name.
     * @return null|array Page record row on success.
     */
    public function findErrorPageByHost($host)
    {
        $rootPageUid = 0;
        $domains = $this->domainRepository->findAllNonRedirectDomains();
        foreach ($domains as $domain) {
            if (strpos($host, $domain['domainName']) === 0) {
                $rootPageUid = (int) $domain['pid'];
            }
        }

        $errorPages = $this->getAccessibleErrorPages();
        if ($rootPageUid) {
            foreach ($errorPages as $errorPage) {
                $rootLine = $this->pageRepository->getRootLine($errorPage['uid']);
                foreach ($rootLine as $page) {
                    if ($rootPageUid === (int) $page['uid']) {
                        return $errorPage;
                    }
                }
            }
        }
        if (count($errorPages)) {
            return reset($errorPages);
        }

        return null;
    }

    /**
     * Returns all accessible error pages from all websites.
     *
     * @return array
     */
    protected function getAccessibleErrorPages()
    {
        $doktype = $this->extensionConfiguration->doktypePage404();
        $errorPages = (array) $this->getDatabaseConnection()->exec_SELECTgetRows(
            'uid',
            'pages',
            sprintf('doktype=%d', $doktype) . $this->pageRepository->enableFields('pages'),
            '',
            'sorting'
        );

        $accessiblePages = [];
        foreach ($errorPages as $errorPage) {
            $page = $this->pageRepository->getPage($errorPage['uid']);
            if (!empty($page)) {
                $rootLine = $this->pageRepository->getRootLine($page['uid']);
                foreach ($rootLine as $p) {
                    if (!$this->pageRepository->checkRecord('pages', (int) $p['uid'])) {
                        continue 2;
                    }
                }
                $accessiblePages[] = $page;
            }
        }
        return $accessiblePages;
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
