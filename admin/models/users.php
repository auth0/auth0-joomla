<?php
/**
 * @package        Auth0 Extension (joomla 3.x)
 * @copyright    Copyright (C) - http://www.auth0.com. All rights reserved.
 * @license        The MIT License (MIT), see LICENSE
 * @author        Germán Lena
 * @download URL    http://www.auth0.com
 */
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.model' );
class Adminauth0Modelusers extends JModelLegacy
{
    var $_data;
	var $_total = null;
	var $_pagination = null;

    function _buildQuery()
    {
		$db				= JFactory::getDBO();
		$mainframe = JFactory::getApplication();
		$orderCol   = JRequest::getCmd('filter_order', 'id');
		$this->setState('list.ordering', $orderCol);
		$listOrder   =  JRequest::getCmd('filter_order_Dir', 'DESC');
		$this->setState('list.direction', $listOrder);
		$orderCol   = JRequest::getCmd('filter_order', 'id');
		$search	= $mainframe->getUserStateFromRequest( "search", 'search', '','string', true);
		if (isset( $search ) && strlen($search)> 0)
		{
			$searchEscaped = '"%'.$db->getEscaped( $search, true ).'%"';
			$where = "WHERE #__auth0_joomla_connect.linked=1";
		}else{
			$where ='';
		}

	$query = "SELECT #__users.id as id, #__users.username as username, #__users.name as fullname, #__users.email as email, #__auth0_joomla_connect.auth0_userid as auth0id,
	#__users.registerDate as joineddate
    FROM #__users INNER JOIN #__auth0_joomla_connect ON #__users.id=#__auth0_joomla_connect.joomla_userid
	".$where." ORDER BY #__users.".$orderCol." ".$listOrder;

	//$query = "SELECT * FROM #__testimonials ".$where." ORDER BY ".$orderCol." ".$listOrder;
	   return $query;
    }

   function __construct()
	  {
		parent::__construct();

		$mainframe = JFactory::getApplication();

		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	  }

	  function getData()
	  {
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_data;
	  }

	   function getTotal()
	  {
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
	  }
	   function getPagination()
		  {
			if (empty($this->_pagination)) {
				jimport('joomla.html.pagination');
				$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
			}
			return $this->_pagination;
		  }

}
