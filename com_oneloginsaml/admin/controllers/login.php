<?php

/**
 * @package     Joomla-Saml
 * @subpackage  com_oneloginsaml
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author      Michael Andrzejewski<michael@jetskitechnologies.com>
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use OneLogin\Saml2\samlJoomla;

/**
 * Controller to handle admin logins
 */
class oneloginsamlControllerLogin extends Joomla\CMS\MVC\Controller\BaseController {
    
    /**
     *
     * @var samlJoomla
     */
    protected $_oneloginPhpSaml;

    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     * Recognized key values include 'name', 'default_task', 'model_path', and
     * 'view_path' (this list is not meant to be comprehensive).
     *
     * @since   1.7.0
     */
    public function __construct($config = array()) {
        //make the Library easily accessable
      
        $this->_oneloginPhpSaml = new samlJoomla();
       

        parent::__construct($config);
    }
    
    /**
     * Redirect to IDP for Login
     * @param string $redirect encoded URL to end up post login
     * @since 1.7.0
     */
    public function samlLogin($redirect = null) {
        
        $this->setRedirect($this->_oneloginPhpSaml->login('administrator/index.php', array(), false, false, true));
        
        return $this;
    }
    
    /**
     * Redirect to IDP for logout
     * @param string $redirect encoded URL to end up post login 
     * @return $this
     * @since 1.7.0
     */
    public function samlLogout($redirect = null) {
        $this->setRedirect($this->_oneloginPhpSaml->logout('administrator/index.php', array(), null, null, true));
        
        $app = Factory::getApplication();
        $app->logout();
        return $this;
    }
    
    /**
     * Process the logon from the IDP
     * @param string $redirect Base 64 encoded url to end up 
     * @return $this chainloading
     * @since 1.7.0
     */
    public function acs($redirect = null) {
        //process the responce
        $this->_oneloginPhpSaml->processResponse();
        //pass the library to the Authentication plugin
        $credentials['oneLoginSAML'] = $this->_oneloginPhpSaml;
        
        //do the login 
        $app = Factory::getApplication();
        $app->login($credentials, $options);
        
        //redirect
        $this->setRedirect(urldecode($app->input->get('RelayState', 'administrator/index.php', 'raw')));
        
        return $this;
    }
    
    /**
     * Redirect to IDP for a cookie refresh
     * @param string $redirect Base 64 encoded URL
     * @since 1.7.0
     */
    public function refreshCookie($redirect = null) {

        $this->setRedirect($this->_oneloginPhpSaml->login(null, array(), false, true, true));
        
        return $this;
        
    }
    
   /**
     * process IDP logout and redirect
     * @param string $redirect Base 64 encoded URL
     * @since 1.7.0
     */
    public function sls($redirect = null) {
        
        $app = Factory::getApplication();
        
        //retrive the logout
        $this->_oneloginPhpSaml->processSLO();
        
        //redirect
        $this->setRedirect(urldecode($app->input->get('RelayState', 'administrator/index.php', 'raw')));
        
        return $this;
    
    
    }
}
