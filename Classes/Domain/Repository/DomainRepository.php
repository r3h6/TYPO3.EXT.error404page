<?php

namespace Monogon\Page404\Domain\Repository;


use TYPO3\CMS\Core\Utility\GeneralUtility;

class DomainRepository implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var TYPO3\CMS\Frontend\Page\PageRepository
     */
    protected $pageRepository;

    public function __construct()
    {
        $this->pageRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\Page\PageRepository::class);
    }

    public function findAllNonRedirectDomains()
    {
        $domains = $this->getDatabaseConnection()->exec_SELECTgetRows(
            '*',
            'sys_domain',
            'redirectTo=\'\'' . $this->pageRepository->enableFields('sys_domain')
        );
        return (array) $domains;
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
