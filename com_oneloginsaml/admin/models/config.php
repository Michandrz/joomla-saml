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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\LibraryHelper;
use Joomla\Registry\Registry;

/**
 * Config CRDUI Model
 * @since 1.7.0
 */
class oneloginsamlModelConfig extends Joomla\CMS\MVC\Model\AdminModel {
    
    protected $__paramsToPull = array(
        'com_oneloginsaml'                  => array('component','','administrator/components/com_oneloginsaml/oneloginsaml.xml'), 
        'plg_authentication_oneloginsaml'   => array('plugin','authentication', 'plugins/authentication/oneloginsaml/oneloginsaml.xml'),
        'plg_system_oneloginsaml'           => array('plugin','system', 'plugins/system/oneloginsaml/oneloginsaml.xml'),
        'plg_content_oneloginsaml'          => array('plugin','content', 'plugins/content/oneloginsaml/oneloginsaml.xml'),
        'oneloginsaml'                      => array('library','', 'libraries/oneloginsaml/oneloginsaml.xml'),
        );
    
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
     * Gets params for a given extension
     * 
     * @param string $type type of extension to pull
     * @param string $name system name of the extension to pull
     * @param string $subset Only used for Plugins, Need to know the plugin type 'system', 'content', 'authentication', etc.
     * @return Registry extension params
     * @throws Exception when unable to determine extension type
     * @since 1.7.0
     */
    public function getParams($type, $name, $subset = null) {
        switch ($type) {
            case 'plugin':
                $extension = PluginHelper::getPlugin($subset, substr($name, strpos($name, '_', 5 )+1));
                break;
            case 'component':
                $extension = ComponentHelper::getComponent($name);
                break;
            case 'module' :
                $extension = ModuleHelper::getModule($name);
                break;
            case 'library':
                $extension = LibraryHelper::getLibrary($name);
                break;
            default :
                throw new Exception('Could not determine extension type', 500);
        }

        $Params = new Registry();
        $Params->loadString($extension->params);

        return $Params;
    }
    
    /**
     * Saves parametars of extension regardless of type
     * @param string $type Extension type plugin|component|module|library
     * @param string $name name of the extension to save
     * @param string|null $subset only set this for plugins, set it to plugin type
     * @param Registry $params Extension params to be saved
     * @return boolean true at end
     * @throws Exception if unable to determine extension type.
     * @since 1.7.0
     */
    public function saveParams($type, $name, $subset = null, $params) {
        switch ($type) {
            case 'plugin':
                $extension = PluginHelper::getPlugin($subset, substr($name, strpos($name, '_', 5 )+1));
                break;
            case 'component':
                $extension = ComponentHelper::getComponent($name);
                break;
            case 'module' :
                $extension = ModuleHelper::getModule($name);
                break;
            case 'library':
                $extension = LibraryHelper::getLibrary($name);
                break;
            default :
                throw new Exception('Could not determine extension type', 500);
        }
        if(!isset($extension->id)) {
            throw new Exception('Could not retrive config for ' . $name . '    subset=' . $subset . ' called=' . substr($name, strpos($name, '_', 5 )) , 500);
        }
        $query = $this->_db->getQuery(true);
        $query->update('#__extensions');
        $query->set($query->quoteName('params') . ' = ' . $query->quote($params->toString()));
        $query->where($query->quoteName('extension_id') . ' = ' . $extension->id);
        $this->_db->setQuery($query);
        $this->_db->execute();
        
        return true;
    }
    
    /**
     * Reads the manifest files of all registered extensions to provide configuration fields to the form class
     * @return string XML Document formatted for the Form class
     * @since 1.7.0
     */
    public function getFields() {
        $return = new DOMDocument('1.0');
        $return->loadXML('<form></form>');
        foreach ($this->__paramsToPull as $name => $attrs) {
            $manifest = new DOMDocument('1.0');
            $manifest->load(JPATH_ROOT . '/' . $attrs[2]);
            $fieldset = $manifest->getElementsByTagName('fieldset');
            for ($i = 0; $i < $fieldset->length; $i++) {
                $node = $return->importNode($fieldset->item($i), true);
                for($n = 0; $n < $node->childNodes->count(); $n++) {
                    if($node->childNodes->item($n)->hasAttributes()) {
                    $node->childNodes->item($n)->attributes->getNamedItem('name')->nodeValue = $name . '.' . $node->childNodes->item($n)->attributes->getNamedItem('name')->nodeValue;
                    }
                }
                $return->documentElement->appendChild($node);
                
            }
        }
        return $return->saveXML();
    }

    /**
     * Loads the form data and returns it in a field=>value array
     * @return array Form data
     * @since 1.7.0
     */
    public function loadFormData() {

        $return = array();

        foreach ($this->__paramsToPull as $name => $settings) {
            foreach($this->getParams($settings[0], $name, $settings[1])->toArray() as $key => $value) {
                $return[$name . '.' . $key] = $value;
            }
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
                'com_oneloginsaml.config', $this->getFields(), array(
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
     * Save the form data
     * @param array $data data in feild=>value format
     * @since 1.7.0
     */
    public function save($data) {
        $cache = array();
        
        foreach($this->__paramsToPull as $param => $settings) {
            $cache[$param] = $this->getParams($settings[0], $param, $settings[1]); 
        }
        
        foreach($data as $key => $value) {
            $name = substr($key, 0, strpos($key,'.'));
            $field = substr($key, strpos($key,'.'));
            $cache[$name]->set($field, $value);
        }
        
        foreach($cache as $extenName =>$updated) {
            $this->saveParams($this->__paramsToPull[$extenName][0], $extenName, $this->__paramsToPull[$extenName][1], $updated);
        }
    }

}
