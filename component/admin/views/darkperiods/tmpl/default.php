<?php
/**
 * 	Availability Calendar Component Administrator Darkperiods View
 *
 * 	@package    	com_availcal
 * 	@subpackage 	components
 * 	@link
 * 	@license			GNU/GPL
 */
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);


$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>

<form
    action="<?php echo JRoute::_('index.php?option=com_availcal&view=darkperiods'); ?>"
    method="post" name="adminForm" id="adminForm">

<?php if (!empty($this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
<?php else : ?>
            <div id="j-main-container">
        <?php endif; ?>


            <div id="filter-bar" class="btn-toolbar">
                <div class="btn-group pull-right hidden-phone">
                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
                    </label>
<?php echo $this->pagination->getLimitBox(); ?>
                </div>
                <div class="filter-search btn-group pull-left">
                    <input type="text" name="filter_search"
                           placeholder="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>"
                           id="filter_search"
                           value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
                           title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
                </div>
                <div class="btn-group pull-left">
                    <button class="btn tip hasTooltip" type="submit"
                            title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
                        <i class="icon-search"></i>
                    </button>
                    <button class="btn tip hasTooltip" type="button"
                            onclick="document.id('filter_search').value = '';this.form.submit();"
                            title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>">
                        <i class="icon-remove"></i>
                    </button>

                </div>
            </div>
            <div class="clearfix"></div>
<?php if (count($this->items)) : ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="20"><input type="checkbox" name="checkall-toggle"
                                                  value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
                                                  onclick="Joomla.checkAll(this)" />
                            </th>
                            <th width="100"><?php echo JHtml::_('grid.sort', 'NAAM', 'a.name', $listDirn, $listOrder); ?>
                            </th>
                            <th><?php echo JHtml::_('grid.sort', 'BUSY', 'a.busy', $listDirn, $listOrder); ?>
                            </th>
                            <th><?php echo JHtml::_('grid.sort', 'START_DATUM', 'a.start_date', $listDirn, $listOrder); ?>
                            </th>
                            <th><?php echo JHtml::_('grid.sort', 'END_DATUM', 'a.end_date', $listDirn, $listOrder); ?>
                            </th>
                            <th><?php echo JText::_('OPMERKING'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="6"><?php echo $this->pagination->getListFooter(); ?>
                            </td>
                        </tr>
                    </tfoot>

                    <tbody>
    <?php foreach ($this->items as $i => $item): ?>
                            <tr>
                                <td><?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                </td>
                                <td><a href="<?php echo JRoute::_('index.php?option=com_availcal&task=darkperiod.edit&id=' . $item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
        <?php echo $this->escape($item->name); ?></a>
                                </td>
                                <td><?php echo ($item->busy == 1 ? JText::_('full') : JText::_('partly')); ?>
                                </td>
                                <td><?php echo JHTML::_('date', $item->start_date, JTEXT::_('Y/m/d')); ?>
                                </td>
                                <td><?php echo JHTML::_('date', $item->end_date, JTEXT::_('Y/m/d')); ?>
                                </td>
                                <td><?php echo $item->remarks; ?>
                                </td>

                            </tr>
    <?php endforeach; ?>

                    </tbody>

                </table>
<?php endif; ?>
            <div>
                <input type="hidden" name="task" value="" /> 
                <input type="hidden" name="boxchecked" value="0" /> 
                <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" /> 
                <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<?php echo JHtml::_('form.token'); ?>
            </div>

            </form>

