<?php
/**
 * @package        Auth0 Extension (joomla 3.x)
 * @copyright    Copyright (C) - http://www.auth0.com. All rights reserved.
 * @license        The MIT License (MIT), see LICENSE
 * @author        Germán Lena
 * @download URL    http://www.auth0.com
 */
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class auth0Viewauth0 extends JViewLegacy {
	function display($tpl = null)
	{
		parent::display('raw');
	}

}

?>
