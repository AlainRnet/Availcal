<?php
/**
 * @version		$Id: default.php 21322 2011-05-11 01:10:29Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	mod_login
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
//include css file
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base() . 'components/com_availcal/assets/plg_css.css');
//Get parameters
$name = $params->get('name');
$week_firstday = $params->get('week_firstday', 0);
$firstlast = $params->get('firstlast', 0);
$customCss = $params->get('customCss', "default");
$weeknumberdisplay = $params->get('week_number', 0);
//Get current date
$dateNow = new JDate();
$currentYear = $dateNow->Format('Y');
$currentMonth = (int) $dateNow->Format('m');
$dayofMonth = $dateNow->Format('d');
$month = $currentMonth;
$year = $currentYear;
$month_name = JTEXT::_('month_' . $month);
$first_of_month = mktime(0, 0, 0, $month, 1, $year);
$maxdays = date('t', $first_of_month);
$date_info = getdate($first_of_month);
$space = "&nbsp; ";
$monthplus = $month + 1;
$monthmin = $month - 1;
$modid = $module->id;
$colspan = 5 + $weeknumberdisplay;
?>
<div class="availcal" data-week_firstday="<?php echo $week_firstday ?>" data-firstlast="<?php echo $firstlast ?>" data-wnbrdisplay="<?php echo $weeknumberdisplay ?>">
    <div class="table_pos">
        <table class="cal_main"  >
            <tr class="cal_title">
                <th><a href="#" id="makeRequest<?php echo $modid; ?>">&lt;</a></th>
                <th colspan="<?php echo $colspan ?>" class="cal_month"><div id="availcalheader<?php echo $modid; ?>">
                <?php echo $month_name . " " . $year ?>
            </div></th>
            <th><a href="#" id="makeRequest2<?php echo $modid; ?>">&gt;</a></th>
            </tr>
            <?php if ($week_firstday == 0) : ?>
                <tr class="cal_days">
                    <?php if ($weeknumberdisplay == 1) echo '<td class="weeknbr">&#35;</td>' ?>
                    <td><?php echo JTEXT::_('zo') ?></td>
                    <td><?php echo JTEXT::_('ma') ?></td>
                    <td><?php echo JTEXT::_('di') ?></td>
                    <td><?php echo JTEXT::_('wo') ?></td>
                    <td><?php echo JTEXT::_('do') ?></td>
                    <td><?php echo JTEXT::_('vr') ?></td>
                    <td><?php echo JTEXT::_('za') ?></td>
                </tr>
                <?php $weekday = $date_info['wday']; ?>
            <?php else : ?>
                <tr class="cal_days">
                    <?php if ($weeknumberdisplay == 1) echo '<td class="weeknbr">&#35;</td>' ?>
                    <td><?php echo JTEXT::_('ma') ?></td>
                    <td><?php echo JTEXT::_('di') ?></td>
                    <td><?php echo JTEXT::_('wo') ?></td>
                    <td><?php echo JTEXT::_('do') ?></td>
                    <td><?php echo JTEXT::_('vr') ?></td>
                    <td><?php echo JTEXT::_('za') ?></td>
                    <td><?php echo JTEXT::_('zo') ?></td>
                </tr>

                <?php $weekday = $date_info['wday'] - 1; ?>
            <?php endif; ?>
        </table>

        <div id="result<?php echo $modid; ?>">
            <table class="cal_main" data-id="<?php echo $name ?>" data-year="<?php echo $year ?>" data-monthplus="<?php echo $monthplus ?>" data-monthmin="<?php echo $monthmin ?>">
                <tr>
                    <?php
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
                    ?>
                    <?php if ($weeknumberdisplay == 1): ?>
                        <td class='weeknbr'><?php echo $week; ?></td>
                    <?php endif; ?>
                    <?php if ($weekday > 0): ?>
                        <td colspan="<?php echo $weekday; ?>">&nbsp;</td>
                    <?php endif; ?>
                    <?php $teller = 0; ?>
                    <?php while ($day <= $maxdays): ?>
                        <?php
                        $linkDate = mktime(0, 0, 0, $month, $day, $year);
                        $week = (int) date('W', $linkDate);
                        if ($week_firstday == 0) {
                            $week = (int) date('W', ($linkDate + 60 * 60 * 24));
                        }
                        ?>
                        <?php if ($weekday == 7): ?>
                        </tr>
                        <tr>
                            <?php if ($weeknumberdisplay == 1): ?>
                                <td class='weeknbr'> <?php echo $week; ?>
                                <?php endif; ?>
                                <?php
                                $weekday = 0;
                                $teller++;
                                ?>
                            <?php endif; ?>
                            <?php
                            if (($day == $dayofMonth) and ($year == $currentYear) and ($month == $currentMonth)) {
                                $class = 'cal_today';
                            } else {
                                $darken = 7;
                                if (isset($dark_days)) {
                                    foreach ($dark_days as $dark) {

                                        if (($linkDate <= $dark['end']) and ($linkDate >= $dark['start'])) {
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
                                                if (($linkDate == $dark['start']) AND ($linkDate == $dark['end'])) {
                                                    $darken = $dark['busy'];
                                                }
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
                            ?>
                        <td class="<?php echo $class; ?>"><?php echo $day; ?><br /></td>
                        <?php
                        $day++;
                        $weekday++;
                        ?>
                    <?php endwhile; ?>
                    <?php if ($weekday != 7): ?>

                        <td colspan="<?php echo (7 - $weekday); ?>">&nbsp;</td>
                    <?php endif; ?>
                </tr>
                <?php if ($teller < 5) : ?>
                    <tr>
                        <?php if ($weeknumberdisplay == 1) : ?>
                            <td class="weeknbr"> <?php echo $space; ?></td>
                        <?php endif; ?>
                        <td colspan="7" > <?php echo $space; ?></td>
                    </tr>
                <?php endif; ?>
                <tr class="legend_first" >
                    <?php if ($weeknumberdisplay == 1) : ?>
                        <td class="weeknbr"> <?php echo $space; ?></td>
                    <?php endif; ?>

                    <td class="cal_post display_post"><?php echo $space; ?></td>
                    <td class="display_post" colspan="2"><?php echo JTEXT::_('BUSY'); ?></td><td></td>
                    <td class="cal_part display_part"><?php echo $space; ?></td>
                    <td class="display_part" colspan="2"><?php echo JTEXT::_('PART'); ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<?php
JHtml::_('jquery.framework');
$js = " 		
 		jQuery(document).ready(function($)	{
 			var week_firstday =  $('.availcal').data('week_firstday');
 			var firstlast =  $('.availcal').data('firstlast');
			var wnbrdisplay =  $('.availcal').data('wnbrdisplay');
			$('#makeRequest$modid').click(function()	{
 				var id = $('#result$modid > .cal_main').data('id');
 				var year = $('#result$modid > .cal_main').data('year');
 				var monthmin = $('#result$modid > .cal_main').data('monthmin');
				$('#result$modid').load ('".JURI::base()."index.php?option=com_availcal&format=update&id=' + id + '&month=' + monthmin + '&year=' + year + '&week_firstday=' + week_firstday + '&firstlast=' + firstlast + '&type=module' + '&wnbrdisplay='+ wnbrdisplay,
				function() { $('#availcalheader$modid').text($('#result$modid > .cal_main').data('monthname'))});
				return false;
			});
 			$('#makeRequest2$modid').click(function()	{
 				var id = $('#result$modid > .cal_main').data('id');
 				var year = $('#result$modid > .cal_main').data('year');
 				var monthplus = $('#result$modid > .cal_main').data('monthplus');
				$('#result$modid').load ('".JURI::base()."index.php?option=com_availcal&format=update&id=' + id + '&month=' + monthplus + '&year=' + year + '&week_firstday=' + week_firstday + '&firstlast=' + firstlast + '&type=module' + '&wnbrdisplay='+ wnbrdisplay,
				function() { $('#availcalheader$modid').text($('#result$modid > .cal_main').data('monthname'))});				
				return false;
			});
		
		});		
";

// add JavaScript to the page
$document->addScriptDeclaration($js);
if ($customCss != "default") {
    $document->addStyleDeclaration($customCss);
}
?>