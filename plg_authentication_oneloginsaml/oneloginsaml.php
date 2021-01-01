<?php
/**
 * @package     Joomla-Saml
 * @subpackage  plg_authentication_oneloginsaml
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author      Michael Andrzejewski<michael@jetskitechnologies.com>
 * @author      Sixto Martin <pitbulk@gmail.com>
 */
use Joomla\CMS\Authentication\Authentication;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel; 

/**
 * Joomla plugin to process the saml authentication
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
     * @since   1.7.0
     */
    public function onUserAuthenticate(&$credentials, $options, &$response) {
        //if the library is not present, no login
        if (is_a($credentials['oneLoginSAML'], 'Onelogin\\Saml2\\samlJoomla')) {
            $saml_lib = $credentials['oneLoginSAML'];

            //bring in the configuration
            $debug = $this->params->get('debug', 0);

            if ($saml_lib->isAuthenticated()) {
                $attrs = $saml_lib->getAttributes();

                //we're authenticed to the IDP, lets load the User data model
                BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_oneloginsaml/models');
                $oneloginUserModel = BaseDatabaseModel::getInstance('User', 'oneloginsamlModel');

                //populate the matcher
                $oneloginUserModel->setMatcher($attrs);
                //try to load the user
                $loadedUser = $oneloginUserModel->getUser();

                if (is_a($loadedUser, "\Joomla\CMS\User\User")) {
                    //user exists
                    if ($this->params->get('updateuser', false, 'boolean')) {
                        $oneloginUserModel->processAttributes($loadedUser, $attrs)
                                ->setGroups($loadedUser, $attrs);
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
                    
                    if(is_numeric($saml_lib->getSessionExpiration())) {

                        $session->set('saml_login_expire', $saml_lib->getSessionExpiration());
                    } else {
                        $session->set('saml_login_expire', time() + $this->params->get('default_session_time', 3600));
                    }
                    $session->set('saml_login', 1);
                    return;
                } elseif ($this->params->get('autocreate', false, 'boolean')) {
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

                        if (is_numeric($saml_lib->getSessionExpiration())) {

                            $session->set('saml_login_expire', $saml_lib->getSessionExpiration());
                        } else {
                            $session->set('saml_login_expire', time() + $this->params->get('default_session_time', 3600));
                        }
                        $session->set('saml_login', 1);
                        return;
                    }
                } else {
                    $response->status = Authentication::STATUS_DENIED;
                    $response->message = 'USER_NOT_EXISTS_AND_NOT ALLOWED TO CREATE';
                    return;
                }
            } else {
                $response->status = Authentication::STATUS_FAILURE;
                $errors = $saml_lib->getErrors();
                if (!empty($errors) && $debug) {
                    $msg_error .= '<br>' . implode(', ', $errors);
                }
                $response->message = $msg_error;
                return;
            }
        }
        $response->status = Authentication::STATUS_UNKNOWN;
        return;
    }

}
