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

$controller	= JControllerLegacy::getInstance('AdminAuth0');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

?>
