<?php
/**
 * @package        Auth0 Extension (joomla 3.x)
 * @copyright    Copyright (C) - http://www.auth0.com. All rights reserved.
 * @license        The MIT License (MIT), see LICENSE
 * @author        Germán Lena
 * @download URL    http://www.auth0.com
 */

defined('_JEXEC') or die('Restricted access');
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_auth0/assets/auth0back.css');

$params = JComponentHelper::getParams('com_auth0');

$clientid = $params->get('clientid');
$clientsecret = $params->get('clientsecret');
$domain = 'https://' . $params->get('domain');

echo '<div class="span10"><div class="well well-small"><ul>';

if (trim($clientid) == "" || trim($clientsecret) == "" || trim($domain) == "") {
    echo '<li>Auth0 app data: <span style="color:red"><b>Incomplete</b></span></li>';
} else {


    require_once( JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_auth0'.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'auth0_connect.php' );

    try {
        $auth0 = new Auth0Connect($domain, $clientid, $clientsecret, JRoute::_('index.php?option=com_auth0&task=auth', true, -1));
        $auth0->getToken();

        echo '<li>Auth0 app data: <span style="color:green"><b>Complete</b></span></li>';
    }
    catch(Exception $e) {
        echo '<li>Auth0 app data: <span style="color:red"><b>Invalid credentials</b></span></li>';
    }

}


$db = JFactory::getDBO();
$query = "SELECT published FROM #__modules WHERE module='mod_auth0'";
$db->setQuery($query);
$pubdata = $db->loadObject();

if ($pubdata) {
    echo ($pubdata->published == 1) ? '<li>Module : <span style="color:green"><b>Enabled</b></span></li>' : '<li>Module : <span style="color:red"><b>Disabled</b></span> </li>';
} else {
    echo '<li>Module : <span style="color:red"><b>Not Found</b></span></li>';
}


echo '</li></div></div>';
