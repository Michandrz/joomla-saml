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

use Joomla\CMS\Factory;
/**
 * Groups CRUDI Model
 * @since 1.6.0
 */
class oneloginsamlModelGroups extends Joomla\CMS\MVC\Model\ListModel
{
    /**
     *reference to the system's DB
     * @var \JDatabase $db 
     * @since 1.6.0
     */
    protected $db;

    /**
     * 
     * @param array $state An optional associative array of configuration settings.
     * @since 1.7.0
     */
    public function __construct($state = null)
    {
	$this->db = Factory::getDBO();
	parent::__construct($state);
    }


    /**
     * Deletes Groups
     * 
     * @param array $ids list of IDs to delete
     * @since 1.7.0
     */
    public function delete($ids = array())
    {
	$table = $this->getTable('groups','oneloginsamlTable');
	foreach($ids as $id) {
	    $table->delete($id);
	}
    }
        /**
     * Loads the Groups from the list
     * @return array Array of GroupMapping objects
     * @since 1.7.0
     */
    protected function getListQuery()
    {
	$query = $this->db->getQuery(true);
	$query->select('*')
		->from($query->quoteName('#__oneloginsaml_groupmapview'));
		$this->db->setQuery($query);
	return $query;
    }

}