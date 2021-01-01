<?php
/**
 * @package     Joomla-Saml
 * @subpackage  plg_content_oneloginsaml
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author      Michael Andrzejewski<michael@jetskitechnologies.com>
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Overrides the default logout links and makes login links
 */
class PlgContentOneloginsaml extends Joomla\CMS\Plugin\CMSPlugin {

    /**
     * Grabs the login module and injects a samlLogin button
     * @param Object $module A reference to a Module object that holds all the data of the module
     * @param JRegistry $params Registry of module params
     * @since 1.7.0
     * @todo figure out what class is actually passed to $module
     */
    public function onRenderModule(&$module, &$params) {
        $session = Factory::getSession();
        if ($module->module == 'mod_login') {

            $loginFormMarkup = new SimpleXMLElement($module->content);
            if ($session->get('saml_login', 0)) {
                $loginFormMarkup->xpath('//input[@name=\'option\']')[0]->attributes()->value = 'com_oneloginsaml';
                $loginFormMarkup->xpath('//input[@name=\'task\']')[0]->attributes()->value = 'samlLogout';
            } else {
                $htmlControls = $loginFormMarkup->xpath('//button/parent::*')[0];
                $newLink = $htmlControls->addChild('a', Text::_('SAML_LOGIN_BUTTON'));
                $newLink->addAttribute('href', 'index.php?option=com_oneloginsaml&task=samlLogin');
                $newLink->addAttribute('class', 'btn btn-primary');
            }
            $module->content = $loginFormMarkup->asXML();
        }
    }
}
