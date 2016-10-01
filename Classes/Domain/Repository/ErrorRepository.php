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

use R3H6\Error404page\Domain\Model\Dto\ErrorDemand;

/**
 * The repository for Errors
 */
class ErrorRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    const MAX_ENTRIES = 10000;

    protected static $table = 'tx_error404page_domain_model_error';

    private $isConsistent = null;

    public function initializeObject()
    {
        /** @var $querySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
        $querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $querySettings->setRespectStoragePage(false);
        $querySettings->setRespectSysLanguage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @param ErrorDemand $demand
     */
    public function findDemanded(ErrorDemand $demand)
    {
        if (!$this->isConsistent()) {
            return null;
        }
        $minDate = $demand->getMinTime() ? new \DateTime('@' . $demand->getMinTime()) : null;
        switch ($demand->getType()) {
            case ErrorDemand::TYPE_GROUPED_BY_DAY:
                return $this->findErrorGroupedByDay($minDate, null, $demand->getUrlHash());
            case ErrorDemand::TYPE_TOP_URLS:
                return $this->findErrorTopUrls($demand->getLimit(), $minDate);
        }
        return null;
    }

    protected function normalizeDates(&$startDate, &$endDate)
    {
        if ($endDate === null) {
            $endDate = new \DateTime('tomorrow midnight');
        }
        if ($startDate === null) {
            $minTime = $endDate->getTimestamp() - 86400;
            /** @var \R3H6\Error404page\Domain\Model\Error */
            $error = $this->findEldestError();
            if ($error !== null) {
                $minTime = min($error->getTimestamp(), $minTime);
            }
            $startDate = new \DateTime('@' . $minTime);
        }
    }

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     */
    public function findErrorGroupedByDay(\DateTime $startDate = null, \DateTime $endDate = null, $urlHash = null)
    {
        $this->normalizeDates($startDate, $endDate);
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Query $query */
        $query = $this->createQuery();
        $range = $endDate->diff($startDate);
        if ($range->days > 180) {
            $timeFormat = '%Y.%m';
            $interval = new \DateInterval('P1M');
        } else {
            $timeFormat = '%Y.%m.%d';
            $interval = new \DateInterval('P1D');
        }

        $where = sprintf('tstamp > %d AND tstamp < %d', $startDate->getTimestamp(), $endDate->getTimestamp());
        if ($urlHash) {
            $where .= ' AND url_hash="' . $this->getDatabaseConnection()->quoteStr($urlHash, static::$table) . '"';
        }

        $query->statement(sprintf('SELECT count(*) AS counter,  DATE_FORMAT(FROM_UNIXTIME(tstamp), "%s") AS timeUnit FROM %s WHERE %s GROUP BY timeUnit ORDER BY timeUnit ASC', $timeFormat, static::$table, $where));
        // Fetch results
        $results = $query->execute(true);
        // Fill the gaps with empty entries
        $errors = array();
        $shift = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($startDate->add($shift), $interval, $endDate->add($shift));
        $i = 0;
        $timeFormat = str_replace('%', '', $timeFormat);
        foreach ($dateRange as $date) {
            $timeUnit = $date->format($timeFormat);
            if (isset($results[$i]) && $results[$i]['timeUnit'] === $timeUnit) {
                $errors[] = $results[$i];
                $i++;
            } else {
                $errors[] = array(
                    'counter' => '0',
                    'timeUnit' => $timeUnit
                );
            }
        }
        return $errors;
    }

    /**
     * Returns eldest error
     *
     * @return \R3H6\Error404page\Domain\Model\Error
     */
    public function findEldestError()
    {
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Query $query */
        $query = $this->createQuery();
        $query->setOrderings(array('tstamp' => QueryInterface::ORDER_ASCENDING));
        return $query->execute()->getFirst();
    }

    /**
     * @param \DateTime $startDate
     * @param $limit
     */
    public function findErrorTopReasons(\DateTime $startDate = null, $limit = 10)
    {

    }

    /**
     * @param $limit
     * @param \DateTime $startDate
     */
    public function findErrorTopUrls($limit = 10, \DateTime $startDate = null, \DateTime $endDate = null)
    {
        $this->normalizeDates($startDate, $endDate);
        $where = sprintf('tstamp > %d AND tstamp < %d', $startDate->getTimestamp(), $endDate->getTimestamp());

        $sql = sprintf('SELECT url_hash AS urlHash, url, count(*) AS counter FROM %s WHERE %s GROUP BY urlHash ORDER BY counter DESC LIMIT %d', static::$table, $where, $limit);

        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Query $query */
        $query = $this->createQuery();
        $query->statement($sql);
        return $query->execute(true);
    }

    /**
     * Checks if db fields are consistent
     *
     * @return boolean
     */
    public function isConsistent()
    {
        if ($this->isConsistent === null) {
            /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Query $query */
            $query = $this->createQuery();
            $query->matching(
                $query->logicalOr(
                    $query->equals('urlHash', null),
                    $query->equals('urlHash', '')
                )
            );
            try {
                $this->isConsistent = $query->count() === 0;
            } catch (\Exception $exception) {
                $this->isConsistent = false;
            }
        }
        return $this->isConsistent;
    }

    /**
     * Log error.
     *
     * @param \R3H6\Error404page\Domain\Model\Error $error
     */
    public function log(Error $error)
    {
        $values = $error->toArray();
        $this->getDatabaseConnection()->debugOutput = false;
        $count = $this->getDatabaseConnection()->exec_SELECTcountRows('*', self::$table);
        if ($count < self::MAX_ENTRIES) {
            $this->getDatabaseConnection()->exec_INSERTquery(self::$table, $values);
            if ($this->getDatabaseConnection()->sql_errno()) {
                unset($values['url_hash']);
                $this->getDatabaseConnection()->exec_INSERTquery(self::$table, $values);
            }
        } else {
            $row = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('uid', self::$table, '1=1', '', 'tstamp ASC');
            $this->getDatabaseConnection()->exec_UPDATEquery(self::$table, 'uid=' . $row['uid'], $values);
            if ($this->getDatabaseConnection()->sql_errno()) {
                unset($values['url_hash']);
                $this->getDatabaseConnection()->exec_UPDATEquery(self::$table, 'uid=' . $row['uid'], $values);
            }
        }
    }

    public function deleteAll()
    {
        $this->getDatabaseConnection()->exec_TRUNCATEquery(self::$table);
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

}