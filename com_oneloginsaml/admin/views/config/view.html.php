<?php

/**
 * @package     OneLogin SAML
 * @subpackage  
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author Michael Andrzejewski
 */
defined('_JEXEC') or die('Restricted access');

class oneloginsamlViewConfig extends JViewLegacy
{

    /**
     * View form
     *
     * @var         form
     */
    protected $form = null;
    
    public function display($tpl = null)
    {
	// Get the Data
	$this->form	 = $this->get('Form');

	//Check for errors.
	if (count($errors = $this->get('Errors')))
	{
	    JError::raiseError(500, implode('<br />', $errors));

	    return false;
	}


	// Set the toolbar
	$this->addToolBar();

	// Display the template
	parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function addToolBar()
    {
	JToolbarHelper::title('COM_ONELOGIN_CONFIG_EDIT', 'oneloginsaml');
	JToolbarHelper::save('config.save');
	JToolbarHelper::cancel('config.cancel','JTOOLBAR_CLOSE');
    }

}
