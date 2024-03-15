<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 *  DarkperiodsList Model
 */
class AvailCalModelDarkperiods extends JModelList {

    /**
     * Constructor.
     *
     * @param	array	An optional associative array of configuration settings.
     * @see		JController
     * @since	1.6
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'name', 'a.name',
                'busy', 'a.busy',
                'start_date', 'a.start_date',
                'end_date', 'a.end_date',
                'remarks', 'a.remarks',
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return	void
     * @since	1.6
     */
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication();

        // Load the filter state.
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter-search');
        //Omit double (white-)spaces and set state
        $this->setState('filter.search', preg_replace('/\s+/', ' ', $search));

        // List state information.
        parent::populateState('a.name', 'asc');
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return	string	An SQL query
     */
    protected function getListQuery() {
        // Create a new query object.		
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $user = JFactory::getUser();
        // Select some fields
        $query->select('a.*');
        // From the Avail_Calendar table
        $query->from('#__avail_calendar AS a');

        // Filter by search in title.
        $search = $this->getState('filter.search');
        if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where('(a.name LIKE ' . $search . ')');
		}

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');

        $query->order($db->escape($orderCol . ' ' . $orderDirn));
        //echo nl2br(str_replace('#__','jos_',$query));
        return $query;
    }

}
