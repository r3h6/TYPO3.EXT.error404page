<?php
namespace R3H6\Error404page\Domain\Model;

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
 * Error
 */
class Error extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * IP
     *
     * @var int
     */
    protected $ip = 0;

    /**
     * Sha1
     *
     * @var string
     * @validate NotEmpty
     */
    protected $sha1 = '';

    /**
     * Url
     *
     * @var string
     * @validate NotEmpty
     */
    protected $url = '';

    /**
     * Reason
     *
     * @var string
     * @validate NotEmpty
     */
    protected $reason = '';

    /**
     * Last referer
     *
     * @var string
     */
    protected $lastReferer = '';

    /**
     * Counter
     *
     * @var int
     * @validate NotEmpty
     */
    protected $counter = 0;

    /**
     * Returns the sha1
     *
     * @return string $sha1
     */
    public function getSha1()
    {
        return $this->sha1;
    }

    /**
     * Sets the sha1
     *
     * @param string $sha1
     * @return void
     */
    public function setSha1($sha1)
    {
        $this->sha1 = $sha1;
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
        $this->reason = $reason;
    }

    /**
     * Returns the lastReferer
     *
     * @return string $lastReferer
     */
    public function getLastReferer()
    {
        return $this->lastReferer;
    }

    /**
     * Sets the lastReferer
     *
     * @param string $lastReferer
     * @return void
     */
    public function setLastReferer($lastReferer)
    {
        $this->lastReferer = $lastReferer;
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
     * @return int [description]
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * Sets the counter
     *
     * @param int $counter [description]
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;
    }
}