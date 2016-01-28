<?php
/**
 * @package        Auth0 Extension (joomla 3.x)
 * @copyright    Copyright (C) - http://www.auth0.com. All rights reserved.
 * @license        http://www.gnu.org/licenses/gpl-2.0-standalone.html
 * @author        Germán Lena
 * @download URL    http://www.auth0.com
 */

// No direct access
defined('_JEXEC') or die;
class auth0Helper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$vName	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('AUTH0_CONNECT'),
			'index.php?option=com_auth0&view=auth0',
			$vName == 'autho'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_AUTH0_CONNECTED_USERS'),
			'index.php?option=com_auth0&view=users',
			$vName == 'users'
		);

		JSubMenuHelper::addEntry(
			JText::_('AUTH0_TEST'),
			'index.php?option=com_auth0&view=test',
			$vName == 'test'
		);

	}
}
