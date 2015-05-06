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

class AdminAuth0Viewusers extends JViewLegacy
{
    function display($tpl = null)
    {
		JToolBarHelper::preferences( 'com_autho0',400,570 );
		JToolBarHelper::title( JText::_( 'Auth0 - Users' ),'badge' );

		$lists['search']=  JRequest::getVar( "search");
		// Get data from the model
		$items = $this->get('Data');
		$pagination = $this->get('Pagination');
		$totalcusers = $this->get('Total');
		$this->state = $this->get('State');
 		// push data into the template
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('delete', $pagination);
		$this->assignRef('lists',	$lists);
		$this->assignRef('totalcusers', $totalcusers);
		parent::display($tpl);
    }
}
