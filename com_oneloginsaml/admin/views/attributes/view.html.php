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
 * View list of attributes 
 */
class oneloginsamlViewAttributes extends Joomla\CMS\MVC\View\HtmlView
{
    /**
     *  loads in all the Attributes
     *
     * @var \JObject 
     */
    protected $items;
    
    /**
     * pagination Class
     * @var \Joomla\CMS\Pagination\Pagination
     */
    protected $pagination;
    /**
     * Load and display the template, adds toolbar, sets page title
     * @param string $tpl template to load
     * @return void|boolean void on success, false on failure
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
     * Tool bar builder, Sets page title
     * 
     * @since 1.7.0
     */
    protected function addToolBar()
    {
	JToolbarHelper::title(Text::_('COM_ONELOGIN_MANAGER_ATTRIBUTES'));
	JToolbarHelper::addNew('attributes.newButton');
	JToolbarHelper::editList('attributes.editButton');
	JToolbarHelper::deleteList('', 'attributes.delete');
    }

}
