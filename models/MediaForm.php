<?php
namespace callmez\wechat\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * 素材上传(表单)验证类
 * @package callmez\wechat\models
 */
class MediaForm extends Model
{
    /**
     * 素材类型
     * @var string
     */
    public $type = Media::TYPE_IMAGE;
    /**
     * 素材类别
     * @var string
     */
    public $material = Media::MATERIAL_TEMPORARY;
    /**
     * 上传文件
     * @var UploadedFile
     */
    public $file;

    public function rules()
    {
        return [
            [['type', 'material', 'file'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => '素材类型',
            'material' => '素材类别',
            'file' => '素材'
        ];
    }

    public function upload()
    {
        if (!$this->validate()) {
            return false;
        }

    }
}