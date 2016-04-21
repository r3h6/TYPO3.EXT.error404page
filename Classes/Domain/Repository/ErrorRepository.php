<?php
namespace R3H6\Error404page\Domain\Repository;

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

use R3H6\Error404page\Domain\Model\Error;

/**
 * The repository for Errors
 */
class ErrorRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    protected static $table = 'tx_error404page_domain_model_error';

    public function getCountPerDay()
    {

    }

    public function getReasons()
    {

    }

    public function getUrls()
    {
        $rows = $this->getDatabasConnection()->exec_SELECTgetRows('url, reason, crdate, count(*) AS counter', self::$table, '1=1', 'url, reason');
        return $rows;
    }

    public function log($url, $reason, $lastReferer)
    {
        $sha1 = sha1($url . '/' . $reason);


        // $error = $this->getDatabasConnection()->exec_SELECTgetSingleRow('counter', self::$table, "sha1='$sha1'");
        //if (!is_array($error)) {
            $error = [
                'sha1' => $sha1,
                'url' => $url,
                'reason' => $reason,
                'counter' => 0,
            ];
        //}
        $error['last_referer'] = $lastReferer;
        $error['crdate'] = time();
        // $error['counter'] = intval($error['counter']) + 1;

        // if ($error['counter'] === 1) {
            $this->getDatabasConnection()->exec_INSERTquery(self::$table, $error);
        // } else {
            // $this->getDatabasConnection()->exec_UPDATEquery(self::$table, "sha1='$sha1'", $error);
        // }

        /** @var R3H6\Error404page\Domain\Model\Error $error */
        // $error = $this->findOneBySha1($sha1);
        // if ($error === null) {
        //     $error = $this->objectManager->get(Error::class);
        //     $error->setSha1($sha1);
        //     $error->setUrl($url);
        //     $error->setReason($reason);
        // }
        // $error->setCounter($error->getCounter() + 1);
        // $error->setLastReferer($lastReferer);

        // if ($error->getUid() !== null) {
        //     $this->update($error);
        // } else {
        //     $this->add($error);
        // }
    }

    /**
     * @return TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabasConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
