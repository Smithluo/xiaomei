<?php
/**
 * 用来自动从富文本(contentAttribute)中使用正则查找到视频源，并截取出10s时的帧，设置为ActiveRecord的某个属性（imageAttribute）
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-20
 * Time: 19:45
 */

namespace common\behaviors;

use common\helper\FileHelper;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class VideoImageBehavior extends Behavior
{
    public $contentAttribute = 'content';
    public $imageAttribute = 'pic';

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
        ];
    }

    public function getVideoUrl() {
        $model = $this->owner;
        $preg='/<video .*?src="(.*?)".*?>/is';
        $content = htmlspecialchars_decode(stripslashes($model[$this->contentAttribute]));
        $result = preg_match($preg, $content, $match);
        if ($result > 0) {
            return $match[1];
        }
        return false;
    }

    public function getVideoPath() {
        $url = $this->getVideoUrl();
        if (!empty($url)) {
            $localPath = str_replace(Yii::$app->params['shop_config']['img_base_url'], '', $url);
            $absPath = Yii::getAlias('@imgRoot'). '/'. $localPath;
            return $absPath;
        }
        return false;
    }

    public function beforeInsert() {
        $model = $this->owner;
        //处理视频抽帧
        if ($model->scenario == 'insert') {
            $path = $this->getVideoPath();
            if (!empty($path)) {

                //把图的图片名设进去，然后在afterSave的时候获取id后拼出要上传的路径，进行文件上传
                $fileName = uniqid(). '.jpg';
                $model[$this->imageAttribute] = $fileName;

            }
        }
    }

    public function afterInsert() {
        $model = $this->owner;
        if ($model->scenario == 'insert') {
            $path = $this->getVideoPath();
            if (!empty($path)) {

                $ffmpeg = FFMpeg::create([
                    'ffmpeg.binaries' => Yii::$app->params['ffmpegPath'],
                    'ffprobe.binaries' => Yii::$app->params['ffprobePath'],
                    'timeout' => 3600,
                    'ffmpeg.threads' => 12,
                ]);
                $video = $ffmpeg->open($path);

                $savePath = $model->getUploadPath($this->imageAttribute);
                if (!empty($savePath)) {
                    $dir = str_replace($model[$this->imageAttribute], '', $savePath);
                    FileHelper::createDirectory($dir, 0777);
                    //从10秒处抽帧
                    $video->frame(TimeCode::fromSeconds(10))->save($savePath);
                }
            }
        }

    }

}