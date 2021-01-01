<?php
/**
 * @package     Joomla-Saml
 * @subpackage  com_oneloginsaml
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author      Michael Andrzejewski<michael@jetskitechnologies.com>
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
/**
 * View of Individual Group 
 * @since 1.7.0
 */
class oneloginsamlViewGroup extends Joomla\CMS\MVC\View\HtmlView
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
     * Pulls in forms and items, adds the admin tool bar
     *
     * @param string $tpl template to load
     * @return void|boolean void on succsess, false on failure 
     * @todo Remove JError as class is dpercated as of J4.0
     * @since 1.7.0
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
     * Add the page title and toolbar. Hides main admin menu
     *
     * @return  void
     *
     * @since   1.7.0
     */
    protected function addToolBar()
    {
	$input = Factory::getApplication()->input;

	// Hide Joomla Administrator Main menu
	$input->set('hidemainmenu', true);

	$isNew = ($this->item->id == 0);

	if ($isNew)
	{
	    $title = Text::_('COM_ONELOGIN_MANAGER_GROUP_NEW');
	} else
	{
	    $title = Text::_('COM_ONELOGIN_MANAGER_GROUP_EDIT');
	}

	ToolbarHelper::title($title, 'oneloginsaml');
	ToolbarHelper::save('groups.save');
	ToolbarHelper::cancel(
		'groups.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE'
	);
    }

}
