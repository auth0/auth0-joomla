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


$params->def('greeting', 1);
require_once (JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_auth0'.DIRECTORY_SEPARATOR.'controller.php');

$user =  JFactory::getUser();
$type = (!$user->get('guest')) ? 'logout' : 'login';

require(JModuleHelper::getLayoutPath('mod_auth0'));
