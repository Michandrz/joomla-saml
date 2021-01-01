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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/**
 * Groups CRUDL controller
 * @since 1.7.0
 */
class oneloginsamlControllerGroups extends Joomla\CMS\MVC\Controller\BaseController
{
    /**
     * Process form and redirect
     * @since 1.7.0
     */
    public function save() {
	$input = $this->getInput('jform');
	$model	 = $this->getModel('Group', 'oneloginsamlModel');
	$model->save($input);
	$msg = Text::_('COM_ONELOGIN_GROUP_MAPPING_SAVED');
	$this->setRedirect(Route::_('index.php?option=com_oneloginsaml&view=groups', false), $msg);
    }
    
    /**
     * Discard changes and redirect
     * @since 1.7.0
     */
    public function cancel() {
	$this->setRedirect(Route::_('index.php?option=com_oneloginsaml&view=groups', false));
    }
    
    /**
     * redirect to blank edit form
     * @since 1.7.0
     */
    public function newButton()
    {
	$this->setRedirect(Route::_('index.php?option=com_oneloginsaml&view=group&layout=edit&id=0', false));
    }

    /**
     * redirect to edit form
     * @since 1.7.0
     */
    public function editButton()
    {
	$input	 = $this->getInput('cid');
	$this->setRedirect(Route::_('index.php?option=com_oneloginsaml&view=group&layout=edit&id=' . $input[0], false));
	
    }

    /**
     * Deletes a record provided by JINPUT and redirects to the view.
     * @since 1.7.0
     */
    public function delete()
    {
	//look for what I am deleting
	$input	 = $this->getInput('cid');
	
	//load the data model
	$model	 = $this->getModel('Group', 'oneloginsamlModel');

	//tell the model to execute the delete function
	$model->delete($input);

	//send a message to the user
	$msg = 'Deleted ' . count($input) . ' Mappings';
	JFactory::getApplication()->enqueueMessage($msg);
	
	//redirect to the table
	$this->setRedirect(Route::_('index.php?option=com_oneloginsaml&view=groups', false));
    }

    /**
     *  Function to load the JINPUT and return an input
     * 
     * @param string $key What input value?
     * @param mixed $defaultValue Default, if no value
     * @param string $type Filter to apply 
     * @return mixed input value 
     * @since 1.7.0
     */
    protected function getInput($key, $defaultValue = array(), $type = 'array')
    {
	//load the input class
	$input = JFactory::getApplication()->input;

	//load and return the requested var
	return $input->get($key, $defaultValue, $type);
    }

}
