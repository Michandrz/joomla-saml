<?php

/**
 * @package     OneLogin SAML.Plugin
 * @subpackage  User.oneloginsaml
 *
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 */
use Joomla\CMS\String\PunycodeHelper;

    class PlgUserOneloginsaml extends Joomla\CMS\Plugin\CMSPlugin {
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
                    $user->email = PunycodeHelper::emailToPunycode($value);
                    break;
                case 'Name' :
                    $user->name = $value;
                    break;
            }
            $user->save();
        }

}
