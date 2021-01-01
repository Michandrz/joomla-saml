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
 * Config controller
 * @since 1.7.0
 */
class oneloginsamlControllerConfig extends Joomla\CMS\MVC\Controller\BaseController {

    /**
     * save the modified config and redirect to display
     * @since 1.7.0
     */
    public function save() {
        $input = $this->getInput('jform');
        $model = $this->getModel('Config', 'oneloginsamlModel');
        $model->save($input);
        $msg = Text::_('COM_ONELOGIN_GROUP_MAPPING_SAVED');
        $this->setRedirect(Route::_('index.php?option=com_oneloginsaml&view=config', false), $msg);
    }

    /**
     * discard the modified config and redirect to display
     * @since 1.7.0
     */
    public function cancel() {
        $this->setRedirect(Route::_('index.php?option=com_oneloginsaml', false));
    }

    /**
     *  Function to load the JINPUT and return an input
     * 
     * @param string $key What input value?
     * @param mixed $defaultValue Default, if no value
     * @param string $type Filter to apply 
     * @return mixed
     * @since 1.7.0
     */
    protected function getInput($key, $defaultValue = array(), $type = 'array') {
        //load the input class
        $input = JFactory::getApplication()->input;

        //load and return the requested var
        return $input->get($key, $defaultValue, $type);
    }

}
