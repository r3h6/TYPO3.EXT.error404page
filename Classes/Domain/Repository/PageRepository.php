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
use R3H6\Error404page\Domain\Model\Error;
use R3H6\Error404page\Domain\Model\Page;

/**
 * PageRepository
 */
class PageRepository implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var \R3H6\Error404page\Domain\Repository\DomainRepository
     * @inject
     */
    protected $domainRepository;

    /**
     * @var \TYPO3\CMS\Frontend\Page\PageRepository
     * @inject
     */
    protected $pageRepository;

    /**
     * @var \R3H6\Error404page\Configuration\ExtensionConfiguration
     * @inject
     */
    protected $extensionConfiguration;

    /**
     * @var array
     */
    protected $cachedRootPagesByHost = [];

    /**
     * Initialize object
     */
    public function initializeObject()
    {
        $this->pageRepository->init(false);
        $this->pageRepository->sys_language_uid = $this->getSystemLanguage();
    }

    /**
     * [findErrorPageByError description]
     *
     * @param  \R3H6\Error404page\Domain\Model\Error $error
     * @return null|array Page record row on success.
     */
    public function findOneByError(Error $error)
    {
        if ($this->extensionConfiguration->get('feature403') && $error->getStatusCode() === Error::STATUS_CODE_NOT_FOUND) {
            $errorPage = $this->find403PageByError($error);
            if ($errorPage !== null) {
                return $errorPage;
            }
        }
        return $this->find404PageByError($error);
    }

    public function find403PageByError(Error $error)
    {
        $doktype = (int) $this->extensionConfiguration->get('doktype403page');
        $rootLine = $this->pageRepository->getRootLine($error->getPid());
        foreach ($rootLine as $pageRecord) {
            if ($doktype === (int) $pageRecord['doktype']) {
                return $pageRecord;
            }
        }
        return $this->findOneByHostAndDoktype($error->getHost(), $doktype);
    }

    public function find404PageByError(Error $error)
    {
        $doktype = (int) $this->extensionConfiguration->get('doktype404page');
        return $this->findOneByHostAndDoktype($error->getHost(), $doktype);
    }

    public function findByIdentifier($identifier)
    {
        $page = $this->pageRepository->getPage((int) $identifier);
        if (is_array($page) && isset($page['uid']) && $this->isAccessible($page['uid'])) {
            return $this->createDomainObject($page);
        }
        return null;
    }

    /**
     * Finds the root page uid for a given host.
     *
     * @param  string $host
     * @return array|null
     */
    public function findRootPageByHost($host)
    {
        if (!isset($this->cachedRootPagesByHost[$host])) {
            $rootPage = null;
            $domains = $this->domainRepository->findAll();
            foreach ($domains as $domain) {
                if (empty($domain['redirectTo']) && strpos($host, $domain['domainName']) === 0) {
                    $rootPage = $this->findByIdentifier($domain['pid']);
                    if ($rootPage !== null) {
                        break;
                    }
                }
            }
            if ($rootPage === null) {
                $rootPages = $this->getDatabaseConnection()->exec_SELECTgetRows('uid', 'pages', 'pid=0 AND is_siteroot=1' . $this->pageRepository->enableFields('pages'));
                if (count($rootPages) === 1) {
                    $rootPage = $this->findByIdentifier($rootPages[0]['uid']);
                }
            }
            $this->cachedRootPagesByHost[$host] = $rootPage;
        }

        return $this->cachedRootPagesByHost[$host];
    }

    /**
     * Finds a error page for a host (domain).
     *
     * @param  string $host The domain name.
     * @return null|array Page record row on success.
     */
    public function findOneByHostAndDoktype($host, $doktype)
    {
        $pages = $this->findAllByDoktype($doktype);
        if (count($pages) === 1) {
            return reset($pages);
        }

        $rootPage = $this->findRootPageByHost($host);
        if ($rootPage !== null) {
            foreach ($pages as $page) {
                $rootLine = $this->pageRepository->getRootLine($page['uid']);
                foreach ($rootLine as $parentPage) {
                    if ($rootPageUid === (int) $parentPage['uid']) {
                        return $page;
                    }
                }
            }
        }

        if (count($pages)) {
            return reset($pages);
        }

        return null;
    }

    protected function isAccessible(array $page)
    {
        $rootLine = (array) $this->pageRepository->getRootLine($page['uid']);
        if (!empty($rootLine)) {
            foreach ($rootLine as $parentPage) {
                if (!$this->pageRepository->checkRecord('pages', (int) $parentPage['uid'])) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Returns all accessible error pages from all websites.
     *
     * @return array<\R3H6\Error404page\Domain\Model\Page>
     */
    protected function findAllByDoktype($doktype)
    {
        // $doktype = $this->extensionConfiguration->get('doktypeError404page');

        $result = (array) $this->getDatabaseConnection()->exec_SELECTquery(
            'uid',
            'pages',
            sprintf('doktype=%d', $doktype) . $this->pageRepository->enableFields('pages'),
            '',
            'sorting'
        );

        $pages = [];
        while ($record = $this->getDatabaseConnection()->sql_fetch_assoc($result)) {
            $page = $this->findByIdentifier($record['uid']);
            if ($page !== null) {
                $pages[] = $page;
            }
        }
        return $pages;
    }

    protected function createDomainObject($data)
    {
        return GeneralUtility::makeInstance(Page::class, $data);
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
