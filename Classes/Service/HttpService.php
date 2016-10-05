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
 * Error handler
 *
 * This is a bridge class for using extbase dependency injection.
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

        /** @var \TYPO3\CMS\Core\Http\HttpRequest $request */
        $request = $this->getHttpRequest($url);

        try {
             /** @var \HTTP_Request2_Response $response */
            $response = $request->send();
            if ($response->getStatus() === 200) {
                $content = $response->getBody();
            }
        } catch (\Exception $exception) {
            // Ignore...
        }

        return $content;
    }

    public function isOwnRequest()
    {
        return GeneralUtility::_GP('tx_error404page_request') !== null;
    }

    /**
     * Creates a http request
     *
     * @param  string $url
     * @return \TYPO3\CMS\Core\Http\HttpRequest
     */
    protected function getHttpRequest($url)
    {
        $url .= (strpos($url, '?') === false) ? '?': '&';
        $url .= '&tx_error404page_request=' . uniqid();

        /** @var \TYPO3\CMS\Core\Http\HttpRequest $request */
        $request = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Http\\HttpRequest', $url);
        $feCookieName = $GLOBALS['TYPO3_CONF_VARS']['FE']['cookieName'];

        // Forward cookies.
        $request->setCookieJar(true);
        if (isset($_COOKIE[$feCookieName]) && !empty($_COOKIE[$feCookieName])) {
                $request->addCookie($feCookieName, $_COOKIE[$feCookieName]);
        }

        // TYPO3 uses user-agent for authentification.
        $request->setHeader('user-agent', GeneralUtility::getIndpEnv('HTTP_USER_AGENT'));

        // Set basic authentication.
        if ($this->extensionConfiguration->use('basicAuthentication')) {
            $basicAuthentication = GeneralUtility::trimExplode(':', $this->extensionConfiguration->get('basicAuthentication'), true);
            $request->setAuth($basicAuthentication[0], $basicAuthentication[1]);
        }

        return $request;
    }
}
