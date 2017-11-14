<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '分类';
$this->params['breadcrumbs'][] = $this->title;

function outputCategory($category, &$level) {
    $result = '
    <tr align="center" class="'.$level.'" id="'.$level.'_'.$category->cat_id.'">
            <td align="left" class="first-cell">  <img src="/images/menu_minus.gif" id="icon_'.$level.'_'.$category->cat_id.'" width="9" height="9" border="0" style="margin-left:0em" onclick="rowClicked(this)">  <span><a href="'.\yii\helpers\Url::to(['category/update', 'id' => $category->cat_id]).'">'.$category->cat_name.'</a></span></td>
            <td>'. $category->goodsCount .'</td>
            <td align="center"><img src="'. ($category->is_show ? '/images/yes.gif' : '/images/no.gif') . '" onclick="listTable.toggle(this, \'toggle_is_show\', '. $category->cat_id .')"></td>
            <td><span onclick="listTable.edit(this, \'edit_grade\', \''. $category->cat_id .'\'); return false;">0</span></td>
            <td><span onclick="listTable.edit(this, \'edit_sort_order\', \''. $category->cat_id .'\'); return false;">'.$category->sort_order.'</span></td>
            <td><a href="'. \yii\helpers\Url::to(['category/update', 'id' => $category->cat_id])  .'">编辑</a> |
                <a href="javascript:confirm_redirect(\'是否确认删除 '.$category->cat_name.' 分类及所有子分类\', \''. \yii\helpers\Url::to(['category/delete', 'id' => $category->cat_id])  .'\')" title="移除">移除</a>
            </td>
        </tr>
    ';

    echo $result;

    if(count($category->children) > 0) {
        ++$level;
        foreach($category->children as $child) {
            outputCategory($child, $level);
        }
        --$level;
    }
}

?>

<p>
    <?php $form = \yii\widgets\ActiveForm::begin([
        'action' => ['import'],
        'method' => 'post',
        'options' => ['enctype' => 'multipart/form-data'
        ]])
    ?>

    <?= $form->field($importModel, 'file')->fileInput() ?>

    <button>提交</button>

    <?php \yii\widgets\ActiveForm::end() ?>
</p>

<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建分类', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <table id="list-table" class="table table-bordered table-striped table-hover">
        <tbody><tr class="active">
            <th class="text-center">分类名称</th>
            <th class="text-center" width="10%">商品数量</th>
            <th class="text-center" width="10%">是否显示</th>
            <th class="text-center" width="10%">价格分级</th>
            <th class="text-center" width="10%">排序</th>
            <th class="text-center" width="10%">操作</th>
        </tr>
        <?php
            $index = 0;
            foreach($categories as $category) {
                outputCategory($category, $index);
            }
        ?>
        </tbody>
    </table>
</div>

<?php
//$this->registerCssFile('/js/fancybox/jquery.fancybox.css?v=2.1.5', ['position' => \yii\web\View::POS_HEAD]);
//$this->registerJsFile('/js/fancybox/jquery.fancybox.js?v=2.1.5', ['position' => \yii\web\View::POS_HEAD]);
//$this->registerJsFile('/js/artDialog/jquery.artDialog.js?skin=aero', ['position' => \yii\web\View::POS_HEAD]);
//$this->registerJsFile('/js/artDialog/plugins/iframeTools.js', ['position' => \yii\web\View::POS_HEAD]);
//$this->registerJsFile('/js/js/utils.js', ['position' => \yii\web\View::POS_HEAD]);
//$this->registerJsFile('/js/admin/common.js', ['position' => \yii\web\View::POS_HEAD]);

$this->registerJs('
/**
 * 确认后跳转到指定的URL
 */
function confirm_redirect(msg, url)
{
  if (confirm(msg))
  {
    location.href=url;
  }
}


var imgPlus = new Image();
imgPlus.src = "/images/menu_plus.gif";
/**
 * 折叠分类列表
 */
function rowClicked(obj){
  // 当前图像
  img = obj;
  // 取得上二级tr>td>img对象
  obj = obj.parentNode.parentNode;
  // 整个分类列表表格
  var tbl = document.getElementById("list-table");
  // 当前分类级别
  var lvl = parseInt(obj.className);
  // 是否找到元素
  var fnd = false;
  var sub_display = img.src.indexOf(\'menu_minus.gif\') > 0 ? \'none\' : \'table-row\' ;
  // 遍历所有的分类
  for (i = 0; i < tbl.rows.length; i++) {
      var row = tbl.rows[i];
      if (row == obj) {
          // 找到当前行
          fnd = true;
          //document.getElementById(\'result\').innerHTML += \'Find row at \' + i +"<br/>";
      } else {
          if (fnd == true) {
              var cur = parseInt(row.className);
              var icon = \'icon_\' + row.id;
              if (cur > lvl) {
                  row.style.display = sub_display;
                  if (sub_display != \'none\') {
                      var iconimg = document.getElementById(icon);
                      iconimg.src = iconimg.src.replace(\'plus.gif\', \'minus.gif\');
                  }
              } else {
                  fnd = false;
                  break;
              }
          }
      }
  }

  for (i = 0; i < obj.cells[0].childNodes.length; i++) {
      var imgObj = obj.cells[0].childNodes[i];
      if (imgObj.tagName == "IMG" && imgObj.src != \'/images/menu_arrow.gif\') {
          imgObj.src = (imgObj.src == imgPlus.src) ? \'/images/menu_minus.gif\' : imgPlus.src;
      }
  }
}', \yii\web\View::POS_END);
?>
