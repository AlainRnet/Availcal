<?php

/**
 * Avalilability Calendar Plugin
 *
 * Author			: Jan Maat
 * Date				: 20 october 2010
 * email			: jan.maat@hetnet.nl
 * copyright		: Jan Maat 2010
 * 1.6 migration    : Jan Maat, Pedro Manuel Baeza
 * Migration date	: 8 september 2011
 * @license			: GNU/GPL
 * Description		: Displays the availability Calendar in an article on the position {availcal="name"}
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

// Import the JPlugin class
jimport('joomla.event.plugin');

/**
 *  Availability Calendar event listener
 *
 */
class plgContentAvailcal extends JPlugin {

    /**
     * The regular expression used to detect if availcal has been embedded into the article
     *
     * @var		string Regular Expression
     * @access	protected
     * @since	1.0
     */
    protected $_regex1 = '/{availcal.*?}/i';

    /**
     * The regular expression used to parse the argument
     *
     * @var		string Regular Expression
     * @access	protected
     * @since	1.0
     */
    protected $_regex2 = '/{availcal=("|�?|“)(.+)("|�?|“)}/i';

    //Constructor
    function plgContentAvailcal(&$subject, $params) {
        parent::__construct($subject, $params);
        $this->loadLanguage();
    }

    /**
     *  Handle onPrepareAvailcal
     *
     */
    public function onContentPrepare($context, &$article, &$params, $page = 0) {

        // just startup
        $mainframe = JFactory::getApplication();

        // Only render in the HTML format
        $document = JFactory::getDocument();
        $type = $document->getType();
        $html = ( $type == 'html' );
        $text = &$article->text;

        //include css file
        $css = JURI::base() . 'components/com_availcal/assets/plg_css.css';
        $document->addStyleSheet($css);

        // Check if the Availcal Component is installed and enabled
        $enabled = JComponentHelper::isEnabled('com_availcal', true);

        // Should we proceed
        if (!$enabled || !$html) {
            // Remove any availcal tags from the content
            $article->text = preg_replace($this->_regex1, '', $article->text);
            return true;
        }




        // find all instances of plugin and put in $matches
        preg_match_all($this->_regex1, $article->text, $declarations);

        // Return if there are no matches
        if (!count($declarations[0])) {
            return true;
        }

        //Get the params
        $nbr_months = $this->params->def('nbr_months', 1);
        $week_firstday = $this->params->def('week_firstday', 0);
        $display_mode = $this->params->def('display_mode', 0);
        $firstlast = $this->params->def('firstlast', 0);
        $customCss = $this->params->def('customCss', "default");
        $weeknumberdisplay = $this->params->def('week_number', 0);

        //Get current date
        $dateNow = JFactory::getDate();
        $currentYear = $dateNow->Format('Y');
        $currentMonth = (int) $dateNow->Format('m');
        $dayofMonth = $dateNow->Format('d');

        //Array for the dark periods
        $dark_days = array();

        //Get the database
        $db = JFactory::getDBO();
        // single part of the jquery script
        $js = "
		jQuery(document).ready(function($)	{
		var week_firstday =  $('.availcal').data('week_firstday');
		var firstlast =  $('.availcal').data('firstlast');
		var wnbrdisplay =  $('.availcal').data('wnbrdisplay');
		";

        //Loop to place the replacement of all matches

        foreach ($declarations[0] as $x => $declaration) {

            preg_match_all($this->_regex2, $declaration, $matches);
            $calendar = '';

            // Get the query id
            if (isset($matches[2][0])) {
                $name = $matches[2][0];
                $regex3 = '/' . $matches[0][0] . '/';
                $namearray[$x] = $name;
                $month = $currentMonth;
                $year = $currentYear;

                //Get Darkperiods
                $query = 'SELECT start_date,end_date,busy FROM' . $db->quoteName('#__avail_calendar') . 'WHERE' . $db->quoteName('name') . ' = ' . $db->Quote($name);
                $db->setQuery($query);
                $db->execute();

                //Check of object has one or more darkperiods				
                $dark_days = array();
                if ($num_rows = $db->getNumRows()) {
                    $counter = 0;
                    $line = $db->loadAssocList();
                    while ($counter < $num_rows) {
                        $dark_days[$counter]['start'] = strtotime($line[$counter]['start_date']);
                        $dark_days[$counter]['end'] = strtotime($line[$counter]['end_date']);
                        $dark_days[$counter]['busy'] = $line[$counter]['busy'];
                        $counter++;
                    }
                }
                //Built Calendar
                // Build availcal div
                $calendar = "<div class=\"availcal\" data-week_firstday=\"$week_firstday\" data-firstlast=\"$firstlast\" data-wnbrdisplay=\"$weeknumberdisplay\">";
                $nbr_months = ($display_mode == 1) ? 1 : $nbr_months;
                $nbr_months = ($display_mode == 2) ? 3 : $nbr_months;
                for ($i = 0; $i < $nbr_months; $i++, $month++) {
                    if ($month == 13) {
                        $month = 1;
                        $year++;
                    }

                    $month_name = JTEXT::_('month_' . $month);
                    $first_of_month = mktime(0, 0, 0, $month, 1, $year);
                    $maxdays = date('t', $first_of_month);
                    $date_info = getdate($first_of_month);

                    //Built 1 calendar
                    $calendar .="<div class=\"table_pos\">";
                    //Header table
                    $calendar .= "<table class=\"cal_main\"  >";
                    if (($display_mode == 0) OR ( ($display_mode == 2) AND ( $i == 1))) {
                        $colspan = 7 + $weeknumberdisplay;
                        $calendar .= "<tr class=\"cal_title\"><th colspan=\"$colspan\" class=\"cal_month\"><div id=\"availcalheader$i$x\">$month_name $year</div></th></tr>";
                    } else {
                        $colspan = 5 + $weeknumberdisplay;
                        if ($display_mode == 1) {
                            $calendar .= "	<tr class=\"cal_title\">
										<th><a href=\"#\" id=\"makeRequest$x\">&lt;</a></th>
										<th colspan=\"$colspan\" class=\"cal_month\">
										<div id=\"availcalheader$i$x\">$month_name $year</div></th>		
										<th><a href=\"#\" id=\"makeRequest2$x\">&gt;</a></th> 
                                                                                </tr><tr class=\"cal_days\">";
                        }
                        if (($display_mode == 2) AND ( $i == 0)) {
                            $calendar .= "	<tr class=\"cal_title\">
										<th><a href=\"#\" id=\"makeRequest$x\">&lt;</a></th>
										<th colspan=\"$colspan\" class=\"cal_month\">
										<div id=\"availcalheader$i$x\">$month_name $year</div></th>		
										<th></th> 
                                                                                </tr><tr class=\"cal_days\">";
                        }
                        if (($display_mode == 2) AND ( $i == 2)) {
                            $calendar .= "	<tr class=\"cal_title\">
										<th></th>
										<th colspan=\"$colspan\" class=\"cal_month\">
										<div id=\"availcalheader$i$x\">$month_name $year</div></th>		
										<th><a href=\"#\" id=\"makeRequest2$x\">&gt;</a></th> 
                                                                                </tr><tr class=\"cal_days\">";
                        }
                    }
                    if ($weeknumberdisplay == 1) {
                        $calendar .= "<td class=\"weeknbr\">&#35;</td>";
                    }
                    if ($week_firstday == 0) {
                        $calendar .= "<td>" . JTEXT::_('zo') . "</td><td>" . JTEXT::_('ma') . "</td><td>" . JTEXT::_('di') . "</td><td>" . JTEXT::_('wo') . "</td><td>" . JTEXT::_('do') . "</td><td>" . JTEXT::_('vr') . "</td><td>" . JTEXT::_('za') . "</td></tr>";
                        $weekday = $date_info['wday'];
                    } else {
                        $calendar .= "<td>" . JTEXT::_('ma') . "</td><td>" . JTEXT::_('di') . "</td><td>" . JTEXT::_('wo') . "</td><td>" . JTEXT::_('do') . "</td><td>" . JTEXT::_('vr') . "</td><td>" . JTEXT::_('za') . "</td><td>" . JTEXT::_('zo') . "</td></tr>";
                        $weekday = $date_info['wday'] - 1;
                        if ($weekday == -1) {
                            $weekday = 6;
                        }
                    }

                    $monthmin = $month - 1;
                    $monthplus = $month + 1;
                    $header = $month_name . " " . $year;
                    $calendar .= "</table><div id=\"result$i$x\">
                                <table class=\"cal_main\"
                                    data-id=\"$name\" 
                                    data-year=\"$year\" 
                                    data-monthplus=\"$monthplus\" 
                                    data-monthmin=\"$monthmin\"
                                    data-monthname=\"$header\" >";

                    //Body part Table
                    $calendar .= '<tr>';
                    //$weekday = $date_info['wday'];
                    $day = 1;
                    $linkDate = mktime(0, 0, 0, $month, $day, $year);
                    $week = (int) date('W', $linkDate);
                    if ($week_firstday == 0) {
                        $week = (int) date('W', ($linkDate + 60 * 60 * 24));
                    }
                    if ($weeknumberdisplay == 1) {
                        $calendar .= "<td class=\"weeknbr\">$week</td>";
                    }

                    if ($weekday > 0) {
                        $calendar .= "<td colspan=\"$weekday\">&nbsp;</td>\n";
                    }
                    $teller = 0;

                    while ($day <= $maxdays) {
                        $linkDate = mktime(0, 0, 0, $month, $day, $year);
                        $week = (int) date('W', $linkDate);
                        if ($week_firstday == 0) {
                            $week = (int) date('W', ($linkDate + 60 * 60 * 24));
                        }
                        if ($weekday == 7) {
                            $calendar .= "</tr>\n<tr>";
                            if ($weeknumberdisplay == 1) {
                                $calendar .= "<td class=\"weeknbr\">$week</td>";
                            }
                            $weekday = 0;
                            $teller++;
                        }


                        if (($day == $dayofMonth) and ( $year == $currentYear) and ( $month == $currentMonth)) {
                            $class = 'cal_today';
                        } else {
                            $darken = 7;
                            foreach ($dark_days as $dark) {

                                if (($linkDate <= $dark['end']) and ( $linkDate >= $dark['start'])) {
                                    $darken = $dark['busy'];
                                    if ($firstlast == 1) {
                                        if ($linkDate == $dark['start']) {
                                            if ($darken == 1) {
                                                $darken = 3;
                                            } else {
                                                $darken = 4;
                                            }
                                        }
                                        if ($linkDate == $dark['end']) {
                                            if ($darken == 1) {
                                                $darken = 5;
                                            } else {
                                                $darken = 6;
                                            }
                                        }
                                        if (($linkDate == $dark['start']) AND ( $linkDate == $dark['end'])) {
                                            $darken = $dark['busy'];
                                        }
                                    }
                                }
                            }
                            switch ($darken) {
                                case 1:
                                    $class = 'cal_post';
                                    $darken = 7;
                                    break;
                                case 0 :
                                    $class = 'cal_part';
                                    $darken = 7;
                                    break;
                                case 3 :
                                    $class = 'cal_firstday_post';
                                    $darken = 7;
                                    break;
                                case 4 :
                                    $class = 'cal_firstday_part';
                                    $darken = 7;
                                    break;
                                case 5 :
                                    $class = 'cal_lastday_post';
                                    $darken = 7;
                                    break;
                                case 6 :
                                    $class = 'cal_lastday_part';
                                    $darken = 7;
                                    break;
                                default :
                                    $class = 'cal_empty';
                                    $darken = 7;
                            }
                        }
                        $calendar .= "<td class=\"$class\">$day<br /></td>\n";
                        $day++;
                        $weekday++;
                    }

                    if ($weekday != 7) {
                        $calendar .= '<td colspan="' . (7 - $weekday) . '">&nbsp;</td>';
                    }
                    $space = " &nbsp; ";
                    $calendar .= "</tr>";
                    if ($teller < 5) {
                        $calendar .="<tr>";
                        if ($weeknumberdisplay == 1) {
                            $calendar .= "<td class=\"weeknbr\">$space</td>";
                        }
                        $calendar .= "<td colspan=\"7\">" . $space . "</td></tr><tr>";
                    }
                    if ($i > 0) {
                        $calendar .="<tr class=\"legend_next\">";
                    } else {
                        $calendar .="<tr class=\"legend_first\">";
                    }
                    if ($weeknumberdisplay == 1) {
                        $calendar .= "<td class=\"weeknbr\">$space</td>";
                    }
                    $calendar .= "<td class=\"cal_post display_post\">" . $space . "</td> <td class=\"display_post\" colspan=\"2\">"
                            . JTEXT::_('BUSY') . "</td><td></td>
								<td class=\"cal_part display_part\">" . $space . "</td><td class=\"display_part\" colspan=\"2\">"
                            . JTEXT::_('PART') . "</td>
								</tr>\n";

                    $calendar .= "</table></div></div>"; //End class table_pos
                }
                $calendar .= "</div>";



                //replace the calendar data
                $article->text = preg_replace($regex3, $calendar, $article->text);
            } else {
                $article->text = preg_replace($this->_regex1, '', $article->text);
            }
        } // end foreach


        
        //$path = JURI::base();
        JHtml::_('jquery.framework');
        if ($display_mode == 1) {
            if (isset($namearray)) {
                foreach ($namearray as $y => $id) {
                    $js .= "
			$('#makeRequest$y').click(function()	{
 				var id = $('#result0$y > .cal_main').data('id');
 				var year = $('#result0$y > .cal_main').data('year');
 				var monthmin = $('#result0$y > .cal_main').data('monthmin');
				$('#result0$y').load ('".JURI::base()."index.php?option=com_availcal&format=update&id=' + id + '&month=' + monthmin + '&year=' + year + '&week_firstday=' + week_firstday + '&firstlast=' + firstlast + '&type=plugin' + '&wnbrdisplay='+ wnbrdisplay,
				function() { $('#availcalheader0$y').text($('#result0$y > .cal_main').data('monthname'))});
				return false;
			});
 			$('#makeRequest2$y').click(function()	{
 				var id = $('#result0$y > .cal_main').data('id');
 				var year = $('#result0$y > .cal_main').data('year');
 				var monthplus = $('#result0$y > .cal_main').data('monthplus');
				$('#result0$y').load ('".JURI::base()."index.php?option=com_availcal&format=update&id=' + id + '&month=' + monthplus + '&year=' + year + '&week_firstday=' + week_firstday + '&firstlast=' + firstlast + '&type=plugin' + '&wnbrdisplay='+ wnbrdisplay,
				function() { $('#availcalheader0$y').text($('#result0$y > .cal_main').data('monthname'))});				
				return false;
			});			
	   	";
                }
                $js .= "	
		});		
		";
            }
        }
        If ($display_mode == 2) {
            if (isset($namearray)) {
                foreach ($namearray as $y => $id) {
                    $js .= "
			$('#makeRequest$y').click(function()	{
 				var id = $('#result0$y > .cal_main').data('id');
 				var year = $('#result0$y > .cal_main').data('year');
 				var monthmin = $('#result0$y > .cal_main').data('monthmin');
				$.get('".JURI::base()."index.php?option=com_availcal&format=update&id=' + id + '&month=' + monthmin + '&year=' + year + '&week_firstday=' + week_firstday + '&firstlast=' + firstlast + '&type=plugin3' + '&wnbrdisplay='+ wnbrdisplay,
				function(respondse)  {
                                    var result = respondse.split(',');
                                    $('#result0$y').html(result[0]);
                                    $('#availcalheader0$y').text($('#result0$y > .cal_main').data('monthname'));
                                    $('#result1$y').html(result[1]);
                                    $('#availcalheader1$y').text($('#result1$y > .cal_main').data('monthname'));
                                    $('#result2$y').html(result[2]);
                                    $('#availcalheader2$y').text($('#result2$y > .cal_main').data('monthname'));
                                });			
				return false;
			});
 			$('#makeRequest2$y').click(function()	{
 				var id = $('#result0$y > .cal_main').data('id');
 				var year = $('#result0$y > .cal_main').data('year');
 				var monthplus = $('#result0$y > .cal_main').data('monthplus');
				$.get ('".JURI::base()."index.php?option=com_availcal&format=update&id=' + id + '&month=' + monthplus + '&year=' + year + '&week_firstday=' + week_firstday + '&firstlast=' + firstlast + '&type=plugin3' + '&wnbrdisplay='+ wnbrdisplay,
				function(respondse) {
                                    var result = respondse.split(',');
                                    $('#result0$y').html(result[0]);
                                    $('#availcalheader0$y').text($('#result0$y > .cal_main').data('monthname'));
                                    $('#result1$y').html(result[1]);
                                    $('#availcalheader1$y').text($('#result1$y > .cal_main').data('monthname'));
                                    $('#result2$y').html(result[2]);
                                    $('#availcalheader2$y').text($('#result2$y > .cal_main').data('monthname'));
                                });				
				return false;
			});			
	   	";
                }
                $js .= "	
		});		
		";
            }
        }
        // add JavaScript to the page
        $document->addScriptDeclaration($js);
        if ($customCss != "default") {
            $document->addStyleDeclaration($customCss);
        }
    }

}
