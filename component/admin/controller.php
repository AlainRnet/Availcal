<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of AvailCal component
 */
class AvailCalController extends JControllerLegacy {

    /**
     * display task
     *
     * @return  JController		This object to support chaining.
     */
    protected $default_view = 'Darkperiods';

    public function display($cachable = false, $urlparams = false) {
        // call parent behavior
        parent::display();
        return $this;
    }

}
