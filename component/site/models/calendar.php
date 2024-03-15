<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modellist');

/**
 * Availability Calendar Site calendar model
 * 
 * @package    	com_availcal
 * @subpackage 	components
 * @link 				
 * @license			GNU/GPL
 */
 
class AvailcalModelCalendar extends JModelList
{
	protected  function _getListQuery()
	{
		// Get id
		$id = JRequest::getVar( 'id');		
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		// Select some fields
		$query->select( 'start_date' );
		$query->select( 'end_date' );
		$query->select( 'busy' );
		// From the AvailCal table
		$query->from('#__avail_calendar');
		
		$query->where('name = \'' . $id . '\'');
		

		return $query;
	}
	public function getItems(){
		$store = $this->getStoreId();
		if (isset($this->cache[$store])){
			return $this->cache[$store];
		}
		$query = $this->_getListQuery();
	//Changed for Availcal Get all records
		$items = $this->_getList($query, 0, 0);  //  Second argument provides the limitstart, Third argument provides limit
	//End change for Availcal
		if ($this->_db->getErrorNum()){
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		$this->cache[$store] = $items;
		return $this->cache[$store];
	}	
}
