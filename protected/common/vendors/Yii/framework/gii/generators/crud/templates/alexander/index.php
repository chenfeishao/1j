<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>
/* @var $this <?php echo $this->getControllerClass(); ?> */
/* @var $model <?php echo $this->getModelClass(); ?> */
/* @var $module <?php echo $this->getModule()->id; ?> */

	<?php echo "
	\$this->renderPartial('backend.views.layouts._topnavbar');
	\$this->renderPartial('backend.views.layouts._sidebar');\n";?>
?>

<div id="content">
	<?php echo "<?php \$this->renderPartial('backend.views.layouts._contentHeader', array('headerTitle' => \$headerTitle, 'headerOperate' => (isset(\$headerOperate))?\$headerOperate:array()));?>\n"; ?>
	<?php echo "<?php \$this->renderPartial('backend.views.layouts._breadcrumb'); ?>\n";?>
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
			<?php echo "<?php\n";?>
				$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array('enableAjaxValidation' => true));
				$this->widget('bootstrap.widgets.TbGridView', array(
					'id'=>'<?php echo $this->class2id($this->modelClass);?>-grid',
					'dataProvider'=>$model->search(),
					'type' => array('bordered','hover'),
					'filter'=>$model,
					'columns'=>array(
						array(
							'id' => 'id',
							'class' => 'CCheckBoxColumn',
							'selectableRows' => '50'
						),
						<?php
							$count = 0;
							foreach($this->tableSchema->columns as $column)
							{
								if(++$count == 7)
									echo "\t\t/*\n";
								if($column->name == 'id')	continue;
echo "\n\t\t\t\t\t\t'$column->name',\n";
							}
							if($count >= 7)
								echo "\t\t*/\n";
						?>
						array(
							'class'=>'bootstrap.widgets.TbButtonColumn',
							'template' => '{update} {delete}',
							'updateButtonUrl'=>'Yii::app()->createUrl("/<?php echo $this->getModule()->id.'/'.$this->class2id($this->modelClass); ?>/update", array("id" => $data->id))',
							'deleteButtonUrl'=>'Yii::app()->createUrl("/<?php echo $this->getModule()->id.'/'.$this->class2id($this->modelClass); ?>/delete", array("id" => $data->id))',
						),
					),
				)); 
				$this->endWidget();
			?>

			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(function(){
        $('#batchDelete').click(function(){
        	if(!confirm('确定要删除这些数据吗?')) return false;
           $.post(
                $(this).attr('href'),
                {id:$.fn.yiiGridView.getChecked('<?php echo $this->class2id($this->modelClass); ?>-grid', 'id')},
                reloadGrid
            );
            return false; 
        });
    });
    function reloadGrid(data) {
        $.fn.yiiGridView.update('<?php echo $this->class2id($this->modelClass); ?>-grid');
    }
</script>