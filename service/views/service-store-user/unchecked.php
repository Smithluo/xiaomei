<?php

use yii\helpers\Html;
use common\models\Users;
use common\models\Region;
use common\widgets\GridView;
use common\helper\DateTimeHelper;

\service\assets\UserUncheckedAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel service\models\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '待审核列表';
$this->params['breadcrumbs'][] = $this->title;
$this->params['steel_boot'] = 'app/service/storeCheck';
?>
<div class="wrapper wrapper-content animated fadeInRight">
<?php echo $this->render('_search', [
    'model' => $searchModel,
    'checked' => $checked
]); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox">
            <div class="ibox-content">
                <div id="editable_wrapper" class="dataTables_wrapper form-inline">
                    <?php if ($models) : ?>
                        <table class="table table-striped table-bordered table-hover" id="editable" data-page-size="15">
                            <thead>
                            <tr>
                                <th>会员ID</th>
                                <th>联系人</th>
                                <th>移动电话</th>
                                <th>门店名称<i class="fa fa-edit" style="margin-left:6px;"></i></th>
                                <th>注册时间</th>
                                <th>审核状态</th>
                                <th>所在省份</th>
                                <th>所在城市</th>
                                <th>备注<i class="fa fa-edit" style="margin-left:6px;"></i></th>
                                <th class="text-right">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($models as $model) : ?>
                                <tr class="gradeX" id="<?=$model->user_id?>">
                                    <td><?=$model->user_id?></td>
                                    <td><?=$model->getUserShowName()?></td>
                                    <td><?=$model->mobile_phone?></td>
                                    <td class="center" xm-data="editorable"><?=$model->company_name?></td>
                                    <td class="center"><?=DateTimeHelper::getFormatCNDateTime($model->reg_time)?></td>
                                    <td class="center">
                                        <span class="label label-danger">
                                            <?=Users::$is_checked_map[$model->is_checked]?>
                                        </span>
                                    </td>
                                    <td class="center"><?=empty($model->provinceRegion)?'':$model->provinceRegion->region_name?></td>
                                    <td class="center"><?=empty($model->cityRegion)?'':$model->cityRegion->region_name?></td>
                                    <td class="center" xm-data="editorable"><?= $model->checked_note?></td>
                                    <td class="text-right">
                                        <?php if($model->is_checked != Users::IS_CHECKED_STATUS_BLACK ){?>
                                        <a href="identify?id=<?=$model->user_id?>"  class="btn btn-outline btn-primary" xm-action="passReply">去审核</a>
                                        <?php }?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div></div>

