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
use \Joomla\CMS\Plugin\PluginHelper;
use \Joomla\Registry\Registry;
use \Joomla\CMS\Factory;

/**
 * Config CRDUI Model
 * @since 1.7.0
 */
class oneloginsamlModelConfig extends \Joomla\CMS\MVC\Model\AdminModel {
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
    public function getTable($type = 'Config', $prefix = 'oneloginsamlTable', $config = array()) {
        return Table::getInstance($type, $prefix, $config);
    }
    
    /**
     *  Returns the current Plugin Params
     * @return Registry;
     */
    public function getPluginParams() {
        
        $oneLoginPlugin = PluginHelper::getPlugin('system', 'oneloginsaml');
        $plgParams = new Registry();
        $plgParams->loadString($oneLoginPlugin->params);
        
        return $plgParams;
    }

    /**
     * Loads the form data and returns it in a field=>value array
     * @return array Form data
     * @since 1.7.0
     */
    public function loadFormData() {

        $return = array();

        foreach ($this->getPluginParams()->toArray() as $key => $value) {
            $return[$key] = $value;
        }
        return $return;
    }

    /**
     * Get form
     * 
     * @param array $data data in feild=>value format
     * @param boolean $loadData load existing data
     * @return  \JForm|boolean  \JForm object on success, false on error.
     * @since 1.7.0
     */
    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm(
                'com_oneloginsaml.config', 'config', array(
            'control' => 'jform',
            'load_data' => $loadData
                )
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Save the form
     * @param array $data data in feild=>value format
     */
    public function save($data) {
        $plgParams = $this->getPluginParams();

        foreach ($data as $key => $value) {
            $plgParams->set($key, $value);
        }

        $query = $this->_db->getQuery(true);
        $query->update('#__extensions')
                ->set($query->quoteName('params') . '=' . $query->quote($plgParams->toString()))
                ->where($query->quoteName('element') . '=' . $query->quote('oneloginsaml'));
        $this->_db->setQuery($query);
        $this->_db->execute();
    }

}
