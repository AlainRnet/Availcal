<?php
/**
 * Availability Calendar Site Controller
 * 
 * @package    	com_availcal
 * @subpackage 	components
 * @link 				
 * @license			GNU/GPL
 */
 // No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * Availal Component Controller
 */
class AvailcalController extends JControllerLegacy
{
	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false, $urlparams = false)
	{
		$format = $this->input->get( 'format', 'html');
                $vName = $this->input->get('view', 'calendar');
		$view = $this->getView($vName, $format);
                $layout = $this->input->get('layout', 'default');
		$model = $this->getModel($vName);
		$view->setModel ($model, true);
		$view->setLayout($layout);
		// Use the View display method
			$view->display(); 
				
	}
}
