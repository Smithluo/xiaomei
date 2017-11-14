<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '权限列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="permission-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <table>
        <tr>
            <th>
                权限
            </th>
            <th>
                用户名
            </th>
        </tr>

    <?php foreach ($permissions as $permission): ?>
        <?php
        $userIds = Yii::$app->authManager->getUserIdsByRole($permission->name);
        if (empty($userIds)) {
            continue;
        }
        $users = \common\models\Users::find()->where([
            'user_id' => $userIds,
        ])->all();
        $rawData = '';
        foreach ($users as $user) {
            $userStr = $user->getShowName(). '('. $user->mobile_phone. ')';
            $rawData .= Html::a($userStr, \yii\helpers\Url::to([
                    'sc-user/view',
                    'id' => $user->user_id,
                ]), [
                    'target' => '_blank',
                ]). PHP_EOL;
        }
        ?>
        <tr>
            <td>
                <?= $permission->name ?>
            </td>
            <td>
                <?= $rawData ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>


    <h1>角色列表</h1>
    <table>
        <tr>
            <th>
                角色
            </th>
            <th>
                用户名
            </th>
        </tr>

        <?php foreach ($roles as $role): ?>
            <?php
            $userIds = Yii::$app->authManager->getUserIdsByRole($role->name);
            if (empty($userIds)) {
                continue;
            }
            $users = \common\models\Users::find()->where([
                'user_id' => $userIds,
            ])->all();
            $rawData = '';
            foreach ($users as $user) {
                $userStr = $user->getShowName(). '('. $user->mobile_phone. ')';
                $rawData .= Html::a($userStr, \yii\helpers\Url::to([
                        'sc-user/view',
                        'id' => $user->user_id,
                    ]), [
                        'target' => '_blank',
                    ]). PHP_EOL;
            }
            ?>
            <tr>
                <td>
                    <?= $role->name ?>
                </td>
                <td>
                    <?= $rawData ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>