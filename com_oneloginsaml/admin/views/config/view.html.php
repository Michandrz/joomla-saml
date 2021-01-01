<?php

/**
 * @package     OneLogin SAML
 * @subpackage  
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author      Michael Andrzejewski<michael@jetskitechnologies.com>
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;
/**
 * View for Onelogin Configuration
 */
class oneloginsamlViewConfig extends Joomla\CMS\MVC\View\HtmlView
{

    /**
     * View form
     *
     * @var \Joomla\CMS\Form\Form
     * @since 1.7.0
     */
    protected $form = null;
    
    /**
     * Item to edit
     * @var \Joomla\CMS\Object\CMSObject
     * @since 1.7.0
     */
    protected $item = null;
    
    /**
     * Loads form, displays view, adds toolbar
     * 
     * @param type $tpl
     * @return void|boolean void on success, false on failure
     * @since 1.7.0
     */
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
	return parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.7.0
     */
    protected function addToolBar()
    {
	JToolbarHelper::title(Text::_('COM_ONELOGIN_CONFIG_EDIT'), 'oneloginsaml');
	JToolbarHelper::save('config.save');
	JToolbarHelper::cancel('config.cancel','JTOOLBAR_CLOSE');
    }

}
