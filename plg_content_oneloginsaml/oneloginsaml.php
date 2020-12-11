<?php

/**
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author Michael Andrzejewski <michael@jetskitechnologies.com>
 */
use \Joomla\CMS\Factory;
use \Joomla\CMS\User\User;

/**
 * Overrides the default logout links and makes login links
 */
class PlgContentOneloginsaml extends \Joomla\CMS\Plugin\CMSPlugin {

    public function onRenderModule(&$module, &$params) {
        $session = Factory::getSession();
        if ($module->module == 'mod_login') {

            $loginFormMarkup = new SimpleXMLElement($module->content);
            if ($session->get('saml_login', 0)) {
                $loginFormMarkup->xpath('//input[@name=\'option\']')[0]->attributes()->value = 'com_oneloginsaml';
                $loginFormMarkup->xpath('//input[@name=\'task\']')[0]->attributes()->value = 'samlLogout';
            } else {
                $htmlControls = $loginFormMarkup->xpath('//button/parent::*')[0];
                $newLink = $htmlControls->addChild('a', 'SSO Log in');
                $newLink->addAttribute('href', 'index.php?option=com_oneloginsaml&task=samlLogin');
                $newLink->addAttribute('class', 'btn btn-primary');
            }
            $module->content = $loginFormMarkup->asXML();
        }
    }

}
