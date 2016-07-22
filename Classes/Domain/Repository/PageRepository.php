<?php

namespace R3H6\Error404page\Domain\Repository;

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
use R3H6\Error404page\Configuration\ExtensionConfiguration;
use R3H6\Error404page\Domain\Repository\DomainRepository;

/**
 * PageRepository
 */
class PageRepository implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var R3H6\Error404page\Domain\Repository\DomainRepository
     * @inject
     */
    protected $domainRepository;

    /**
     * @var TYPO3\CMS\Frontend\Page\PageRepository
     * @inject
     */
    protected $pageRepository;

    /**
     * @var R3H6\Error404page\Configuration\ExtensionConfiguration
     * @inject
     */
    protected $extensionConfiguration;

    /**
     * @var array
     */
    protected $rootPageHostMap = [];

    /**
     * Initialize object
     */
    public function initializeObject()
    {
        $this->pageRepository->init(false);
        $this->pageRepository->sys_language_uid = $this->getSystemLanguage();
    }

    /**
     * Finds the root page uid for a given host.
     *
     * @param  string $host
     * @return int
     */
    public function findRootPageByHost($host)
    {
        if (!isset($this->rootPageHostMap[$host])) {
            $rootPageUid = 0;
            $domains = $this->domainRepository->findAllNonRedirectDomains();
            foreach ($domains as $domain) {
                if (strpos($host, $domain['domainName']) === 0) {
                    $rootPageUid = (int) $domain['pid'];
                    break;
                }
            }
            if ($rootPageUid === 0) {
                $rootPages = $this->getDatabaseConnection()->exec_SELECTgetRows('uid', 'pages', 'pid=0 AND is_siteroot=1' . $this->pageRepository->enableFields('pages'));
                if (count($rootPages) === 1) {
                    $rootPageUid = (int) $rootPages[0]['uid'];
                }
            }
            $this->rootPageHostMap[$host] = $rootPageUid;
        }

        return $this->rootPageHostMap[$host];
    }

    /**
     * Finds a error page for a host (domain).
     *
     * @param  string $host The domain name.
     * @return null|array Page record row on success.
     */
    public function findErrorPageByHost($host,$doktype)
    {
        $rootPageUid = $this->findRootPageByHost($host);
        $errorPages = $this->getAccessibleErrorPages($doktype);
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
    protected function getAccessibleErrorPages($doktype)
    {
        $doktype = $this->extensionConfiguration->get($doktype);

        $errorPages = (array) $this->getDatabaseConnection()->exec_SELECTgetRows(
            'uid',
            'pages',
            sprintf('doktype=%d', $doktype) . $this->pageRepository->enableFields('pages'),
            '',
            'sorting'
        );
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($errorPages);

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
     * Get system language uid
     *
     * @return int
     */
    protected function getSystemLanguage()
    {
        return (int) GeneralUtility::_GP('L');
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
