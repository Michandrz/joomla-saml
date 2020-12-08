<?php

/**
 * @package     OneLogin SAML.Component
 * @subpackage  oneloginsaml
 *
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT 
 * @author      Michael Andrzejewski<michael@jetskitechnologies.com>
 */
defined('_JEXEC') or die;

use \Joomla\CMS\Plugin\PluginHelper;
use \Joomla\Registry\Registry;

/**
 * Component main controller
 * 
 * @todo program redirect
 * @since 1.7.0
 */
class oneloginsamlController extends \Joomla\CMS\MVC\Controller\BaseController {

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
     * Override default task
     */
    public function __default() {
        $this->getMetadata();
    }

    /**
     * echo the metadata
     * 
     * @return $this
     * @throws OneLogin_Saml2_Error
     */
    public function getMetadata() {
        $settings = $this->_oneloginPhpSaml->getSettings();
        $errors = $settings->validateMetadata($settings->getSPMetadata());
        if (empty($errors)) {
            $settings->getSPMetadata();
        } else {
            throw new OneLogin_Saml2_Error(
                    'Invalid SP metadata: ' . implode(', ', $errors),
                    OneLogin_Saml2_Error::METADATA_SP_INVALID
            );
        }

        return $this;
    }

}