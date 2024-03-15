<?php
/**
 * Availability Calendar Administrator xmlUpload model
 *
 * @package    	com_availcal
 * @subpackage 	components
 * @link
 * @license			GNU/GPL
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Import the JModel class
jimport( 'joomla.application.component.model' );
// joomla  date utilities
jimport('joomla.utilities.date');

/**
 *  Availability Calendar xmlUpload Model
 *
 * 	@package    	com_availcal
 * 	@subpackage 	components
 */
class AvailcalModelXmlupload extends JModelLegacy
{
	/**
	 *	Method to upload a XML file
	 *
	 *	@access public
	 *	@return object
	 */
	function readfile()
	{
		if(!defined('DS')){
			define('DS',DIRECTORY_SEPARATOR);
		}		
		//Retrieve file details from uploaded file, sent from upload form
                $jinput = JFactory::getApplication()->input;
                $file =  $jinput->files->get('xmlupload');		

		//Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.file');

		//Clean up filename to get rid of strange characters like spaces etc
		$filename = JFile::makeSafe($file['name']);

		//Set up the source and destination of the file
		$src = $file['tmp_name'];
		$dest = JPATH_ROOT . DS . "tmp" . DS . $filename;
		if ( strtolower(JFile::getExt($filename) ) == 'xml') {
			if ( JFile::upload($src, $dest) ) {
				return $dest;
			} else {
				return false;
			}
		} else {
			return false;
		}
			
			
		return true;
	}
	/**
	 *
	 * Method to convert XML file
	 *
	 * @access public
	 *	@return object
	 */
	function convertXml($dest)
	{
		if(!defined('DS')){
			define('DS',DIRECTORY_SEPARATOR);
		}
		$xml=JFactory::getXML($dest);		
		
		if ($xml->getName() != 'objects')
		{
			$this->errorCode = JText::_( 'WRONG XML' );
			return false;
		}
		Foreach ($xml->object as $object)	{
			$name = (string)$object['name'];
			Foreach ($object->darkperiod as $darkperiod)	{
			if ($busy = $darkperiod->busy)
				{
					if ($busy < 2)
					{
						if ($startdate = $darkperiod->startdate)
						{
							if ($enddate = $darkperiod->enddate)
							{
								$table = $this->getTable('Darkperiod', 'AvailCalTable');
								$start_date = new JDate($startdate);
								$end_date = new  JDate($enddate);
									
								$record['name'] = $name;
								$record['start_date'] = $start_date->toSQL();
								$record['end_date'] = $end_date->toSQL();
								$record['busy'] = (string)$busy;
								if ($remark = $darkperiod->remark)
								{
									$record['remarks'] = (string)$remark;
								}
								$table->reset();
								// Bind the data to the table
								if( !$table->bind($record))
								{
									$this->setError( $this->_db->getErrorMsg());
									return false;
								}

								// Validate the data
								if( !$table->check())
								{
									$this->setError( $this->_db->getErrorMsg());
									return false;
								}

								// Store the revue
								if( !$table->store())
								{
									// An error occurred, update the model error message
									$this->setError( $table->getErrorMsg());
									return false;
								}
							}
						}
					}
				}
			}
			
		}	

		return TRUE;
	}
	function deleteFile($dest)	
	{
		if (JFile::delete($dest))	{
			return true;		
		} else {
			return false;
		}		
	}
}

