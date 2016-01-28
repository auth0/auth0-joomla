<?php
/**
 * @package        Auth0 Extension (joomla 3.x)
 * @copyright    Copyright (C) - http://www.auth0.com. All rights reserved.
 * @license        http://www.gnu.org/licenses/gpl-2.0-standalone.html
 * @author        Germán Lena
 * @download URL    http://www.auth0.com
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.controller' );

class Adminauth0Controller extends JControllerLegacy
{
	protected $default_view = 'auth0';

	function display($cachable = false, $urlparams = false) {

			require_once JPATH_COMPONENT.'/helpers/auth0.php';
			auth0Helper::addSubmenu(JRequest::getCmd('view', 'auth0'));

			$view   = $this->input->get('view', 'auth0');
			$layout = $this->input->get('layout', 'default');
			$id     = $this->input->getInt('id');

			parent::display();
			return $this;
	}

}
?>
