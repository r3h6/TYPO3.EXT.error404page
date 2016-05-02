<?php
namespace R3H6\Error404page\Domain\Repository;

use R3H6\Error404page\Domain\Model\Error;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 R3 H6 <r3h6@outlook.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * The repository for Errors
 */
class ErrorRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    const MAX_ENTRIES = 10000;

    protected static $table = 'tx_error404page_domain_model_error';

    public function initializeObject()
    {
        /** @var $querySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
        $querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $querySettings->setRespectStoragePage(false);
        $querySettings->setRespectSysLanguage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     */
    public function findErrorGroupedByDay(\DateTime $startDate = null, \DateTime $endDate = null)
    {
        // if ($endDate === null) {
        //     $endDate = new \DateTime('today midnight');
        // }
        // if ($startDate === null) {
        //     $startDate = clone $endDate;
        //     $startDate->modify('-1 month');
        // }
        // /** @var TYPO3\CMS\Extbase\Persistence\Generic\Query $query */
        // $query = $this->createQuery();
        // $query->statement(sprintf('SELECT count(*) AS counter, DATE(FROM_UNIXTIME(crdate)) AS dayDate FROM %s WHERE crdate > %d AND crdate < %d GROUP BY dayDate ORDER BY dayDate ASC', static::$table, $startDate->getTimestamp(), $endDate->getTimestamp()));
        // return $query->execute(true);
    }

    /**
     * @param $limit
     */
    public function findErrorTopReasons($limit = 10)
    {
        // /** @var TYPO3\CMS\Extbase\Persistence\Generic\Query $query */
        // $query = $this->createQuery();
        // $query->statement(sprintf('SELECT reason, count(*) AS counter FROM %s GROUP BY reason ORDER BY counter DESC LIMIT %d', static::$table, $limit));
        // return $query->execute(true);
    }

    /**
     * @param $limit
     */
    public function findErrorTopUrls($limit = 10)
    {
        /** @var TYPO3\CMS\Extbase\Persistence\Generic\Query $query */
        $query = $this->createQuery();
        $query->statement(sprintf('SELECT url, count(*) AS counter FROM %s GROUP BY url ORDER BY counter DESC LIMIT %d', static::$table, $limit));
        return $query->execute(true);
    }

    public function log($url, $rootPage, $reason, $referer, $userAgent, $ip)
    {
        /** @var R3H6\Error404page\Domain\Model\Error $error */
        $error = $this->objectManager->get(Error::class);
        $error->setUrl($url);
        $error->setRootPage($rootPage);
        $error->setReason($reason);
        $error->setReferer($referer);
        $error->setUserAgent($userAgent);
        $error->setIp($ip);

        $count = $this->getDatabaseConnection()->exec_SELECTcountRows('*', self::$table);
        if ($count < self::MAX_ENTRIES) {
            $this->getDatabaseConnection()->exec_INSERTquery(self::$table, $error->toArray());
        } else {
            $row = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('uid', self::$table, '1=1', '', 'crdate ASC');
            $this->getDatabaseConnection()->exec_UPDATEquery(self::$table, 'uid=' . $row['uid'], $error->toArray());
        }
    }

    public function deleteAll()
    {
        $this->getDatabaseConnection()->exec_TRUNCATEquery(self::$table);
    }

    /**
     * @return TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
