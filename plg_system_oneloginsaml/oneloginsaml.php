<?php
/**
 * @package     Joomla-Saml
 * @subpackage  plg_system_oneloginsaml
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author      Michael Andrzejewski<michael@jetskitechnologies.com>
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Load the OneLogin library and handle admin side injection
 * 
 */
class PlgSystemOneloginsaml extends \Joomla\CMS\Plugin\CMSPlugin {

    /**
     *
     * @var bool load the language autoloader
     * @since 1.7.0
     */
    protected $autoloadLanguage = true;

    /**
     * Register the library into the Joomla application
     * @since 1.7.0
     * @todo code to check for saml auth expiry
     * @todo code to handle force admin login via SAML
     */
    public function onAfterInitialise() {
        require_once JPATH_LIBRARIES . '/oneloginsaml/vendor/autoload.php';
        
        $app = Factory::getApplication();
        
        //check to see if we're doing a SAML function
        if ($app->input->get('option') == 'plg_onelogin') {
            
            //set constants if we are to stop the Dispatcher class from being a bitch
            define('JPATH_COMPONENT', JPATH_BASE . '/components/' . 'com_oneloginsaml');
            define('JPATH_COMPONENT_SITE', JPATH_SITE . '/components/' . 'com_oneloginsaml');
            define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/' . 'com_oneloginsaml');
        }
    }
    
    /**
     * Hack to allow for backend SSO
     * @since 1.7.0
     * @todo I hate having to add the SAMLLogin button client side. Investigate server side methods.
     */
    public function onBeforeRender() {
        if (Factory::getApplication()->isClient('administrator') && Factory::getUser()->guest) {

            $response = Factory::getDocument();
            $content = 'jQuery( document ).ready(function() {
                jQuery("button.login-button").parent().after("<a href=\"index.php?option=plg_onelogin&task=login.samlLogin\" class=\"btn btn-primary btn-block btn-large login-button\">'
                    . Text::_('SAML_LOGIN_BUTTON', true) . 
                    '</a>  ");
            });';
            $response->addScriptDeclaration($content);
        }
        if (Factory::getApplication()->isClient('administrator') && Factory::getSession()->get('saml_login')) {

            $response = Factory::getDocument();
            $content = 'jQuery( document ).ready(function() {
                jQuery("ul.nav-user li ul li:last-child a").attr("href", "/administrator/index.php?option=com_oneloginsaml&task=login.samlLogout");
            });';
            $response->addScriptDeclaration($content);
        }
    }

}
