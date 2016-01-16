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

    public function __construct()
    {
        $this->domainRepository = GeneralUtility::makeInstance(DomainRepository::class);
        $this->pageRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\Page\PageRepository::class);
        $this->extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
    }

    public function findErrorPageByHost($host)
    {
        $rootPageUid = 0;
        $domains = $this->domainRepository->findAllNonRedirectDomains();
        foreach ($domains as $domain) {
            if (strpos($host, $domain['domainName']) !== false) {
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

    protected function getAccessibleErrorPages()
    {
        $doktype = $this->extensionConfiguration->getErrorPageType();
        $pages = (array) $this->getDatabaseConnection()->exec_SELECTgetRows(
            'uid',
            'pages',
            sprintf('doktype=%d', $doktype) . $this->pageRepository->enableFields('pages'),
            '',
            'sorting'
        );

        $accessiblePages = [];
        foreach ($pages as $i => $errorPage) {
            $rootLine = $this->pageRepository->getRootLine($errorPage['uid']);
            foreach ($rootLine as $page) {
                if (!$this->pageRepository->checkRecord('pages', (int) $page['uid'])) {
                    continue 2;
                }
            }
            $accessiblePages[] = $errorPage;
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
