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

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

/**
 *  Model for handling attribute updates.
 * @since 1.7.0
 */
class oneloginsamlModelAttribute extends Joomla\CMS\MVC\Model\AdminModel
{    /**
     * 
     * Load the Table
     * 
     * @param   string  $type    The type (name) of the Table class to get an instance of.
     * @param   string  $prefix  An optional prefix for the table class name.
     * @param   array   $config  An optional array of configuration values for the Table object.
     * @return Table
     * @since 1.7.0
     */
    public function getTable($type = 'Attributes', $prefix = 'oneloginsamlTable', $config = array())
    {
	return Table::getInstance($type, $prefix, $config);
    }

    /**
     * 
     * Loads the form
     * 
     * @param array $data Not entirely sure why this is here...
     * @param bool $loadData load data or not
     * @return boolean
     * @since 1.7.0
     */
    public function getForm($data = array(), $loadData = true)
    {
	// Get the form.
	$form = $this->loadForm(
		'com_oneloginsaml.attribute', 'attribute', array(
	    'control'	 => 'jform',
	    'load_data'	 => $loadData
		)
	);

	if (empty($form))
	{
	    return false;
	}

	return $form;
    }

    /**
     * Loads the data for the form class to inject.
     * @return array data to be injected.
     * @since 1.7.0
     */
    protected function loadFormData()
    {
	// Check the session for previously entered form data.
	$data = Factory::getApplication()->getUserState(
		'com_oneloginsaml.edit.attribute.data', array()
	);

	if (empty($data))
	{
	    $data = $this->getItem();
	}

	return $data;
    }
    
    /**
     *  saves the data after changes made by the user
     * @param array $data to be saved
     * @since 1.7.0
     */
    public function save($data) {
	    $table = $this->getTable();
	    $table->bind($data);
	    $table->store();
	    
    }

}
