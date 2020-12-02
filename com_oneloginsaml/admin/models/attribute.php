<?php

/**
 * @package     OneLogin SAML
 * @subpackage  
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author Michael Andrzejewski
 */

/**
 *  Description of attribute
 */
class oneloginsamlModelAttribute extends JModelAdmin
{

    public function getTable($type = 'Attributes', $prefix = 'oneloginsamlTable', $config = array())
    {
	return JTable::getInstance($type, $prefix, $config);
    }

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

    protected function loadFormData()
    {
	// Check the session for previously entered form data.
	$data = JFactory::getApplication()->getUserState(
		'com_oneloginsaml.edit.attribute.data', array()
	);

	if (empty($data))
	{
	    $data = $this->getItem();
	}

	return $data;
    }
    
    public function save($data) {
	    $table = $this->getTable();
	    $table->bind($data);
	    $table->store();
	    
    }

}
