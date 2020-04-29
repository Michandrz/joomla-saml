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

class oneloginsamlViewAttributes extends JViewLegacy
{

    function display($tpl = null)
    {
	$this->items		 = $this->get('Items');
	$this->pagination	 = $this->get('Pagination');


	// Check for errors.
	if (count($errors = $this->get('Errors')))
	{
	    JError::raiseError(500, implode('<br />', $errors));

	    return false;
	}

	$this->addToolBar();

	// Display the template
	parent::display($tpl);
    }

    protected function addToolBar()
    {
	JToolbarHelper::title(JText::_('COM_ONELOGIN_MANAGER_ATTRIBUTES'));
	JToolbarHelper::addNew('attributes.newButton');
	JToolbarHelper::editList('attributes.editButton');
	JToolbarHelper::deleteList('', 'attributes.delete');
    }

}
