<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

/**
 * Accommodation Controller
 */
class AvailCalControllerDarkperiod extends JControllerForm {

    /**
     * @since   1.6
     */
    protected $view_item = 'form'; 
    protected $view_list = 'darkperiods';

    public function getModel($name = 'form', $prefix = '', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
}

