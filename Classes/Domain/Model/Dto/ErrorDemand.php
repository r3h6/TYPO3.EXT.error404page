<?php
namespace R3H6\Error404page\Domain\Model\Dto;

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
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * ErrorDemand
 */
class ErrorDemand extends \TYPO3\CMS\Extbase\DomainObject\AbstractValueObject
{
    const TYPE_GROUPED_BY_DAY = 'ErrorGroupedByDay';
    const TYPE_TOP_URLS = 'ErrorTopUrls';
    const DEFAULT_LIMIT = 50;


    const TIME_ONE_WEEK_AGO = 'midnight -1 week';
    const TIME_ONE_MONTH_AGO = 'midnight -1 month';
    const TIME_ONE_YEAR_AGO = 'midnight -1 year';

    /**
     * ErrorRepository
     *
     * @var \R3H6\Error404page\Domain\Repository\ErrorRepository
     * @inject
     */
    protected $_errorRepository = null;

    /**
     * [$minTime description]
     * @var integer
     */
    protected $minTime = 0;

    /**
     * [$url description]
     * @var string
     */
    protected $url = '';

    /**
     * [$type description]
     * @var string
     */
    protected $type = '';

    /**
     * [$limit description]
     * @var integer
     */
    protected $limit = self::DEFAULT_LIMIT;

    /**
     * [$urlHash description]
     * @var string
     */
    protected $urlHash = '';

    /**
     * Gets the minTime
     *
     * @return integer
     */
    public function getMinTime()
    {
        return $this->minTime;
    }

    /**
     * Sets the minTime
     *
     * @param integer
     */
    public function setMinTime($minTime)
    {
        $this->minTime = $minTime;
    }

    /**
     * Gets the url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the url
     *
     * @param string
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Gets the type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param string
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Gets the limit
     *
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Sets the limit
     *
     * @param integer $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * Gets the urlHash
     *
     * @return string
     */
    public function getUrlHash()
    {
        return $this->urlHash;
    }

    /**
     * Sets the urlHash
     *
     * @param string $urlHash
     */
    public function setUrlHash($urlHash)
    {
        $this->urlHash = $urlHash;
    }

    /**
     * Returns the date options
     *
     * @return array options for select
     */
    public function getMinDateOptions()
    {
        $eldestError = $this->_errorRepository->findEldestError();

        $options = [];

        if ($eldestError !== null) {
            $minTime = strtotime(self::TIME_ONE_WEEK_AGO);
            if ($eldestError->getTimestamp() < $minTime) {
                $options[$minTime] = LocalizationUtility::translate('tx_error404page_domain_model_dto_errordemand.min_date.7', 'error404page');
            }
            $minTime = strtotime(self::TIME_ONE_MONTH_AGO);
            if ($eldestError->getTimestamp() < $minTime) {
                $options[$minTime] = LocalizationUtility::translate('tx_error404page_domain_model_dto_errordemand.min_date.31', 'error404page');
            }
            $minTime = strtotime(self::TIME_ONE_YEAR_AGO);
            if ($eldestError->getTimestamp() < $minTime) {
                $options[$minTime] = LocalizationUtility::translate('tx_error404page_domain_model_dto_errordemand.min_date.365', 'error404page');
            }
        }
        $options[0] = LocalizationUtility::translate('tx_error404page_domain_model_dto_errordemand.min_date.0', 'error404page');
        return $options;
    }
}
