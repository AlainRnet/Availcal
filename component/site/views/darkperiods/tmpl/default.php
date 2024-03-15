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

defined('_JEXEC') or die;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">	

    <?php echo $this->getToolbar(); ?> 
     
    <fieldset>        
        <label class="filter-search-lbl" for="filter-search">
            <?php echo JText::_('COM_AVAILCAL_TITLE_FILTER_LABEL') . '&#160;'; ?>
        </label>
        <input type="text" name="filter-search" id="filter-search" 
               value="<?php echo $this->escape($this->state->get('filter.search')); ?>" 
               class="inputbox" onchange="Joomla.submitform();" 
               title="<?php echo JText::_('COM_AVAILCAL_FILTER_SEARCH_DESC'); ?>" />

        <label for="limit" class="filter-search-lbl">
            <?php echo JText::_('JGLOBAL_DISPLAY_NUM') . '&#160;'; ?>
        </label>
        <?php echo $this->pagination->getLimitBox(); ?>        


    </fieldset>
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


    <div class="containerpg" >
         <div class="pagination">
         <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
    </div>
<input type="hidden"name="filter_order" value="<?php echo $listOrder; ?>" /> 
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<input type="hidden" name="limitstart" value="" />
<input type="hidden" name="task" value="" />
<input type = "hidden" name = "option" value = "com_availcal" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHtml::_('form.token'); ?>
</form>
