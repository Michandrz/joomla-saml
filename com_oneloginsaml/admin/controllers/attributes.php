<?php

/**
 * @package     OneLogin SAML
 * @subpackage  
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author      Michael Andrzejewski<michael@jetskitechnologies.com>
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/**
 * Attribute CRUDL controller
 * @since 1.7.0
 */
class oneloginsamlControllerAttributes extends Joomla\CMS\MVC\Controller\BaseController 
{

    /**
     * Process form and redirect
     * @since 1.7.0
     */
    public function save() {
        $input = $this->getInput('jform');
        $model = $this->getModel('Attribute', 'oneloginsamlModel');
        $model->save($input);
        $msg = Text::_('COM_ONELOGIN_ATTRIBUTE_MAPPING_SAVED');
        $this->setRedirect(Route::_('index.php?option=com_oneloginsaml&view=attributes', false), $msg);
    }
    public function setMatcher() {
        $input = $this->getInput('id', null, 'integer');
        if($input != null) {
            $model = $this->getModel('Attributes', 'oneloginsamlModel');
            $model->setMatcher($input);
            $msg = Text::_('COM_ONELOGIN_ATTRIBUTE_MATCHER_UPDATED');
        }
        $this->setRedirect(Route::_('index.php?option=com_oneloginsaml&view=attributes', false), $msg);
    }

    /**
     * Discard changes and redirect
     * @since 1.7.0
     */
    public function cancel() {
        $this->setRedirect(Route::_('index.php?option=com_oneloginsaml&view=attributes', false));
    }

    /**
     * redirect to blank edit form
     * @since 1.7.0
     */
    public function newButton() {
        $this->setRedirect(Route::_('index.php?option=com_oneloginsaml&view=attribute&layout=edit&id=0', false));
    }

    /**
     * redirect to edit form
     * @since 1.7.0
     */
    public function editButton() {
        $input = $this->getInput('cid');
        $this->setRedirect(Route::_('index.php?option=com_oneloginsaml&view=attribute&layout=edit&id=' . $input[0], false));
    }

    /**
     * Deletes a record provided by JINPUT and redirects to the view.
     * 
     */
    public function delete() {
        //look for what I am deleting
        $input = $this->getInput('cid');

        //load the data model
        $model = $this->getModel('Attribute', 'oneloginsamlModel');

        //tell the model to execute the delete function
        $model->delete($input);

        //send a message to the user
        $msg = 'Deleted ' . count($input) . ' Mappings';
        JFactory::getApplication()->enqueueMessage($msg);

        //redirect to the table
        $this->setRedirect(Route::_('index.php?option=com_oneloginsaml&view=attributes', false));
    }

    /**
     *  Function to load the JINPUT and return an input
     * 
     * @param string $key What input value?
     * @param mixed $defaultValue Default, if no value
     * @param string $type Filter to apply 
     * @return mixed
     */
    protected function getInput($key, $defaultValue = array(), $type = 'array') {
        //load the input class
        $input = JFactory::getApplication()->input;

        //load and return the requested var
        return $input->get($key, $defaultValue, $type);
    }

}
