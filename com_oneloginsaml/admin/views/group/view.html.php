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

class oneloginsamlViewGroup extends JViewLegacy
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
	    $title = JText::_('COM_ONELOGIN_MANAGER_GROUP_NEW');
	} else
	{
	    $title = JText::_('COM_ONELOGIN_MANAGER_GROUP_EDIT');
	}

	JToolbarHelper::title($title, 'oneloginsaml');
	JToolbarHelper::save('groups.save');
	JToolbarHelper::cancel(
		'groups.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE'
	);
    }

}
