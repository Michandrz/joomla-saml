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
        //IDP response
        $saml_auth->processResponse();

        //import the user authentication class
        jimport('joomla.user.authentication');
        $authenticate = JAuthentication::getInstance();
        $response = new JAuthenticationResponse();

        //check to see if the user is authenticated to the idp
        if (!$saml_auth->isAuthenticated()) {
            //user is not generate an error and redirect
            $msg_error = 'NO_AUTHENTICATED';
            $errors = $saml_auth->getErrors();

            if (!empty($errors) && $debug) {
                $msg_error .= '<br>' . implode(', ', $errors);
            }
            $response->status = JAuthentication::STATUS_FAILURE;
            $response->message = $msg_error;
            $app->redirect($login_url, $response->message, 'error');
        }

        //Load IDP attrs
        $attrs = $saml_auth->getAttributes();
        
        //Load the the OneloginsamlModelUser
        if (!defined('JPATH_COMPONENT')) {
            define('JPATH_COMPONENT', JPATH_BASE . '/components/');
        }
        JModelLegacy::addIncludePath(JPATH_BASE . '/administrator/components/com_oneloginsaml/models');
        /**
         * @var  oneloginsamlModelUser
         */
        $oneloginUserModel = JModelLegacy::getInstance('User', 'oneloginsamlModel');
        
        //populate the matcher
        $oneloginUserModel->setMatcher($attrs);
        
        //try to load the user
        $loadedUser = $oneloginUserModel->getUser();
        

        if (!is_a($loadedUser, "\Joomla\CMS\User\User")) {
            // User not found, check if could be created
            $autocreate = $plgParams->get('onelogin_saml_autocreate');

            if ($autocreate) {
                if (empty($username)) {
                    $username = $email;
                }

                // user data
                $data['name'] = (isset($name) && !empty($name)) ? $name : $username;
                $data['username'] = $username;
                $data['email'] = $data['email1'] = $data['email2'] = JStringPunycode::emailToPunycode($email);
                $data['password'] = $data['password1'] = $data['password2'] = NULL;

                // Get the model and validate the data.
                jimport('joomla.application.component.model');

                JModelLegacy::addIncludePath(JPATH_BASE . '/components/com_users/models');
                $model = JModelLegacy::getInstance('Registration', 'UsersModel');

                $return = $model->register($data);

                if ($return === false) {
                    $errors = $model->getErrors();
                    $response->status = JAuthentication::STATUS_FAILURE;
                    $response->message = 'USER NOT EXISTS AND FAILED THE CREATION PROCESS';
                    $app->redirect($login_url, $response->message, 'error');
                }
                
                $loadedUser = $oneloginUserModel->getUser();

                $loadedUser->set('block', '0');
                $loadedUser->set('activation', '');
                $loadedUser->save();

                $oneloginUserModel->processAttributes($loadedUser, $attrs);
                $oneloginUserModel->setGroups($loadedUser, $attrs);

                $response->status == JAuthentication::STATUS_SUCCESS;
                $session->set('user', $loadedUser);

                // SSO SAML Login flag
                $session->set('saml_login', 1);

                $app->redirect($login_url, "Welcome $loadedUser->username", 'message');
            } else {
                //User didn't exist and we're not allowed to create
                //generate error and redirect
                $response->status = JAuthentication::STATUS_FAILURE;
                $response->message = 'USER DOES NOT EXIST AND NOT ALLOWED TO CREATE';
                $app->redirect($login_url, $response->message, 'error');
            }
        } else {
            
            // check if user data should be update
            $autoupdate = $plgParams->get('onelogin_saml_updateuser');

            if ($autoupdate) {
                $oneloginUserModel->processAttributes($loadedUser, $attrs);
                $oneloginUserModel->setGroups($loadedUser, $attrs);
            }
            //we're done authenticating, set the user and session and redirect
            $response->status == JAuthentication::STATUS_SUCCESS;
            $session->set('user', $loadedUser);

            // SSO SAML Login flag
            $session->set('saml_login', 1);

            $app->redirect($login_url, "Welcome $loadedUser->username", 'message');
        }
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

    class PlgUserOneloginsaml extends JPlugin {
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
