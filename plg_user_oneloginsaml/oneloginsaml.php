<?php
/**
 * @package     Joomla-Saml
 * @subpackage  plg_iser_oneloginsaml
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author      Michael Andrzejewski<michael@jetskitechnologies.com>
 */
use Joomla\CMS\String\PunycodeHelper;

/**
 * class to handle attribute setting via the samlLogin Lib
 */
class PlgUserOneloginsaml extends Joomla\CMS\Plugin\CMSPlugin {

    /**
     * very simple user attribute updating/matching from the samlLoginLib
     *
     * @param \Joomla\CMS\User\User $user User to set values on
     * @param string $key the attribute name to be updated
     * @param string $value the attribute value to be updated
     */
    public function onUserUpdateInfo($user, $key, $value) {
        switch ($key) {
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
