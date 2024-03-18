<?php
/**
 * @version		$Id: helper.php 21421 2011-06-03 07:21:02Z infograf768 $
 * @package		Joomla.Site
 * @subpackage	mod_availcal
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;

class modAvailcalHelper
{
	public static function getDarkperiods($params)
	{
		// just startup
		$mainframe = Factory::getApplication();


		
		//Get parameters
		$name = $params->get('name');
		
		//Array for the dark periods
		$dark_days = array();

		//Get the database
		$db =Factory::getDBO();
		//Get Darkperiods
		$query = 'SELECT start_date,end_date,busy FROM' . $db->quoteName('#__avail_calendar'). 'WHERE' . $db->quoteName('name') . ' = ' . $db->Quote($name) ;
		$db->setQuery($query);
		$db->execute();

		//Check of object has one or more darkperiods
		$line = null;
		$dark_days = null;
		if ($num_rows = $db->getNumRows()	){
			$counter = 0;
			$line = $db->loadAssocList();
			while ($counter < $num_rows )
			{
				$dark_days[$counter]['start'] = strtotime($line[$counter]['start_date']);
                                $dark_days[$counter]['end'] = strtotime($line[$counter]['end_date']);
				$dark_days[$counter]['busy'] = $line[$counter]['busy'];
				$counter++;
			}
		}
		return $dark_days;
	}
}
