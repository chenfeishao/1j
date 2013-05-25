<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>
/* @var $this <?php echo $this->getControllerClass(); ?> */
/* @var $model <?php echo $this->getModelClass(); ?> */
/* @var $form CActiveForm */
?>

<?php echo "<?php echo \$form->errorSummary(\$model); ?>\n"; ?>
<?php echo "<?php if(!\$model->isNewRecord) echo \$form->hiddenField(\$model, 'id', array('id' => 'modelId', 'name' => 'modelId')); ?>\n"; ?>
<?php
foreach($this->tableSchema->columns as $column)
{
	if($column->autoIncrement)
		continue;
?>
<?php echo "<?php echo \$form->textFieldRow(\$model, '{$column->name}');?>\n"; ?>
<?php
}
?>