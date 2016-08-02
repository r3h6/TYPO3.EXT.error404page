<?php
namespace R3H6\Error404page\Domain\Model;

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

/**
 * Error
 */
class Error extends \TYPO3\CMS\Extbase\DomainObject\AbstractValueObject
{
    const STATUS_CODE_NOT_FOUND = 404;
    const STATUS_CODE_FORBIDDEN = 403;

    /**
     * Timestamp
     *
     * @var integer
     */
    protected $timestamp = 0;

    /**
     * Url
     *
     * @var string
     * @validate NotEmpty
     */
    protected $url = '';

    /**
     * Root page
     *
     * @var int
     * @validate NotEmpty
     */
    protected $rootPage = 0;

    /**
     * Reason
     *
     * @var string
     * @validate NotEmpty
     */
    protected $reason = '';

    /**
     * Counter
     *
     * @var int
     * @validate NotEmpty
     */
    protected $counter = 0;

    /**
     * Last referer
     *
     * @var string
     */
    protected $referer = '';

    /**
     * IP
     *
     * @var string
     */
    protected $ip = '';

    /**
     * User agent
     *
     * @var string
     */
    protected $userAgent = '';

    /**
     * urlHash
     *
     * @var string
     */
    protected $urlHash = '';

    /**
     * Language
     *
     * @var integer
     */
    protected $_language = 0;

    /**
     * Status code
     *
     * @var integer
     */
    protected $_statusCode = self::STATUS_CODE_NOT_FOUND;

    /**
     * Doktype
     *
     * @var integer
     */
    protected $_doktype = null;

    /**
     * Gets the language
     *
     * @return integer
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * Sets the language
     *
     * @param integer $language
     */
    public function setLanguage($language)
    {
        $this->_language = $language;
    }

    /**
     * Returns the url
     *
     * @return string $url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the url
     *
     * @param string $url
     * @return void
     */
    public function setUrl($url)
    {
        $this->url = $url;
        $this->urlHash = sha1($url);
    }

    /**
     * Returns the reason
     *
     * @return string $reason
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Sets the reason
     *
     * @param string $reason
     * @return void
     */
    public function setReason($reason)
    {
        if (preg_match('/Cannot decode "([^"]+)"/si', $reason)) {
            $reason = 'Cannot decode path';
        } else if (preg_match('/Could not map alias "([^"]+)" to an id\\./si', $reason)) {
            $reason = 'Could not map alias to an id.';
        } else if (preg_match('/Segment "([^"]+)" was not a keyword for a postVarSet as expected on page with id=([0-9]+)\\./si', $reason, $matches)) {
            $reason = 'Segment was not a keyword for a postVarSet as expected on page';
            $this->pid = (int) $matches[2];
        }

        $extensionConfiguration = $this->getExtensionConfiguration();
        if ($extensionConfiguration->get('feature403') && $reason === 'ID was not an accessible page') {
            $this->_statusCode = self::STATUS_CODE_FORBIDDEN;
            $this->_doktype = (int) $extensionConfiguration->get('_doktypeError403page');
        } else {
            $this->_doktype = (int) $extensionConfiguration->get('_doktypeError404page');
        }

        $this->reason = $reason;
    }

    /**
     * Returns the ip
     *
     * @return int $ip
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Sets the ip
     *
     * @param int $ip
     * @return void
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Gets the counter
     *
     * @return int
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * Sets the counter
     *
     * @param int $counter
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;
    }

    /**
     * Returns the referer
     *
     * @return string referer
     */
    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * Sets the referer
     *
     * @param string $referer
     * @return void
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;
    }

    /**
     * Returns the rootPage
     *
     * @return int $rootPage
     */
    public function getRootPage()
    {
        return $this->rootPage;
    }

    /**
     * Sets the rootPage
     *
     * @param int $rootPage
     * @return void
     */
    public function setRootPage($rootPage)
    {
        $this->rootPage = $rootPage;
    }

    /**
     * Returns the userAgent
     *
     * @return string $userAgent
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Sets the userAgent
     *
     * @param string $userAgent
     * @return void
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * Gets the timestamp
     *
     * @return integer
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Sets the timestamp
     *
     * @param integer $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * Gets the statusCode
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    /**
     * Sets the statusCode
     *
     * @param integer $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->_statusCode = $statusCode;
    }

    public function getDoktype()
    {
        return $this->_doktype;
    }

    protected function getExtensionConfiguration()
    {
        return GeneralUtility::makeInstance(\R3H6\Error404page\Configuration\ExtensionConfiguration::class);
    }

    /**
     * Returns properties array
     *
     * @return array
     */
    public function toArray()
    {
        $properties = array();
        foreach ($this->_getProperties() as $key => $value) {
            $properties[GeneralUtility::camelCaseToLowerCaseUnderscored($key)] = $value;
        }
        $properties['tstamp'] = time();
        $properties['pid'] = (int) $this->pid;
        if ($this->_isNew()) {
            $properties['crdate'] = $properties['tstamp'];
        }
        unset($properties['uid']);
        unset($properties['timestamp']);
        return $properties;
    }

    /**
     * Returns the urlHash
     *
     * @return string $urlHash
     */
    public function getUrlHash()
    {
        return $this->urlHash;
    }
}
