<?php

/**
 * @package     OneLogin SAML.Component
 * @subpackage  com_oneloginsaml
 *
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT 
 * @author      Michael Andrzejewski<michael@jetskitechnologies.com>
 */
defined('_JEXEC') or die;


/**
 * Class to handle SEOs written out of force (Microsoft Azure I'm looking at you)
 */
class oneloginsamlRouter extends \Joomla\CMS\Component\Router\RouterView {
    
    public function __construct($app = null, $menu = null) {
        JLoader::register('oneloginsamlTaskRules', JPATH_BASE . '\\components\\com_oneloginsaml\\taskRules.php');
        $taskRule = new oneloginsamTaskRules($this);
        
        $this->attachRule($taskRule);
        parent::__construct($app, $menu);
    }
    
}
