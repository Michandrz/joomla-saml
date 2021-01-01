<?php

/**
 * @package     OneLogin SAML
 * @subpackage  com_oneloginsaml
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author Michael Andrzejewski <michael@jetskitechnologies.com>
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

/**
 * View list of all group mappings
 * @since 1.7.0
 */
class oneloginsamlViewGroups extends Joomla\CMS\MVC\View\HtmlView
{
    /**
     * Loads the view adds pagination and toolbar.
     * 
     * @param string $tpl template to load
     * @return void|boolean void on success,false on error
     * @since 1.7.0
     */
    function display($tpl = null)
    {
	$this->items		= $this->get('Items');
	$this->pagination	= $this->get('Pagination');


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

    /**
     * Adds admin tool bar. 
     * @since 1.7.0
     */
    protected function addToolBar()
    {
	ToolbarHelper::title(Text::_('COM_ONELOGIN_MANAGER_GROUPS'));
	ToolbarHelper::addNew('groups.newButton');
	ToolbarHelper::editList('groups.editButton');
	ToolbarHelper::deleteList('', 'groups.delete');
    }

}
