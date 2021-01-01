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

use Onelogin\Saml2\samlJoomla;
use OneLogin\Saml2\Error;

/**
 * Component main controller
 * 
 * @todo program redirect
 * @since 1.7.0
 */
class oneloginsamlController extends Joomla\CMS\MVC\Controller\BaseController {

    /**
     *
     * @var samlJoomla
     * @since 1.7.0
     */
    protected $_oneloginPhpSaml;

    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     * Recognized key values include 'name', 'default_task', 'model_path', and
     * 'view_path' (this list is not meant to be comprehensive).
     * @since   1.7.0
     */
    public function __construct($config = array()) {
        //make the Library easily accessable
        $this->_oneloginPhpSaml = new samlJoomla();
        parent::__construct($config);
    }

    /**
     * Override default task
     * @since 1.7.0
     */
    public function __default() {
        $this->getMetadata();
    }

    /**
     * echo the metadata
     * 
     * @return $this
     * @throws Error
     * @since 1.7.0
     */
    public function getMetadata() {
        $settings = $this->_oneloginPhpSaml->getSettings();
        $errors = $settings->validateMetadata($settings->getSPMetadata());
        if (empty($errors)) {
            print_r($settings->getSPMetadata());
        } else {
            throw new Error(
                    'Invalid SP metadata: ' . implode(', ', $errors),
                    Error::METADATA_SP_INVALID
            );
        }

        return $this;
    }

}
