<?php
namespace callmez\wechat\modules\admin\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use yii\db\ActiveRecord;

class WechatForm extends \callmez\wechat\models\Wechat
{
    public function rules()
    {
        return array_merge([
            [['avatar', 'qr_code'], 'checkUpload', 'skipOnEmpty' => false],
            [['avatar', 'qr_code'], 'file', 'extensions' => ['gif', 'jpg', 'png']],
            [['avatar', 'qr_code'], 'applyUpload'],
        ], parent::rules());
    }

    /**
     * 检查是否有上传,无上传则复原旧属性
     * @param $attribute
     * @param $params
     */
    public function checkUpload($attribute, $params)
    {
        $this->$attribute = UploadedFile::getInstance($this, $attribute) ?: $this->getOldAttribute($attribute);
    }


    /**
     * 上传的图片赋值到指定字段对象中
     * @param $attribute
     * @param $params
     */
    public function applyUpload($attribute, $params)
    {
        $file = $this->$attribute;
        if ($file instanceof UploadedFile) {
            $path = '/wechat/' . $attribute . '/' . md5($this->hash) . '.' . $file->getExtension();
            $realpath = Yii::getAlias('@storageRoot' . $path);
            FileHelper::createDirectory(dirname($realpath));
            if ($file->saveAs($realpath)) {
                $this->$attribute = $path;
            }
        }
    }
}