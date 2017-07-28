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
use R3H6\Error404page\Domain\Model\Error;
use R3H6\Error404page\Domain\Model\Page;

/**
 * PageRepository.
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
    protected $cachedRootPagesByHost = array();

    /**
     * @var array
     */
    protected $cachedPagesByDoktype = array();

    /**
     * Initialize object.
     */
    public function initializeObject()
    {
        $this->pageRepository->init(false);
        // $this->pageRepository->sys_language_uid = $this->getSystemLanguage();
    }

    /**
     * @param Error $error [description]
     *
     * @return \R3H6\Error404page\Domain\Model\Page|null
     */
    public function findLoginPageForError(Error $error)
    {
        $this->pageRepository->sys_language_uid = $error->getLanguage();
        if ($error->getPid()) {
            $rows = $this->getDatabaseConnection()->exec_SELECTgetRows('pid', 'tt_content', "CType='login'".$this->pageRepository->enableFields('tt_content'));
            // Do not return a page if there is only one found, it could belong to an other domain!
            $rootLine = $this->pageRepository->getRootLine($error->getPid());

            // Search for a login page within the rootline from the requested page.
            foreach ($rows as $row) {
                foreach ($rootLine as $pageRecord) {
                    if ((int) $pageRecord['uid'] === (int) $row['pid']) {
                        return $this->findByIdentifier($row['pid']);
                    }
                }
            }

            // Search first login page with same root page.
            foreach ($rows as $row) {
                $loginRootLine = $this->pageRepository->getRootLine($row['pid']);
                if ($rootLine[0]['uid'] === $loginRootLine[0]['uid']) {
                    return $this->findByIdentifier($row['pid']);
                }
            }
        }

        return;
    }

    /**
     * [find404PageForError description].
     *
     * @param Error $error [description]
     *
     * @return \R3H6\Error404page\Domain\Model\Page|null
     */
    public function find404PageForError(Error $error)
    {
        $this->pageRepository->sys_language_uid = $error->getLanguage();
        $doktype = (int) $this->extensionConfiguration->get('doktypeError404page');
        $page = $this->findFirstByHostAndDoktype($error->getHost(), $doktype);
        if ($page === null) {
            $page = $this->findFirstWithoutHostByDoktype($doktype);
        }

        return $page;
    }

    protected function findByIdentifier($identifier)
    {
        $page = $this->pageRepository->getPage((int) $identifier);
        if (is_array($page) && isset($page['uid'])) {
            return $this->createDomainObject($page);
        }

        return;
    }

    /**
     * Finds the root page uid for a given host.
     *
     * @param string $host
     *
     * @return \R3H6\Error404page\Domain\Model\Page|null
     */
    protected function findRootPageByHost($host)
    {
        if (!isset($this->cachedRootPagesByHost[$host])) {
            $rootPage = null;
            $domains = $this->domainRepository->findByDomainName($host);
            foreach ($domains as $domain) {
                $rootPage = $this->findByIdentifier($domain['pid']);
                if ($rootPage !== null) {
                    break;
                }
            }
            if ($rootPage === null) {
                $rootPages = $this->getDatabaseConnection()->exec_SELECTgetRows('uid', 'pages', 'pid=0 AND is_siteroot=1'.$this->pageRepository->enableFields('pages'));
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
     * @param string $host The domain name.
     *
     * @return null|\R3H6\Error404page\Domain\Model\Page Page record row on success.
     */
    protected function findFirstByHostAndDoktype($host, $doktype)
    {
        /** @var array<\R3H6\Error404page\Domain\Model\Page> $pages */
        $pages = $this->findAllByDoktype($doktype);

        /** @var \R3H6\Error404page\Domain\Model\Page $rootPage */
        $rootPage = $this->findRootPageByHost($host);

        if ($rootPage !== null) {
            $this->getLogger()->debug(sprintf('Get first matching error page for root page "%s"', $rootPage->getUid()));
            foreach ($pages as $page) {
                $rootLine = $this->pageRepository->getRootLine($page->getUid());
                foreach ($rootLine as $parentPage) {
                    if ($rootPage->getUid() === (int) $parentPage['uid']) {
                        return $page;
                    }
                }
            }
        } elseif (count($pages)) {
            return reset($pages);
        }

        $this->getLogger()->debug(sprintf('No page found for host "%s" and doktype "%s"', $host, $doktype));

        return;
    }

    protected function findFirstWithoutHostByDoktype($doktype)
    {
        /** @var array<\R3H6\Error404page\Domain\Model\Page> $pages */
        $pages = $this->findAllByDoktype($doktype);

        $domains = $this->domainRepository->findAll();

        $blackList = array();
        foreach ($domains as $domain) {
            $blackList[] = $domain['pid'];
        }

        foreach ($pages as $page) {
            $rootLine = $this->pageRepository->getRootLine($page->getUid());
            foreach ($rootLine as $parentPage) {
                if (in_array($parentPage['uid'], $blackList)) {
                    continue 2; // It's not this page, try next!
                }
            }

            return $page;
        }

        $this->getLogger()->debug(sprintf('No page found for doktype "%s"', $doktype));

        return;
    }

    /**
     * Returns all accessible error pages from all websites.
     *
     * @return array<\R3H6\Error404page\Domain\Model\Page>
     */
    protected function findAllByDoktype($doktype)
    {
        if (!isset($this->cachedPagesByDoktype[$doktype])) {
            $result = $this->getDatabaseConnection()->exec_SELECTquery(
                'uid',
                'pages',
                sprintf('doktype=%d', $doktype).$this->pageRepository->enableFields('pages'),
                '',
                'sorting'
            );

            $pages = array();
            while ($record = $this->getDatabaseConnection()->sql_fetch_assoc($result)) {
                $page = $this->findByIdentifier($record['uid']);
                if ($page !== null) {
                    $this->getLogger()->debug(sprintf('Found page "#%s" with doktype "%s"', $page->getUid(), $doktype));
                    $pages[] = $page;
                }
            }

            $this->cachedPagesByDoktype[$doktype] = $pages;
        }

        return $this->cachedPagesByDoktype[$doktype];
    }

    protected function createDomainObject($data)
    {
        return GeneralUtility::makeInstance('R3H6\\Error404page\\Domain\\Model\\Page', $data);
    }

    /**
     * Get system language uid.
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

    /**
     * Get class logger.
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger()
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Log\\LogManager')->getLogger(__CLASS__);
    }
}
