<?php
/**
 * @package     Joomla-Saml
 * @subpackage  com_oneloginsaml
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author      Michael Andrzejewski<michael@jetskitechnologies.com>
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for the attributes
 * @since 1.7.0
 */
class oneloginsamlTableAttributes extends Joomla\CMS\Table\Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
         * @since 1.7.0
	 */
	function __construct(&$db)
	{
		parent::__construct('#__oneloginsaml_attrmap', 'id', $db);
	}
}