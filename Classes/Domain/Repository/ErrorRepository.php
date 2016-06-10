<?php
namespace R3H6\Error404page\Domain\Repository;

use R3H6\Error404page\Domain\Model\Error;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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
        if ($endDate === null) {
            $endDate = new \DateTime('tomorrow');
        }
        if ($startDate === null) {
            $minTime = (new \DateTime('@' . $endDate->getTimestamp()))->modify('-1 month')->getTimestamp();
            /** @var TYPO3\CMS\Extbase\Persistence\Generic\Query $query */
            $query = $this->createQuery();
            $query->statement(sprintf('SELECT MIN(tstamp) AS minTime FROM %s', static::$table));
            $result = $query->execute(true);
            if (!empty($result)) {
                $minTime = max((int) $result[0]['minTime'], $minTime);
            }
            $startDate = new \DateTime('@' . $minTime);
        }

        /** @var TYPO3\CMS\Extbase\Persistence\Generic\Query $query */
        $query = $this->createQuery();
        $query->statement(sprintf('SELECT count(*) AS counter, DATE(FROM_UNIXTIME(tstamp)) AS dayDate FROM %s WHERE tstamp > %d AND tstamp < %d GROUP BY dayDate ORDER BY dayDate ASC', static::$table, $startDate->getTimestamp(), $endDate->getTimestamp()));

        // Fetch results
        $results = $query->execute(true);

        // Fill the gaps with empty entries
        $errors = [];
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($startDate, $interval, $endDate);
        $i = 0;
        foreach ($dateRange as $date) {
            $dayDate = $date->format('Y-m-d');
            if (isset($results[$i]) && $results[$i]['dayDate'] === $dayDate) {
                $errors[] = $results[$i];
                $i++;
            } else {
                $errors[] = [
                    'counter' => '0',
                    'dayDate' => $dayDate,
                ];
            }
        }

        return $errors;
    }

    /**
     * Returns eldest error
     *
     * @return R3H6\Error404page\Domain\Model\Error
     */
    public function findEldestError()
    {
        /** @var TYPO3\CMS\Extbase\Persistence\Generic\Query $query */
        $query = $this->createQuery();
        $query->setOrderings(['tstamp' => QueryInterface::ORDER_ASCENDING]);
        return $query->execute()->getFirst();
    }

    /**
     * @param $limit
     */
    public function findErrorTopReasons(\DateTime $startDate = null, $limit = 10)
    {
        // /** @var TYPO3\CMS\Extbase\Persistence\Generic\Query $query */
        // $query = $this->createQuery();
        // $query->statement(sprintf('SELECT reason, count(*) AS counter FROM %s GROUP BY reason ORDER BY counter DESC LIMIT %d', static::$table, $limit));
        // return $query->execute(true);
    }

    /**
     * @param $limit
     */
    public function findErrorTopUrls($limit = 10, \DateTime $startDate = null)
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
            $row = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('uid', self::$table, '1=1', '', 'tstamp ASC');
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
