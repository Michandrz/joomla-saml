<?php

/**
 * @package     OneLogin SAML.Plugin
 * @subpackage  User.oneloginsaml
 *
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 */
/**
 * Check to see if we're being called directly or in the application.
 */
if (!defined('_JEXEC')) {
    /**
     * We're being called directly, load the Joomla Env
     */
    define('_JEXEC', 1);

    if (!defined('_JDEFINES')) {
        define('JPATH_BASE', dirname(dirname(dirname(dirname(__FILE__)))));
        require_once JPATH_BASE . '/includes/defines.php';
    }


    require_once JPATH_BASE . '/includes/framework.php';

    // Mark afterLoad in the profiler.
    JDEBUG ? $_PROFILER->mark('afterLoad') : null;

    // Instantiate the application.
    $app = JFactory::getApplication('site');
    $app->initialise();
    JPluginHelper::importPlugin('system');
    $dispatcher = JEventDispatcher::getInstance();
    $dispatcher->trigger('onAfterInitialise');
    $login_url = JRoute::_('../../../index.php?option=com_users&view=login', true);

    //try to load the plugin
    $oneLoginPlugin = JPluginHelper::getPlugin('user', 'oneloginsaml');

    //sanity check
    if (!$oneLoginPlugin) {
        throw new Exception("Onelogin SAML Plugin not active");
    }

    //import user and session info
    $user = JFactory::getUser();
    $session = JFactory::getSession();

    //load plugin parameters
    jimport('joomla.html.parameter');
    $plgParams = new JRegistry();
    if ($oneLoginPlugin && isset($oneLoginPlugin->params)) {
        $plgParams->loadString($oneLoginPlugin->params);
    }

    //see if we're in debug mode
    $debug = $plgParams->get('onelogin_saml_advanced_settings_debug');

    //import the onelogin library and pass the settings
    $saml_auth = new OneLogin_Saml2_Auth_Joomla($plgParams);

    //decide what direct function is required
    if (isset($_GET['sso'])) {
        //login required redirect to the idp
        $saml_auth->login();
    } else if (isset($_GET['slo'])) {
        //logout required redirect to the idp
        $saml_auth->logout(); 
    } else if (isset($_GET['acs'])) {
        //Moved to Component
    } else if (isset($_GET['sls'])) {
        // logout 
        $saml_auth->processSLO();
        $errors = $saml_auth->getErrors();
        if (empty($errors)) {
            /** @TODO Do local logout */
            $app->redirect($login_url, 'Sucessfully logged out', 'message');
        } else {
            $app->redirect($login_url, implode(', ', $errors), 'error');
        }
    } else if (isset($_GET['metadata'])) {
        //print our settings and exit
        $settings = $saml_auth->getSettings();
        $metadata = $settings->getSPMetadata();
        $errors = $settings->validateMetadata($metadata);
        if (empty($errors)) {
            header('Content-Type: text/xml');
            echo $metadata;
        } else {
            throw new OneLogin_Saml2_Error(
                    'Invalid SP metadata: ' . implode(', ', $errors),
                    OneLogin_Saml2_Error::METADATA_SP_INVALID
            );
        }
    } else {
        //we were't given a valid todo, throw an error
        throw new Exception("No action selected, set one of those GET parameters: 'sso', 'slo', 'acs', 'sls' or 'metadata' .");
    }
} else {

    // We can't use the normal login process. We don't require user to click on Login
    // Joomla form providing credentials

    class PlgUserOneloginsaml extends \Joomla\CMS\Plugin\CMSPlugin {
        /*
          public function onUserAuthenticate($credentials, $options, &$response)
          {
          // We redirect to initiate the SSO Login
          // $app = JFactory::getApplication();
          // $sso_url = JURI::base().'plugins/user/oneloginsaml/oneloginsaml.php?sso';
          // $app->redirect($sso_url);
          }
         */
        /**
         * 
         * @param \Joomla\CMS\User\User $user
         * @param string $key the attribute name to be updated
         * @param string $value the attribute value to be updated
         */
        public function onUserUpdateInfo($user, $key, $value ) {
            switch($key) {
                case 'Username':
                    $user->username = $value;
                    break;
                case 'Email' :
                    $user->email = $value;
                    break;
                case 'Name' :
                    $user->name = $value;
                    break;
            }
            $user->save();
        }
        public function onUserLogout($parameters, $options) {
            if ($this->params->get('onelogin_saml_slo')) {

                $session = JFactory::getSession();

                if ($session->get('saml_login')) {
                    $saml_auth = new OneLogin_Saml2_Auth_Joomla($this->params);
                    $saml_auth->logout();
                }
            }
        }

    }

}
