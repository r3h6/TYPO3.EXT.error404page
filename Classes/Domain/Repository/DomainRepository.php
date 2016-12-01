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

/**
 * DomainRepository.
 */
class DomainRepository implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var \TYPO3\CMS\Frontend\Page\PageRepository
     * @inject
     */
    protected $pageRepository;

    /**
     * Find all.
     *
     * @return array Domain records
     */
    public function findAll()
    {
        $domains = $this->getDatabaseConnection()->exec_SELECTgetRows(
            '*',
            'sys_domain',
            "redirectTo=''".$this->pageRepository->enableFields('sys_domain')
        );

        return (array) $domains;
    }

    public function findByDomainName($domainName)
    {
        $domains = $this->getDatabaseConnection()->exec_SELECTgetRows(
            '*',
            'sys_domain',
            "redirectTo='' AND domainName=".$this->getDatabaseConnection()->fullQuoteStr($domainName, 'sys_domain').' '.$this->pageRepository->enableFields('sys_domain')
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
