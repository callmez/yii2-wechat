<?php
namespace callmez\wechat\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use callmez\wechat\behaviors\ArrayBehavior;

/**
 * 素材存储表
 * @package callmez\wechat\models
 */
class Media extends ActiveRecord
{
    /**
     * 媒体素材(图片, 音频, 视频, 缩略图)
     */
    const TYPE_MEDIA = 'media';
    /**
     * 图文素材(永久)
     */
    const TYPE_NEWS = 'news';
    /**
     * 图片素材
     */
    const TYPE_IMAGE = 'image';
    /**
     * 音频素材
     */
    const TYPE_VOICE = 'voice';
    /**
     * 视频素材
     */
    const TYPE_VIDEO = 'video';
    /**
     * 缩略图素材
     */
    const TYPE_THUMB = 'thumb';
    /**
     * 临时素材
     */
    const MATERIAL_TEMPORARY = 'tomporary';
    /**
     * 永久素材
     */
    const MATERIAL_PERMANENT = 'permanent';
    /**
     * 素材类型
     * @var array
     */
    public static $types = [
        self::TYPE_IMAGE => '图片',
        self::TYPE_THUMB => '缩略图',
        self::TYPE_VOICE => '语音',
        self::TYPE_VIDEO => '视频',
    ];
    /**
     * 素材统称
     * @var array
     */
    public static $mediaTypes = [
        self::TYPE_MEDIA => '媒体素材',
        self::TYPE_NEWS => '图文素材'
    ];
    /**
     * 素材类别
     * @var array
     */
    public static $materialTypes = [
        self::MATERIAL_TEMPORARY => '临时素材',
        self::MATERIAL_PERMANENT => '永久素材'
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::className(),
            'array' => [
                'class' => ArrayBehavior::className(),
                'attributes' => [
                    ArrayBehavior::TYPE_SERIALIZE => ['result']
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wechat_media}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filename', 'result'], 'required'],
            [['mediaId', 'filename'], 'string', 'max' => 100],
            [['type'], 'string', 'max' => 10],
            [['material'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mediaId' => '媒体ID',
            'filename' => '文件名',
            'result' => '响应内容',
            'type' => '媒体类型',
            'material' => '素材类别',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}