<?php
namespace callmez\wechat\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use yii\validators\Validator;

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
    public $type;
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
    /**
     * 公众号
     * @var Wechat
     */
    protected $wechat;

    /**
     * @inhertdoc
     */
    public function __construct(Wechat $wechat, $config = [])
    {
        $this->wechat = $wechat;
        parent::__construct($config);
    }

    /**
     * @inhertdoc
     */
    public function rules()
    {
        return [
            [['type', 'material', 'file'], 'required'],
            [['type'], 'in', 'range' => array_keys(Media::$types)],
            [['material'], 'in', 'range' => array_keys(Media::$materialTypes)],
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'jpg, amr, mp3, mp4', 'maxSize' => 10485760], // 10MB
            [['file'], 'checkFile']
        ];
    }

    /**
     * 各类型上传文件验证
     * @param $attribute
     * @param $params
     */
    public function checkFile($attribute, $params)
    {
        // 按照类型 验证上传
        switch ($this->type) {
            case Media::TYPE_IMAGE:
                $rule = [[$attribute], 'file', 'skipOnEmpty' => false, 'extensions' => 'jpg', 'maxSize' => 1048576]; // 1MB
                break;
            case Media::TYPE_THUMB:
                $rule = [[$attribute], 'file', 'skipOnEmpty' => false, 'extensions' => 'jpg', 'maxSize' => 524288]; // 64KB
                break;
            case Media::TYPE_VOICE:
                $rule = [[$attribute], 'file', 'skipOnEmpty' => false, 'extensions' => 'amr, mp3', 'maxSize' => 2097152]; // 2MB
                break;
            case Media::TYPE_VIDEO:
                $rule = [[$attribute], 'file', 'skipOnEmpty' => false, 'extensions' => 'mp4', 'maxSize' => 10485760]; // 10MB
                break;
            default:
                return ;
        }
        $validator = Validator::createValidator($rule[1], $this, (array) $rule[0], array_slice($rule, 2));
        $validator->validateAttributes($this);
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

    /**
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        if ($this->material == Media::MATERIAL_TEMPORARY) {
            $method = 'uploadMedia';
        } elseif ($this->material == Media::MATERIAL_PERMANENT) {
            $method = 'uploadMedia';
        } else {
            $this->addError('material', '错误的素材类别');
            return false;
        }
        $sdk = $this->wechat->getSdk();
        $path = '@runtime/temp/' . md5($_SERVER['REQUEST_TIME_FLOAT']) . '/' . $this->file->name;
        $filePath = Yii::getAlias($path);
        FileHelper::createDirectory(dirname($filePath));
        $this->file->saveAs($filePath);
        $data = [];
        if (!($result = call_user_func_array([$sdk, $method], [$filePath, $this->type, $data]))) {
            $this->addError('file', json_encode($sdk->lastError));
            return false;
        }
        $media = Yii::createObject(Media::className());
        $media->setAttributes([
            'mediaId' => $result['media_id'],
            'filename' => $this->file->name,
            'type' => $this->type,
            'material' => $this->material,
            'result' => $result
        ]);
        return $media->save();
    }
}