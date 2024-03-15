<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Darkperiod View
 */
class AvailCalViewForm extends JViewLegacy {

    /**
     * display method of Darkperiod view
     * @return void
     */
    public function display($tpl = null) {
        // get the Data
        $form = $this->get('Form');
        $item = $this->get('Item');
        $script = $this->get('Script');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
        // Assign the Data
        $this->form = $form;
        $this->item = $item;
        $this->script = $script;

        // Set the toolbar
        // Display the template
        parent::display($tpl);
        // Set the document
        $this->setDocument();
    }

    protected function setDocument() {
        $isNew = ($this->item->id < 1);
        $document = JFactory::getDocument();
        $document->setTitle($isNew ? JText::_('ADD') : JText::_('EDIT'));
        $document->addScript(JURI::root() . $this->script);
        $document->addScript(JURI::root() . "/administrator/components/com_availcal/views/darkperiod/submitbutton.js");
        JText::script('COM_AVAILCAL_AVAILCAL_ERROR_UNACCEPTABLE');
        JText::script('COM_AVAILCAL_AVAILCAL_ERROR_DATE');
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
        $bar->appendButton('Standard', 'save', 'save', 'darkperiod.save', false);
        $bar->appendButton('Separator');
        $bar->appendButton('Standard', 'cancel', 'Cancel', 'darkperiod.cancel', false);
        
        //generate the html and return
        return $bar->render();
    }

}
