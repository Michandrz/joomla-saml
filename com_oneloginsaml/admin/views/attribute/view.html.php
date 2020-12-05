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
/**
 * View of an individual item
 */
class oneloginsamlViewAttribute extends \Joomla\CMS\MVC\View\HtmlView
{

    /**
     * View form
     *
     * @var \Joomla\Form\Form
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
     * Display the Attribute view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    public function display($tpl = null)
    {
	// Get the Data
	$this->form	 = $this->get('Form');
	$this->item	 = $this->get('Item');

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
	$input = JFactory::getApplication()->input;

	// Hide Joomla Administrator Main menu
	$input->set('hidemainmenu', true);

	$isNew = ($this->item->id == 0);

	if ($isNew)
	{
	    $title = JText::_('COM_ONELOGIN_MANAGER_ATTRIBUTE_NEW');
	} else
	{
	    $title = JText::_('COM_ONELOGIN_MANAGER_ATTRIBUTE_EDIT');
	}

	JToolbarHelper::title($title, 'oneloginsaml');
	JToolbarHelper::save('attributes.save');
	JToolbarHelper::cancel(
		'attributes.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE'
	);
    }

}
