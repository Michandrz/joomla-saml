<?php

/**
 * @package     OneLogin SAML
 * @subpackage  com_onelogin
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author      Michael Andrzejewski<micahel@jetskitechnologies.com>
 */
defined('_JEXEC') or die('Restricted access');

use \Joomla\CMS\Table\Table;
use \Joomla\CMS\Factory;
/**
 *  Description of attribute
 * @since 1.7.0
 */
class oneloginsamlModelAttribute extends \Joomla\CMS\MVC\Model\AdminModel
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
     * 
     * @return type
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
     * 
     * @param type $data
     */
    public function save($data) {
	    $table = $this->getTable();
	    $table->bind($data);
	    $table->store();
	    
    }

}
