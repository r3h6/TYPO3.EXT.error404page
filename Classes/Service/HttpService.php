<?php

namespace R3H6\Error404page\Service;

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
use TYPO3\CMS\Core\Utility\HttpUtility;

/**
 * HttpService
 */
class HttpService implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var \R3H6\Error404page\Configuration\ExtensionConfiguration
     * @inject
     */
    protected $extensionConfiguration;

    /**
     * Redirect to url.
     *
     * @param  string $url [description]
     */
    public function redirect($url)
    {
        HttpUtility::redirect($url);
    }

    /**
     * Reads and returns the content of the url.
     *
     * @param  string $url
     * @return string
     */
    public function readUrl($url)
    {
        $content = null;
        $url = $this->appendSignature($url);

        try {
            /** @var \Psr\Http\Message\ResponseInterface $response */
            $response = $this->getRequestFactory()->request($url, 'GET', $this->getRequestOptions());
            if ($response->getStatusCode() !== 200) {
                throw new \Exception($response->getReasonPhrase(), 1478293539);
            }
            $content = $response->getBody()->getContents();
        } catch (\Exception $exception) {
            $this->getLogger()->debug('Could not read url "' . $url . '" ' . $exception->getMessage());
        }

        return $content;
    }

    public function isOwnRequest()
    {
        return GeneralUtility::_GP('tx_error404page_request') !== null;
    }

    protected function appendSignature($url)
    {
        $url .= (strpos($url, '?') === false) ? '?': '&';
        $url .= 'tx_error404page_request=' . uniqid();
        return $url;
    }

    protected function getRequestOptions()
    {
        $options = [];

        // Forward cookies.
        $cookieJar = new \GuzzleHttp\Cookie\CookieJar(true);
        $feCookieName = $GLOBALS['TYPO3_CONF_VARS']['FE']['cookieName'];
        if (isset($_COOKIE[$feCookieName]) && !empty($_COOKIE[$feCookieName])) {
            $feCookie = new \GuzzleHttp\Cookie\SetCookie();
            $feCookie->setDomain(GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY'));
            $feCookie->setName($feCookieName);
            $feCookie->setValue($_COOKIE[$feCookieName]);
            $cookieJar->setCookie($feCookie);
            $options['cookies'] = $cookieJar;
        }

        // TYPO3 uses user-agent for authentification.
        $options['user-agent'] = GeneralUtility::getIndpEnv('HTTP_USER_AGENT');

        // Set basic authentication.
        if ($this->extensionConfiguration->has('basicAuthentication')) {
            $basicAuthentication = GeneralUtility::trimExplode(':', $this->extensionConfiguration->get('basicAuthentication'), true);

            $options['auth'] = [
                $basicAuthentication[0],
                $basicAuthentication[1],
            ];
        }

        return $options;
    }

    /**
     * [getRequestFactory description]
     *
     * @return \TYPO3\CMS\Core\Http\RequestFactory
     */
    protected function getRequestFactory()
    {
        return GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Http\\RequestFactory');
    }

    /**
     * Get class logger
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger()
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Log\\LogManager')->getLogger(__CLASS__);
    }
}