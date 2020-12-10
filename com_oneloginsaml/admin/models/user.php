<?php

/**
 *  @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 *  @license     MIT
 *  @author Michael Andrzejewski<michael@jetskitechnologies.com>
 */
use \Joomla\CMS\User\User;
use \Joomla\CMS\User\UserHelper;
use \Joomla\CMS\Plugin\PluginHelper;
use \Joomla\CMS\Component\ComponentHelper;
use \Joomla\CMS\Factory;
/**
 * Model to handle various user/database interactions
 */
class oneloginsamlModelUser extends \Joomla\CMS\MVC\Model\BaseDatabaseModel {

    /**
     * Holds the idp and local account matchers
     * @var array
     */
    protected $matcher;
    
    /**
     * value to match against to find account
     * @var string
     */
    protected $matcherValue;
    
    /**
     * Saml Group attribute mapping
     * @var array
     */
    protected $groupMatcher;
    
    /**
     * Takes the Saml attributes maps them to a local string and pushes them to the plugins for post-processing
     * @param User $user
     * @param array $saml_attrs
     * @since 1.7.0
     */
    public function processAttributes($user, $saml_attrs) {
        
        //load the attrmap
        $query = $this->_db->getQuery(true);
        $query->select('`local`, `idp`')->from('#__oneloginsaml_attrmap');
        $this->_db->setQuery($query);
        $attrmap = $this->_db->loadAssocList('idp', 'local');
        
        //Let the user plugins deal with where the data goes
        PluginHelper::importPlugin('user');
        $app = Factory::getApplication();
        //$dispatcher = JEventDispatcher::getInstance();
        
        foreach($saml_attrs as $samlattributename => $samlattribute) {
            if(array_key_exists($samlattributename, $attrmap)) {
                $app->triggerEvent('onUserUpdateInfo', array($user, $attrmap[$samlattributename], $samlattribute[0]));
            }
        }
    }
    /**
     * 
     * @param User $user
     * @param array $saml_attrs
     * @since 1.7.0
     */
    public function setGroups($user, $saml_attrs) {
        
        //load the groupmap
        $query = $this->_db->getQuery(true);
        $query->select('`local`, `idp`')->from('#__oneloginsaml_groupmap');
        $this->_db->setQuery($query);
        $groupmap = $this->_db->loadAssocList('idp', 'local');
        
        //load the saml assigned groups
        $saml_groups = $saml_attrs[$this->groupMatcher['idp']];
        
        //output variable
        $groups = array();
        
        foreach($saml_groups as $saml_group) {
            if(isset($groupmap[$saml_group])) {
                $groups[] = $groupmap[$saml_group];
            }
        }
        $groups = array_unique($groups);
        
        if(count($groups) < 1) {
            $com_userParams = ComponentHelper::getParams('com_users');
            $groups[] = $com_userParams->get('new_usertype', 2);
        }
        
        UserHelper::setUserGroups($user->id, $groups);
    }
    /**
     * Sets the Matchers for the samlparams to come through
     * 
     * @param array $saml_attrs
     * @param array $attrmap
     * @todo throw exceptions if matcher doesn't exist or is null
     * @since 1.7.0
     */
    public function setMatcher($saml_attrs) {
            //load the account matcher first
            $query = $this->_db->getQuery(true);
            $query->select('`local`, `idp`')
                    ->from('#__oneloginsaml_attrmap')
                    ->where($query->quoteName('match') . " = " . "1");
            $this->_db->setQuery($query);
            $this->matcher = $this->_db->loadAssoc();
    
            //load the group matcher
            $query = $this->_db->getQuery(true);
            $query->select('`local`, `idp`')
                    ->from('#__oneloginsaml_attrmap')
                    ->where($query->quoteName('local') . " = " . $query->quote('Groups'));
            $this->_db->setQuery($query);
            $this->groupMatcher = $this->_db->loadAssoc();
        
        //set our matcherValue
        $this->matcherValue = $saml_attrs[$this->matcher['idp']][0];
    }
    
    /**
     * Looks for the User
     * 
     * @param string $column column to search for user
     * @param string $value needle to search for
     * @return boolean|User User Object on success, false on failure
     * @throws ErrorException
     * @since 1.7.0
     */
    public function getUser() {
        if(is_object($this->user)) {
            return $this->user;
        }
        if($this->matcher == null) {
            throw new Exception("Call to getUser before processAttribures", 500);
        }
        $column = $this->matcher['local'];
        $value = $this->matcherValue;
        
        $query = $this->_db->getQuery(true);
        $query->select($query->quoteName('id'))
                ->from('#__users')
                ->where(
                        $query->quoteName($column)
                        . "="
                        . $query->quote($value));
        $this->_db->setQuery($query);
        $this->_db->execute();
        if($this->_db->getNumRows() == 0 ) {
            return false;
        } elseif($this->_db->getNumRows() > 1 ) {
            throw new Exception("More than one User was found unable to complete login", 500);
        }
        return new User($this->_db->loadResult());
    }
    /**
     * Creates the user bypassing verification coming from the IDP
     * @param type $saml_attrs
     * @return boolean|User User Object on success, false on failure
     * @todo wite this function
     */
    public function createUser($saml_attrs) {
        //first set the matcher attributes
        $this->setMatcher($saml_attrs);
        $user = new User;
        $this->processAttributes($user, $saml_attrs);
        $this->setGroups($user, $saml_attrs);
        
        if($user->save()) {
            return $user;
        } else {
            return false;
        }
    }    
}
