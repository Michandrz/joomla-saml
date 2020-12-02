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

class oneloginsamlControllerConfig extends JControllerLegacy
{
    public function save() {
	$input = $this->getInput('jform');
	$model	 = $this->getModel('Config', 'oneloginsamlModel');
	$model->save($input);
	$msg = JText::_('COM_ONELOGIN_GROUP_MAPPING_SAVED');
	$this->setRedirect(JRoute::_('index.php?option=com_oneloginsaml&view=config', false), $msg);
    }
    public function cancel() {
	$this->setRedirect(JRoute::_('index.php?option=com_oneloginsaml', false));
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
