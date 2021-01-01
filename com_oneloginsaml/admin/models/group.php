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
 *  CRUDI model for Groups
 */
class oneloginsamlModelGroup extends  Joomla\CMS\MVC\Model\AdminModel
{
    /**
     * 
     * Load the Table
     * 
     * @param   string  $type    The type (name) of the Table class to get an instance of.
     * @param   string  $prefix  An optional prefix for the table class name.
     * @param   array   $config  An optional array of configuration values for the Table object.
     * @return Table
     * @since 1.7.0
     */
    public function getTable($type = 'Groups', $prefix = 'oneloginsamlTable', $config = array())
    {
	return Table::getInstance($type, $prefix, $config);
    }

    /**
     * Get form
     * 
     * @param array $data data in feild=>value format
     * @param boolean $loadData load existing data
     * @return  \JForm|boolean  \JForm object on success, false on error.
     * @since 1.7.0
     */
    public function getForm($data = array(), $loadData = true)
    {
	// Get the form.
	$form = $this->loadForm(
		'com_oneloginsaml.group', 'group', array(
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
     * Loads the form data and returns it in a feild=>value array
     * @return array Form data
     * @since 1.7.0
     */
    protected function loadFormData()
    {
	// Check the session for previously entered form data.
	$data = Factory::getApplication()->getUserState(
		'com_oneloginsaml.edit.group.data', array()
	);

	if (empty($data))
	{
	    $data = $this->getItem();
	}

	return $data;
    }
    
    /**
     * Save the form
     * @param array $data data in feild=>value format
     * @since 1.7.0
     */
    public function save($data) {
	    $table = $this->getTable();
	    $table->bind($data);
	    $table->store();
	    
    }

}
