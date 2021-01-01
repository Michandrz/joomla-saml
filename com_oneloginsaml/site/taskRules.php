<?php
/**
 * @package     Joomla-Saml
 * @subpackage  com_oneloginsaml
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author      Michael Andrzejewski<michael@jetskitechnologies.com>
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterView;

/**
 * Rule to process simple tasks...why this isn't standard in joomla
 *
 * @since  1.7.0
 */
class oneloginsamlTaskRules implements \Joomla\CMS\Component\Router\Rules\RulesInterface
{
	/**
	 * Router this rule belongs to
	 *
	 * @var RouterView
	 * @since 1.7.0
	 */
	protected $router;

	/**
	 * Class constructor.
	 *
	 * @param   RouterView  $router  Router this rule belongs to
	 *
	 * @since 1.7.0
	 */
	public function __construct(RouterView $router)
	{
		$this->router = $router;
	}

	/**
	 * Dummymethod to fullfill the interface requirements
	 *
	 * @param   array  &$query  The query array to process
	 *
	 * @return  void
	 *
	 * @since 1.7.0
	 * @codeCoverageIgnore
	 */
	public function preprocess(&$query)
	{
	}

	/**
	 * Parse a menu-less URL
	 *
	 * @param   array  &$segments  The URL segments to parse
	 * @param   array  &$vars      The vars that result from the segments
	 *
	 * @return  void
	 *
	 * @since 1.7.0
	 */
	public function parse(&$segments, &$vars)
	{  
            $vars['task'] = $segments[0];
	/*	$active = $this->router->menu->getActive();

		if (!is_object($active))
		{
			$views = $this->router->getViews();

			if (isset($views[$segments[0]]))
			{
				$vars['view'] = array_shift($segments);

				if (isset($views[$vars['view']]->key) && isset($segments[0]))
				{
					$vars[$views[$vars['view']]->key] = preg_replace('/-/', ':', array_shift($segments), 1);
				}
			}*/
	}

	/**
	 * Build a menu-less URL
	 *
	 * @param   array  &$query     The vars that should be converted
	 * @param   array  &$segments  The URL segments to create
	 *
	 * @return  void
	 *
	 * @since 1.7.0
	 */
	public function build(&$query, &$segments)
	{
            $segments[] = $query['task'];
            unset($query['task']);
        }
}
