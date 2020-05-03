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

/**
 * Attribute CRUDI Model
 * @since 1.0
 */
class oneloginsamlModelGroups extends JModelList
{
    /**
     *
     * @var JDatabase $db reference to the system's DB
     */
    protected $db;

    /**
     * @param \Joomla\Registry\Registry $state 
     */
    public function __construct($state = null)
    {
	$this->db = JFactory::getDBO();
	parent::__construct($state);
    }



    public function delete($ids = array())
    {
	$table = $this->getTable('groups','oneloginsamlTable');
	foreach($ids as $id) {
	    $table->delete($id);
	}
    }

    protected function getListQuery()
    {
	$query = $this->db->getQuery(true);
	$query->select('*')
		->from($query->quoteName('#__oneloginsaml_groupmapview'));
		$this->db->setQuery($query);
	return $query;
    }

}