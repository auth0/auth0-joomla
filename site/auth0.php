<?php
/**
 * @package        Auth0 Extension (joomla 3.x)
 * @copyright    Copyright (C) - http://www.auth0.com. All rights reserved.
 * @license        The MIT License (MIT), see LICENSE
 * @author        Germán Lena
 * @download URL    http://www.auth0.com
 */
 
defined('_JEXEC') or die('Restricted access');

// Require the base controller
require_once( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controller.php' );

$controller = JControllerLegacy::getInstance('auth0');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>
