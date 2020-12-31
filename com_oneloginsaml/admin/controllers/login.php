<?php

/** 
 *  @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 *  @license     MIT
 */
defined('_JEXEC') or die;

use \Joomla\CMS\Plugin\PluginHelper;
use \Joomla\Registry\Registry;
use \Joomla\CMS\Factory;
/**
 * Description of login
 *
 * @author Michael Andrzejewski<michael@jetskitechnologies.com>
 */
class oneloginsamlControllerLogin extends \Joomla\CMS\MVC\Controller\BaseController {
    
    /**
     *
     * @var \OneLogin_Saml2_Auth_Joomla
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
        
        $oneLoginPlugin = PluginHelper::getPlugin('system', 'oneloginsaml');
        $plgParams = new Registry($oneLoginPlugin->params);
        $this->_oneloginPhpSaml = new OneLogin_Saml2_Auth_Joomla($plgParams);
       

        parent::__construct($config);
    }
    /**
     * Redirect to IDP for Login
     * @param string $redirect Base 64 encoded URL
     */
    public function samlLogin($redirect = null) {
        
        $this->setRedirect($this->_oneloginPhpSaml->login('administrator/index.php', array(), false, false, true));
        
        return $this;
    }
    
    public function samlLogout($redirect = null) {
        $this->setRedirect($this->_oneloginPhpSaml->logout('administrator/index.php', array(), null, null, true));
        
        $app = Factory::getApplication();
        $app->logout();
        return $this;
    }


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
     */
    public function refreshCookie($redirect = null) {

        $this->setRedirect($this->_oneloginPhpSaml->login(null, array(), false, true, true));
        
        return $this;
        
    }
    
   /**
     * Post IDP Logout
     * @param string $redirect Base 64 encoded URL
     * @todo handle redirect
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
