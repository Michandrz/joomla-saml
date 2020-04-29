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

class oneloginsamlControllerAttributes extends JControllerLegacy
{
    public function save() {
	$input = $this->getInput('jform');
	$model	 = $this->getModel('Attribute', 'oneloginsamlModel');
	$model->save($input);
	$msg = JText::_('COM_ONELOGIN_ATTRIBUTE_MAPPING_SAVED');
	$this->setRedirect(JRoute::_('index.php?option=com_oneloginsaml&view=attributes', false), $msg);
    }
    public function cancel() {
	$this->setRedirect(JRoute::_('index.php?option=com_oneloginsaml&view=attributes', false));
    }

    public function newButton()
    {
	$this->setRedirect(JRoute::_('index.php?option=com_oneloginsaml&view=attribute&layout=edit&id=0', false));
    }

    public function editButton()
    {
	$input	 = $this->getInput('cid');
	$this->setRedirect(JRoute::_('index.php?option=com_oneloginsaml&view=attribute&layout=edit&id=' . $input[0], false));
	
    }

    /**
     * Deletes a record provided by JINPUT and redirects to the view.
     * 
     */
    public function delete()
    {
	//look for what I am deleting
	$input	 = $this->getInput('cid');
	
	//load the data model
	$model	 = $this->getModel('Attribute', 'oneloginsamlModel');

	//tell the model to execute the delete function
	$model->delete($input);

	//send a message to the user
	$msg = 'Deleted ' . count($input) . ' Mappings';
	JFactory::getApplication()->enqueueMessage($msg);
	
	//redirect to the table
	$this->setRedirect(JRoute::_('index.php?option=com_oneloginsaml&view=attributes', false));
    }

    /**
     *  Function to load the JINPUT and return an input
     * 
     * @param string $key What input value?
     * @param mixed $defaultValue Default, if no value
     * @param string $type Filter to apply 
     * @return mixed
     */
    protected function getInput($key, $defaultValue = array(), $type = 'array')
    {
	//load the input class
	$input = JFactory::getApplication()->input;

	//load and return the requested var
	return $input->get($key, $defaultValue, $type);
    }

}
