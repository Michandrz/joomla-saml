<?php

/**
 * @package     OneLogin SAML.Component
 * @subpackage  oneloginsaml
 *
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT 
 */
defined('_JEXEC') or die;

/**
 * oneloginsamlController
 *
 * @since  1.6
 */
class oneloginsamlController extends JController {

    /**
     * Method to display a view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached
     * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return  oneloginsamlController This object to support chaining.
     *
     * @since   1.5
     */
    public function display($cachable = false, $urlparams = array()) {

        $view = $this->input->get('view');
        $layout = $this->input->get('layout', 'default');

        return parent::display();
    }

}
