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

use Joomla\CMS\Factory;

/**
 * Attribute CRUDI Model
 * @since 1.0
 */
class oneloginsamlModelAttributes extends \Joomla\CMS\MVC\Model\ListModel {

    /**
     *  reference to the system's DB
     * @var \JDatabase
     * @since 1.7.0
     */
    protected $db;

    /**
     * 
     * @param array $state An optional associative array of configuration settings.
     * @since 1.7.0
     */
    public function __construct($state = null) {
        $this->db = Factory::getDBO();
        parent::__construct($state);
    }

    /**
     * Deletes attributes
     * 
     * @param array $ids list of IDs to delete
     * @since 1.7.0
     */
    public function delete($ids = array()) {
        $table = $this->getTable('attribute', 'oneloginsamlTable');
        foreach ($ids as $id) {
            $table->delete($id);
        }
    }

    /**
     * Loads the Groups from the list
     * @return array Array of attributeMapping objects
     * @since 1.7.0
     */
    protected function getListQuery() {
        $query = $this->db->getQuery(true);
        $query->select('*')
                ->from($this->db->quoteName('#__oneloginsaml_attrmap'));
        $this->db->setQuery($query);

        return $query;
    }

}
