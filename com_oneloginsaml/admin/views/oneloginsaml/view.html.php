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
/**
 * Standard Joomla view class
 * 
 * @since 1.7.0
 */
class oneloginsamlViewOneloginsaml extends Joomla\CMS\MVC\View\HtmlView {
    
    /**
     * Adds admin tool bar. 
     * @since 1.7.0
     */
    protected function addToolBar()
    {
	ToolbarHelper::title(Text::_('COM_ONELOGIN_LANDING_TITLE'));
    }
}
