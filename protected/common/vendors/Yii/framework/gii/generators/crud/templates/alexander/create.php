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
				<?php echo "<?php 
					\$form = \$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
						'id' => '".$this->class2id($this->modelClass)."-form',
						'enableClientValidation' => true,
						'type' => 'horizontal',
						'focus' => null,
						'action' => \$this->createUrl('/".$this->getModule()->id.'/'.$this->class2id($this->modelClass)."/create'),
						'clientOptions'=>array(
				            'validateOnSubmit'=>true,
				        ),
				        'htmlOptions' => array(
					       	'class' => 'widget-box'
					    ),
					));
				?>\n";?>
				<?php echo "<?php \$this->widget('backend.extensions.alexander.widgets.AdBoxTabs', array(
					'tabs' => \$this->getTabularFormTabs(\$form, \$model),
				)); ?>";?>

				<div class="form-actions">
					<?php echo "<?php 
						\$this->widget('bootstrap.widgets.TbButton', array(
								'type'=>'primary',
								'buttonType'=>'submit',
								'icon' => 'ok',
								'label'=> \$model->isNewRecord ? '新建' : '保存'
						));
						\$this->widget('bootstrap.widgets.TbButton', array(
								'type'=>'primary',
								'buttonType'=>'link',
								'url' => \$this->createUrl('/".$this->getModule()->id.'/'.$this->class2id($this->modelClass)."/index'),
								'label'=> '取消',
								'htmlOptions'=>array(
									'style' => 'margin-left:40px;'
								)
						));
					?>";?>	
				</div>
				<?php echo "<?php \$this->endWidget(); ?>\n"; ?>
			</div>
		</div>
	</div>
</div>