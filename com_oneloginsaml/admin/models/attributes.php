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

/**
 * Attribute CRUDI Model
 * @since 1.0
 */
class oneloginsamlModelAttributes extends Joomla\CMS\MVC\Model\ListModel {

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
    
    public function setMatcher($id) {
        //unset all match columns
        $query = $this->_db->getQuery(true);
        $query->update('#__oneloginsaml_attrmap')
                ->set($query->quoteName('match') . '= 0')
                ->where($query->quoteName('match') . '!= 0');
        $this->_db->setQuery($query);
        $this->_db->execute();
        
        //set match column
        $query = $this->_db->getQuery(true);
        $query->update('#__oneloginsaml_attrmap')
                ->set($query->quoteName('match') . ' = 1')
                ->where($query->quoteName('id') . ' = ' . $id);
        $this->_db->setQuery($query);
        $this->_db->execute();
    }

    /**
     * Loads the Attributes from the list
     * @return array Array of attributeMapping objects
     * @since 1.7.0
     */
    protected function getListQuery() {
        $query = $this->_db->getQuery(true);
        $query->select('*')
                ->from($this->_db->quoteName('#__oneloginsaml_attrmap'));
        $this->_db->setQuery($query);

        return $query;
    }

}
