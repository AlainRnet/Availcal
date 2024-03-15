<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
?>
<form
    action="<?php echo JRoute::_('index.php?option=com_availcal&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="item-form" class="form-validate">
    <?php echo $this->getToolbar(); ?> 

    <fieldset>
        <legend>
<?php echo JText::_('COM_AVAILCAL_DETAILS'); ?>
        </legend>

<?php foreach ($this->form->getFieldset() as $field): ?>


    <?php echo $field->label ?>


    <?php echo $field->input; ?>


<?php endforeach; ?>

    </fieldset>

    <input type="hidden" name="task" value="darkperiod.save" />
    <input type = "hidden" name = "option" value = "com_availcal" />
<?php echo JHtml::_('form.token'); ?>

</form>
