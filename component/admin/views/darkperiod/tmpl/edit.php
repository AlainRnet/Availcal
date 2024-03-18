<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');

?>
<form
	action="<?php echo JRoute::_('index.php?option=com_availcal&layout=edit&id='.(int) $this->item->id); ?>"
	method="post" name="adminForm" id="item-form" class="form-validate">

	<div class="span10 form-horizontal">
		<fieldset>
			<legend>
				<?php echo JText::_( 'COM_AVAILCAL_DETAILS' ); ?>
			</legend>
			<div class="tab-content">
				<?php foreach($this->form->getFieldset() as $field): ?>
				<div class="control-group">
					<div class="control-label">
						<?php echo $field->label ?>
					</div>
					<div class="controls">
						<?php echo $field->input; ?>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</fieldset>
	</div>

	<input type="hidden" name="task" value="darkperiod.edit" />
	<?php echo JHtml::_('form.token'); ?>

</form>
<script type="text/javascript">
function VerifDate()
{if (document.getElementById("jform_end_date").value<document.getElementById("jform_start_date").value) {
document.getElementById('jform_end_date').value=document.getElementById('jform_start_date').value;
document.getElementById('jform_end_date').setAttribute('data-alt-value',document.getElementById('jform_start_date').value);
}}
</script>
