<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="article-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= \yii\bootstrap\Tabs::widget([
        'items' => [
            [
                'label' => '属性编辑',
                'content' => $this->render(
                    '_form_general',
                    [
                        'model' => $model,
                        'form' => $form,
                        'sceneMap' => $sceneMap,
                        'categoryTree' => $categoryTree,
                        'countryMap' => $countryMap,
                        'resourceTypeMap' => $resourceTypeMap,
                        'resourceSiteMap' => $resourceSiteMap,
                        'galleryMap' => $galleryMap,
                    ]
                ),
                'active' => true
            ],
            [
                'label' => '内容编辑',
                'content' => $this->render('_form_content', ['model' => $model, 'form' => $form]),
            ],
        ]]);
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
