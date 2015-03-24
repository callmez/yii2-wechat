<?php
namespace callmez\wechat\modules\admin\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use yii\db\ActiveRecord;

class Wechat extends \callmez\wechat\models\Wechat
{
    /**
     * 头像上传组件
     * @var UploadedFile|null
     */
    public $uploadAvatar;
    /**
     * 二维码上传组件
     * @var UploadedFile|null
     */
    public $uploadQrCode;

    public function rules()
    {
        return array_merge([
            [['uploadAvatar', 'uploadQrCode'], 'checkUpload', 'skipOnEmpty' => false],
            [['uploadAvatar', 'uploadQrCode'], 'file', 'extensions' => ['gif', 'jpg', 'png'], 'skipOnEmpty' => true],
            [['uploadAvatar', 'uploadQrCode'], 'applyUpload', 'skipOnEmpty' => true],
        ], parent::rules());
    }

    /**
     * 检查是否有上传
     * @param $attribute
     * @param $params
     */
    public function checkUpload($attribute, $params)
    {
        $this->$attribute = UploadedFile::getInstance($this, $attribute);
    }

    /**
     * 上传对应的字段对象
     * @var array
     */
    protected $uploadMap = [
        'uploadAvatar' => 'avatar',
        'uploadQrCode' => 'qr_code'
    ];

    /**
     * 上传的图片赋值到指定字段对象中
     * @param $attribute
     * @param $params
     */
    public function applyUpload($attribute, $params)
    {
        if (!isset($this->uploadMap[$attribute])) {
            $this->addError($attribute, '没有上传转换对象');
        }
        $targetAttribute = $this->uploadMap[$attribute];
        $file = $this->$attribute;
        if ($file instanceof UploadedFile) {
            $path = '/wechat/' . $attribute . '/' . md5($this->hash) . '.' . $file->getExtension();
            $realpath = Yii::getAlias('@storageRoot' . $path);
            FileHelper::createDirectory(dirname($realpath));
            if ($file->saveAs($realpath)) {
                $this->$targetAttribute = $path;
            }
        }
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'uploadAvatar' => '头像地址',
            'uploadQrCode' => '二维码地址',
        ]);
    }
}