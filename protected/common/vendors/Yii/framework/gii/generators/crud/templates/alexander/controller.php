<?php
/**
 * This is the template for generating a controller class file for CRUD feature.
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>

class <?php echo $this->controllerClass; ?> extends <?php echo $this->baseControllerClass."\n"; ?>
{
	private $headerTitle = '标题';
	private $headerNew = '新建';
	private $headerEdit = '修改';
	private $headerDelete = '删除';
	/**
	 * @return bool
	 */
	protected function beforeAction($action)
	{
		parent::beforeAction($action);
		$this->pageTitle = $this->headerTitle.'-'.Yii::app()->name;
		return true;
	}

	public function behaviors()
	{
		return array(
			'GetSideMenu' => array(
				'class' => 'backend.modules.catalog.controllers.Behaviors.GetSideMenuBehavior'
			),
		);
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new <?php echo $this->modelClass; ?>('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['<?php echo $this->modelClass; ?>']))
			$model->attributes=$_GET['<?php echo $this->modelClass; ?>'];

		$this->breadcrumbs = array(
			array('label' => '目录'),
            array('label' => $this->headerTitle, 'link' => '#'),
        );
		$this->render('index',array(
			'model'=>$model,
			'headerTitle' => $this->headerTitle,
			'headerOperate' => array(
				array('label' => $this->headerNew, 'class'=>'btn-primary', 'icon'=>'plus-sign', 'link' => $this->createUrl('/<?php echo $this->getModule()->id.'/'.$this->class2id($this->modelClass); ?>/create')),
				array('label' => $this->headerDelete, 'class'=>'btn-danger', 'icon'=>'remove-sign', 'link' => $this->createUrl('/<?php echo $this->getModule()->id.'/'.$this->class2id($this->modelClass); ?>/ajaxbatchedit',array('act'=>'doDelete')), 'id' => 'batchDelete'),
			)
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new <?php echo $this->modelClass; ?>;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['<?php echo $this->modelClass; ?>']))
		{
			if(isset($_POST['modelId']))
			{
				$model = $this->loadModel($_POST['modelId']);
			}
			$model->attributes=$_POST['<?php echo $this->modelClass; ?>'];
			if($model->save())
				$this->redirect(array('index'));
		}

		$this->breadcrumbs = array(
			array('label' => '目录'),
            array('label' => $this->headerTitle, 'link' => $this->createUrl('/<?php echo $this->getModule()->id.'/'.$this->class2id($this->modelClass); ?>/index')),
            array('label' => $this->headerNew, 'link' => '#')
        );
		$this->render('create',array(
			'model'=>$model,
			'headerTitle' => $this->headerTitle
		));
	}

	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		$this->breadcrumbs = array(
			array('label' => '目录'),
            array('label' => $this->headerTitle, 'link' => $this->createUrl('/<?php echo $this->getModule()->id.'/'.$this->class2id($this->modelClass); ?>/index')),
            array('label' => $this->headerEdit, 'link' => '#')
        );
        $this->render('create',array(
			'model'=>$model,
			'headerTitle' => $this->headerTitle,
			'headerOperate' => array(
				array('label' => $this->headerNew, 'class'=>'btn-primary', 'icon'=>'plus-sign', 'link' => $this->createUrl('/<?php echo $this->getModule()->id.'/'.$this->class2id($this->modelClass); ?>/create')),
				array('label' => $this->headerDelete, 'class'=>'btn-danger', 'icon'=>'remove-sign', 'link' => $this->createUrl('/<?php echo $this->getModule()->id.'/'.$this->class2id($this->modelClass); ?>/delete',array('id'=>$id)), 'id' => 'sigleDelete'),
			)
		));
	}

	/**
	 * 生成带选项卡的表单
	 *
	 * @param $form 表单
	 * @param $model 要渲染的模型
	 */
	public function getTabularFormTabs($form, $model)
	{
		$tabs = array();
		$count = 0;
		$groups = array(
			'General' => '一般',
			'Data' => '数据',
			'Design' => '设计'
		);

		foreach($groups as $key => $label)
		{
			$tabs[] = array(
				'active' => $count++ === 0,
				'label' => $label,
				'content' => $this->renderPartial("_tab{$key}Form", array('form' => $form, 'model' => $model), true),
			);
		}

		return $tabs;
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return <?php echo $this->modelClass; ?> the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=<?php echo $this->modelClass; ?>::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param <?php echo $this->modelClass; ?> $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='<?php echo $this->class2id($this->modelClass); ?>-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
