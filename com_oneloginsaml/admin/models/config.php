<?php

/*
 * @package     OneLogin SAML
 * @subpackage  
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author Michael Andrzejewski
 */

use Joomla\CMS\MVC\Model\AdminModel as JModelAdmin;

/**
 *  Description of config
 */
class oneloginsamlModelConfig extends JModelAdmin
{
    public function getTable($type = 'Config', $prefix = 'oneloginsamlTable', $config = array())
    {
	return JTable::getInstance($type, $prefix, $config);
    }
    
    public function loadFormData()
    {
	$query = $this->_db->getQuery(true);
	$query->select('*')
		->from($query->quoteName('#__oneloginsaml_config'));
	$this->_db->setQuery($query);
	$return = array();
	
	foreach($this->_db->loadAssocList() as $row) {
	    $return[$row['param']] = $row['value'];
	}
	return $return;
    }

    public function getForm($data = array(), $loadData = true)
    {
	// Get the form.
	$form = $this->loadForm(
		'com_oneloginsaml.config', 'config', array(
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
        public function save($data) {
	    foreach($data as $key => $value) {
		$query = $this->_db->getQuery(true);
		$query->update('#__oneloginsaml_config')
			->set('`value` = ' . $query->quote($value))
			->where('`param` = ' . $query->quot($key));
		$query->setQuery($query);
		$this->_db->execute();
	    }
    }
}
