<?php

/**
 * Availability Calendar Component Frontend Darkperiods View
 *
 * 	@package    	com_availcal
 * 	@subpackage 	components
 * 	@link
 * 	@license			GNU/GPL
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * Darkperiods View
 *
 * @package    com_availcal
 * @subpackage components
 */
class AvailCalViewDarkperiods extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;
    protected $params;

    /**
     * Accommodations view display method
     * @return void
     */
    function display($tpl = null) {
        $app = JFactory::getApplication();
        $params = $app->getParams();
        // Get data from the model
        $items = $this->get('Items');
        $pagination = $this->get('Pagination');
        $state = $this->get('State');
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
        // Assign data to the view
        $this->items = $items;
        $this->pagination = $pagination;
        $this->state = $state;

        // Display the template
        parent::display($tpl);
    }

    function getToolbar() {
        // add required stylesheets from admin template       
        $document = JFactory::getDocument();
        $document->addStyleSheet('administrator/templates/hathor/css/template.css');
        //now we add the necessary stylesheets from the administrator template
        //in this case i make reference to the bluestork default administrator template in joomla 1.6
        //load the JToolBar library and create a toolbar
        jimport('joomla.html.toolbar');
        $bar = new JToolBar('toolbar');
        //and make whatever calls you require
        $bar->appendButton('Standard', 'new', 'New', 'darkperiod.add', false);
        $bar->appendButton('Separator');
        $bar->appendButton('Standard', 'delete', 'Delete', 'darkperiods.delete', true);
        $bar->appendButton('Separator');
        $bar->appendButton('Standard', 'edit', 'Edit', 'darkperiod.edit', false);
        //generate the html and return
        return $bar->render();
    }

}
