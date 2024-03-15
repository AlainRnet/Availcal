<?php

/**
 * Availability Calendar Site view
 *
 * @package    	com_availcal
 * @subpackage 	components
 * @link
 * @license			GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the AvailCal Component
 * HTML Request View
 */
class AvailcalViewCalendar extends JViewLegacy {

    // Overwriting JView display method
    function display($tpl = null) {

        //Get current date
        $dateNow = new JDate();
        $currentYear = $dateNow->Format('Y');
        $currentMonth = (int) $dateNow->Format('m');
        //Get request input
        $month = JRequest::getVar('month', $currentMonth);
        $year = JRequest::getVar('year', $currentYear);
        $id = JRequest::getVar('id');
        $y = JRequest::getVar('y');
        $type = Jrequest::getVar('type');
        $weeknumberdisplay = JRequest:: getVar('wnbrdisplay');
        //Load language file
        $language = JFactory::getLanguage();
        if (($type == 'plugin') OR ( $type == 'plugin3')) {
            $language->load('plg_content_availcal', JPATH_SITE . '/plugins/content/availcal');
        } else {
            $language->load('mod_availcal', JPATH_SITE . '/modules/mod_availcal');
        }
        $calendar = ""; //initiate calender variable
        //Determin month to display
        if ($month == 13) {
            $month = 1;
            $year = $year + 1;
        }
        if ($month == 0) {
            if ($year <= $currentYear) {
                $year = $currentYear;
                $month = 1;
            } else {
                $month = 12;
                $year = $year - 1;
            }
        }
        if ($month < $currentMonth AND $year == $currentYear) {
            $month = $currentMonth;
        }
        $monthmin = $month - 1;
        $monthplus = $month + 1;

        $loop_counter = 1;
        $loop_counter = ($type == 'plugin3') ? 3 : $loop_counter;
        for ($ii = 0; $ii < $loop_counter; $ii++, $month++) {
            if ($month == 13) {
                $month = 1;
                $year++;
            }
            if (($ii > 0) AND ( $type == 'plugin3')) {
                $calendar .= ',';
            }
            $dayofMonth = $dateNow->Format('d');
            $first_of_month = mktime(0, 0, 0, $month, 1, $year);
            $maxdays = date('t', $first_of_month);
            $date_info = getdate($first_of_month);
            //Array for the dark periods
            $dark_days = array();

            $month_name = JTEXT::_('month_' . $month);
            $week_firstday = JRequest::getVar('week_firstday', 0);
            $firstlast = JRequest::getVar('firstlast', 0);
            $header = $month_name ." " . $year;
            //Get dark periods
            $items = $this->get('Items');

            //built Calendar


            foreach ($items as $i => $item) {
                $dark_days[$i]['start'] = strtotime($item->start_date);
                $dark_days[$i]['end'] = strtotime($item->end_date);
                $dark_days[$i]['busy'] = $item->busy;
            }

            $calendar .="<table class=\"cal_main\" data-id=\"$id\" 
												data-year=\"$year\" 
												data-monthplus=\"$monthplus\" 
												data-monthmin=\"$monthmin\"
												data-monthname=\"$header\">";
            $calendar .= '<tr>';
            if ($week_firstday == 0) {
                $weekday = $date_info['wday'];
            } else {
                $weekday = $date_info['wday'] - 1;
                if ($weekday == -1) {
                    $weekday = 6;
                }
            }
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
                    $d = date('m/d/Y', $linkDate);
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
                            }
                            if (($linkDate == $dark['start']) AND ( $linkDate == $dark['end'])) {
                                $darken = $dark['busy'];
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
                $calendar .= "<td colspan=\"7\">" . $space . "</td></tr>";
            }
            $calendar .="<tr class=\"legend_first\">";
            if ($weeknumberdisplay == 1) {
                $calendar .= "<td class=\"weeknbr\">$space</td>";
            }
            $calendar .= "<td class=\"cal_post display_post\">" . $space . "</td> <td class=\"display_post\" colspan=\"2\">"
                    . JTEXT::_('BUSY') . "</td><td></td>
						
						<td class=\"cal_part display_part\">" . $space . "</td><td class=\"display_part\" colspan=\"2\">"
                    . JTEXT::_('PART') . "</td>
						
										</tr>\n";

            $calendar .= "</table>\n"; //End class table_pos
        } //End for
        echo $calendar;
    }

}
