<?php

/**
 *  @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 *  @license     MIT
 *  @author      Michael Andrzejewski<michael@jetskitechnologies.com>
 */
use \Joomla\CMS\Authentication\Authentication;
use \Joomla\CMS\Factory;

/**
 * Description of plgAuthenticationOneloginsaml
 */
class PlgAuthenticationOneloginsaml extends Joomla\CMS\Plugin\CMSPlugin {

    /**
     * This method should handle any authentication and report back to the subject
     *
     * @param   array   $credentials  Array holding the user credentials
     * @param   array   $options      Array of extra options
     * @param   Joomla\CMS\Authentication\AuthenticationResponse  &$response    Authentication response object
     *
     * @return  void
     *
     * @since   1.5
     */
    public function onUserAuthenticate(&$credentials, $options, &$response) {
        //if the library is not present, no login
        if (is_a($credentials['oneLoginSAML'], 'OneLogin_Saml2_Auth')) {
            $saml_lib = $credentials['oneLoginSAML'];

            //bring in the configuration
            JModelLegacy::addIncludePath(JPATH_BASE . '/administrator/components/com_oneloginsaml/models');
            $params = JModelLegacy::getInstance('Config', 'oneloginsamlModel')->getPluginParams();
            $debug = $params->get('onelogin_saml_advanced_settings_debug');

            if ($saml_lib->isAuthenticated()) {
                $attrs = $saml_lib->getAttributes();

                //we're authenticed to the IDP, lets load the User data model
                $oneloginUserModel = JModelLegacy::getInstance('User', 'oneloginsamlModel');

                //populate the matcher
                $oneloginUserModel->setMatcher($attrs);
                //try to load the user
                $loadedUser = $oneloginUserModel->getUser();

                if (is_a($loadedUser, "\Joomla\CMS\User\User")) {
                    //user exists
                    if ($params->get('onelogin_saml_updateuser', false, 'boolean')) {
                        $oneloginUserModel->processAttributes($loadedUser, $attrs);
                        $oneloginUserModel->setGroups($loadedUser, $attrs);
                        $loadedUser->save();
                    }

                    if (JFactory::getApplication()->isClient('administrator')) {
                        $response->language = $loadedUser->getParam('admin_language');
                    } else {
                        $response->language = $loadedUser->getParam('language');
                    }
                    $response->email = $loadedUser->email;
                    $response->fullname = $loadedUser->name;
                    $response->username = $loadedUser->username;
                    $response->status = Authentication::STATUS_SUCCESS;
                    $response->error_message = null;

                    $session = Factory::getSession();
                    $session->set('user', $loadedUser);
                    $session->set('saml_login_expire', $saml_lib->getSessionExpiration());
                    $session->set('saml_login', 1);
                } elseif ($params->get('onelogin_saml_autocreate', false, 'boolean')) {
                    //user doesn't exist, but we can create it
                    $loadedUser = $oneloginUserModel->createUser($attrs);
                    if ($loadedUser === false) {
                        $response->status = Authentication::STATUS_DENIED;
                        $response->message = 'USER NOT EXISTS AND FAILED TO CREATE';
                    } else {
                        $response->email = $loadedUser->email;
                        $response->fullname = $loadedUser->name;
                        $response->username = $loadedUser->username;

                        if (JFactory::getApplication()->isClient('administrator')) {
                            $response->language = $loadedUser->getParam('admin_language');
                        } else {
                            $response->language = $loadedUser->getParam('language');
                        }
                        $response->email = $loadedUser->email;
                        $response->fullname = $loadedUser->name;
                        $response->username = $loadedUser->username;
                        $response->status = Authentication::STATUS_SUCCESS;
                        $response->error_message = null;

                        $session = Factory::getSession();
                        $session->set('user', $loadedUser);
                        $session->set('saml_login_expire', $saml_lib->getSessionExpiration());
                        $session->set('saml_login', 1);
                    }
                } else {
                    $response->status = Authentication::STATUS_DENIED;
                    $response->message = 'USER NOT EXISTS AND NOT ALLOWED TO CREATE';
                }
            } else {
                $response->status = Authentication::STATUS_FAILURE;
                $errors = $saml_lib->getErrors();
                if (!empty($errors) && $debug) {
                    $msg_error .= '<br>' . implode(', ', $errors);
                }
                $response->message = $msg_error;
            }
        }
        $response->status = Authentication::STATUS_UNKNOWN;
    }

}
