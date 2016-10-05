<?php
namespace R3H6\Error404page\Facade;

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
 * FrontendController
 *
 * Facade for TypoScriptFrontendController (TSFE)
 */
class FrontendController
{
    /**
     * TypoScriptFrontendController
     *
     * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected $typoScriptFrontendController;

    public function __construct()
    {
        $this->typoScriptFrontendController = $GLOBALS['TSFE'];
    }

    public function getType()
    {
        $this->initialize();
        return (int) $this->typoScriptFrontendController->type;
    }

    public function isDefaultType()
    {
        return $this->getType() === 0;
    }

    public function getSystemLanguageUid()
    {
        $this->initialize();
        return (int) $this->typoScriptFrontendController->sys_language_uid;
    }

    public function isDefaultLanguage()
    {
        return $this->getSystemLanguageUid() === 0;
    }

    public function typoLink(array $parameters)
    {
        $this->initialize();
        return $this->typoScriptFrontendController->cObj->typoLink_URL($parameters);
    }

    public function isDefaultGetVar($getVar)
    {
        $this->initialize();
        return isset($this->typoScriptFrontendController->config['config']['defaultGetVars.'][$getVar]);
    }

    protected function initialize()
    {
        if (!is_object($this->typoScriptFrontendController->cObj)) {
            $this->typoScriptFrontendController->initTemplate();
            $this->typoScriptFrontendController->getConfigArray();
            $this->typoScriptFrontendController->settingLanguage();
            $this->typoScriptFrontendController->newCObj();
        }
    }
}
