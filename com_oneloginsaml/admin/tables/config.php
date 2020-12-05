<?php
/** 
 * @package     OneLogin SAML
 * @subpackage  
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author Michael Andrzejewski
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Table for the config
 * @since 1.7.0
 */
class oneloginsamlTableConfig extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
         * @since 1.7.0
	 */
	function __construct(&$db)
	{
		parent::__construct('#__oneloginsaml_config', 'param', $db);
	}
}