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
                $msg_error .= '<br>'.implode(', ', $errors);
            }
            $response->status = JAuthentication::STATUS_FAILURE;
            $response->message = $msg_error;
            $app->redirect($login_url, $response->message, 'error');
        }
        
        //user is logged in, load the user
        $attrs = $saml_auth->getAttributes();

        $username = '';
        $email = '';
        $name = '';
        
        if (empty($attrs)) {
            $username = $saml_auth->getNameId();
            $email = $username;
        } else {
            $nameMapping = $plgParams->get('onelogin_saml_attr_mapping_name');
            $usernameMapping = $plgParams->get('onelogin_saml_attr_mapping_username');
            $mailMapping = $plgParams->get('onelogin_saml_attr_mapping_mail');
            $groupsMapping = $plgParams->get('onelogin_saml_attr_mapping_groups');
            if (!empty($usernameMapping) && isset($attrs[$usernameMapping]) && !empty($attrs[$usernameMapping][0])) {
                $username = $attrs[$usernameMapping][0];
            }
            if (!empty($mailMapping) && isset($attrs[$mailMapping]) && !empty($attrs[$mailMapping][0])) {
                $email = $attrs[$mailMapping][0];
            }
            if (!empty($nameMapping) && isset($attrs[$nameMapping]) && !empty($attrs[$nameMapping][0])) {
                $name = $attrs[$nameMapping][0];
            }
            if (!empty($groupsMapping) && isset($attrs[$groupsMapping]) && !empty($attrs[$groupsMapping])) {
                $saml_groups = $attrs[$groupsMapping];
            } else {
                $saml_groups = array();
            }
        }

        //pull in and verify our matching feild has data
        $matcher = $plgParams->get('onelogin_saml_account_matcher', 'username');


        if (empty($username) && $matcher == 'username') {
            $response->status = JAuthentication::STATUS_FAILURE;
            $response->message = 'NO_USERNAME';
            $app->redirect($login_url, $response->message, 'error');
        }
        if (empty($email) && $matcher == 'mail') {
            $response->status = JAuthentication::STATUS_FAILURE;
            $response->message = 'NO_MAIL';
            $app->redirect($login_url, $response->message, 'error');
        }

        //try to load the user from the joomla table
        $result = $saml_auth->get_user_from_joomla($matcher, $username, $email);

        if (!$result) {
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

                if (!defined('JPATH_COMPONENT')) {
                    define('JPATH_COMPONENT', JPATH_BASE . '/components/');
                }

                JModelLegacy::addIncludePath(JPATH_BASE . '/components/com_users/models');
                $model = JModelLegacy::getInstance('Registration', 'UsersModel');

                $return = $model->register($data);

                if ($return === false) {
                    $errors = $model->getErrors();
                    $response->status = JAuthentication::STATUS_FAILURE;
                    $response->message = 'USER NOT EXISTS AND FAILED THE CREATION PROCESS';
                    $app->redirect($login_url, $response->message, 'error');
                }

                $result = $saml_auth->get_user_from_joomla($matcher, $username, $email);

                $user = JUser::getInstance($result->id);

                $user->set('block', '0');
                $user->set('activation', '');
                $user->save();


                $groups = $saml_auth->get_mapped_groups($plgParams, $saml_groups);
                if (empty($groups)) {
                    $params = JComponentHelper::getParams('com_users');
                    // Get the default new user group, Registered if not specified.
                    $system = $params->get('new_usertype', 2);
                    $groups[] = $system;
                }

                $user->set('groups', $groups);
                $user->save();

                $response->status == JAuthentication::STATUS_SUCCESS;
                $session->set('user', $user);

                // SSO SAML Login flag
                $session->set('saml_login', 1);

                $app->redirect($login_url, "Welcome $user->username", 'message');
            } else {
                //User didn't exist and we're not allowed to create
                //generate error and redirect
                $response->status = JAuthentication::STATUS_FAILURE;
                $response->message = 'USER DOES NOT EXIST AND NOT ALLOWED TO CREATE';
                $app->redirect($login_url, $response->message, 'error');
            }
        } else {
            //we found the user, load the user
            $user = JUser::getInstance($result->id);

            // check if user data should be update
            $autoupdate = $plgParams->get('onelogin_saml_updateuser');

            if ($autoupdate) {
                /** @TODO Update */
                if (isset($name) && !empty($name)) {
                    $user->set('name', $name);
                    $user->save();
                }

                $groups = $saml_auth->get_mapped_groups($plgParams, $saml_groups);
                if (!empty($groups)) {
                    $user->set('groups', $groups);
                    $user->save();
                }
            }
            //we're done authenticating, set the user and session and redirect
            $response->status == JAuthentication::STATUS_SUCCESS;
            $session->set('user', $user);

            // SSO SAML Login flag
            $session->set('saml_login', 1);

            $app->redirect($login_url, "Welcome $user->username", 'message');
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
                'Invalid SP metadata: '.implode(', ', $errors),
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

    class PlgUserOneloginsaml extends JPlugin
    {
    /*
        public function onUserAuthenticate($credentials, $options, &$response)
        {
            // We redirect to initiate the SSO Login
            // $app = JFactory::getApplication();
            // $sso_url = JURI::base().'plugins/user/oneloginsaml/oneloginsaml.php?sso';
            // $app->redirect($sso_url);
        }
    */
        public function onUserLogout($parameters, $options)
        {
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
