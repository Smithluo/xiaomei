<?php

namespace common\behaviors;

use Yii;
use yii\web\UploadedFile;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/15 0015
 * Time: 16:03
 */
class UploadImageBehavior extends \mongosoft\file\UploadImageBehavior
{
//    public $host = 'http://img.xiaomei360.com';
    public $pathRoot = '@mRoot/';
    public $storePrefix = '';
    public $arrayKey = null;

    public function getUploadPath($attribute, $old = false)
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        $path = $this->resolvePath($this->path);
        $fileName = ($old === true) ? $model->getOldAttribute($attribute) : $model->$attribute;
        if (!empty($this->storePrefix)) {
            $prefix = $this->resolvePath($this->storePrefix);
            $fileName = str_replace($prefix, '', $fileName);
        }

        $result =  $fileName ? Yii::getAlias($path . '/' . $fileName) : null;
        if (!empty($result)) {
            $result = str_replace('//', '/', $result);
        }
        return $result;
    }

    public function getThumbUploadPath($attribute, $profile = 'thumb', $old = false)
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        $path = $this->resolvePath($this->thumbPath);
        $attribute = ($old === true) ? $model->getOldAttribute($attribute) : $model->$attribute;
        $fileName = $this->getThumbFileName($attribute, $profile);
        if (!empty($this->storePrefix)) {
            $prefix = $this->resolvePath($this->storePrefix);
            $fileName = str_replace($prefix, '', $fileName);
        }
        $result = $fileName ? Yii::getAlias($path . '/' . $fileName) : null;
        if (!empty($result)) {
            $result = str_replace('//', '/', $result);
        }
        return $result;
    }

    /**
     * This method is invoked before validation starts.
     */
    public function beforeValidate()
    {
        $model = $this->owner;
        if (in_array($model->scenario, $this->scenarios)) {
            if (($file = $model->getAttribute($this->attribute)) instanceof UploadedFile) {
                $this->_file = $file;
            } else {
                if ($this->instanceByName === true) {
                    $this->_file = UploadedFile::getInstanceByName($this->attribute);
                } else {
                    $this->_file = UploadedFile::getInstance($model, (isset($this->arrayKey) ? '['. $this->arrayKey. ']' : ''). $this->attribute);
                }
            }
            if ($this->_file instanceof UploadedFile) {
                $this->_file->name = $this->getFileName($this->_file);
                $model->setAttribute($this->attribute, $this->_file);
            }
        }
    }

    /**
     * This method is called at the beginning of inserting or updating a record.
     */
    public function beforeSave()
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        if (in_array($model->scenario, $this->scenarios)) {
            if ($this->_file instanceof UploadedFile) {
                if (!$model->getIsNewRecord() && $model->isAttributeChanged($this->attribute)) {
                    if ($this->unlinkOnSave === true) {
                        $this->delete($this->attribute, true);
                    }
                }
                $filePath = $this->getUploadPath($this->attribute);
                $pathRoot = \Yii::getAlias($this->pathRoot);
                $storeName = str_replace($pathRoot, '', $filePath);
                $model->setAttribute($this->attribute, $storeName);
            } else {
                // Protect attribute
                unset($model->{$this->attribute});
            }
        } else {
            if (!$model->getIsNewRecord() && $model->isAttributeChanged($this->attribute)) {
                if ($this->unlinkOnSave === true) {
                    $this->delete($this->attribute, true);
                }
            }
        }
    }
}