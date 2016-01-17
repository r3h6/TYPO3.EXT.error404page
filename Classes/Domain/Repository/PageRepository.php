<?php

namespace Monogon\Page404\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Monogon\Page404\Configuration\ExtensionConfiguration;
use Monogon\Page404\Domain\Repository\DomainRepository;

class PageRepository implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var Monogon\Page404\Domain\Repository\DomainRepository
     */
    protected $domainRepository;

    /**
     * @var TYPO3\CMS\Frontend\Page\PageRepository
     */
    protected $pageRepository;

    /**
     * @var Monogon\Page404\Configuration\ExtensionConfiguration
     */
    protected $extensionConfiguration;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->domainRepository = GeneralUtility::makeInstance(DomainRepository::class);
        $this->extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $this->pageRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\Page\PageRepository::class);
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
