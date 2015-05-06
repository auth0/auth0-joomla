<?php
/**
 * @package        Auth0 Extension (joomla 3.x)
 * @copyright    Copyright (C) - http://www.auth0.com. All rights reserved.
 * @license        http://www.gnu.org/licenses/gpl-2.0-standalone.html
 * @author        Germán Lena
 * @download URL    http://www.auth0.com
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the HelloWorld Component
 *
 * @package    HelloWorld
 */

class Adminauth0Viewtest extends JViewLegacy
{
    function display($tpl = null)
    {
		JToolBarHelper::preferences( 'com_auth0',400,570 );
		JToolBarHelper::title( JText::_( 'Auth0 - Test' ),'badge' );
		parent::display($tpl);
    }
}
