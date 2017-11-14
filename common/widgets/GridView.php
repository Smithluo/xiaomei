<?php

namespace common\widgets;

use yii\helpers\Html;
use yii\widgets\BaseListView;

class GridView extends \yii\grid\GridView {

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->summary = '';
        $this->options = [
            'class' => 'ibox-content',
        ];
        $this->tableOptions = array_merge($this->tableOptions, [
            'class' => 'footable table table-stripped toggle-arrow-tiny',
            'data-page-size' => 15,
            'xm-node' => 'dataList'
        ]);
//        $this->pager = [
//            'class' => RightLinkPager::className(),
//        ];
        $this->showFooter = true;
    }

    public function run()
    {
//        $id = $this->options['id'];
//        $options = Json::htmlEncode($this->getClientOptions());
//        $view = $this->getView();
//        GridViewAsset::register($view);
//        $view->registerJs("jQuery('#$id').yiiGridView($options);");
        BaseListView::run();
    }

    /**
     * Renders the data models for the grid view.
     */
    public function renderItems()
    {
        $caption = $this->renderCaption();
        $columnGroup = $this->renderColumnGroup();
        $tableHeader = $this->showHeader ? $this->renderTableHeader() : false;
        $tableBody = $this->renderTableBody();
        $tableFooter = $this->showFooter ? $this->renderTableFooter() : false;
        $content = array_filter([
            $caption,
            $columnGroup,
            $tableHeader,
            $tableBody,
            $tableFooter,
        ]);

        return Html::tag('table', implode("\n", $content), $this->tableOptions);
    }

}