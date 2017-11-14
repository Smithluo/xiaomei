<?php

namespace common\models;

use common\helper\DateTimeHelper;
use common\helper\TextHelper;
use Yii;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "o_favourite_search".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $type
 * @property string $content
 * @property string $search_time
 * @property integer $search_count
 */
class FavouriteSearch extends \yii\db\ActiveRecord
{
    const TYPE_SHOP = 0;
    const TYPE_ARTICLE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_favourite_search';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'search_count', 'type'], 'integer'],
            [['search_time'], 'safe'],
            [['content'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'type' => 'Type',
            'content' => 'Content',
            'search_time' => 'Search Time',
            'search_count' => 'Search Count',
        ];
    }


    /**
     * 更新 用户的搜索记录
     * @param $userId
     * @param $keyword
     */
    public static function recordSearchKeywords($userId, $keyword, $type = self::TYPE_ARTICLE)
    {

        $data = FavouriteSearch::find()->where([
            'user_id' => $userId,
            'content' => (string)$keyword,
            'type' => $type,
        ])->one();

        if (empty($data)) {
            $data = new FavouriteSearch();
            $data->content = (string)$keyword;
            $data->user_id = $userId;
            $data->type = $type;
            $data->search_count = 0;
        }

        $data->search_time = date('Y-m-d H:i:s', time());
        ++$data->search_count;
        if (!$data->save()) {
            Yii::warning(' 关键词搜索入库失败 $data = '.VarDumper::export($data).
                '; errors = '.TextHelper::getErrorsMsg($data->errors));
        }

        $allRecords = FavouriteSearch::find()->where([
                'user_id' => $userId,
                'type' => $type,
            ])->orderBy([
                'search_time' => SORT_ASC
            ])->all();

        if(count($allRecords) > 10) {
            $allRecords[0]->delete();
        }
    }
}
