<?php

use yii\db\Schema;
use yii\db\Migration;
use callmez\wechat\models\Wechat;
use callmez\wechat\models\Rule;
use callmez\wechat\models\RuleKeyword;

class m141017_131752_initWechat extends Migration
{
    public function up()
    {
        //微信公众号表
        $tableName = Wechat::tableName();
        $this->createTable($tableName, [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . "(40) NOT NULL DEFAULT '' COMMENT '公众号名称'",
            'hash' => Schema::TYPE_STRING . "(5) NOT NULL DEFAULT '' COMMENT '公众号名称'",
            'token' => Schema::TYPE_STRING . "(32) NOT NULL DEFAULT '' COMMENT '微信服务访问验证token'",
            'access_token' => Schema::TYPE_STRING . "(600) NOT NULL DEFAULT '' COMMENT '访问微信服务验证token'",
            'account' => Schema::TYPE_STRING . "(30) NOT NULL DEFAULT '' COMMENT '微信号'",
            'original' => Schema::TYPE_STRING . "(40) NOT NULL DEFAULT '' COMMENT '原始ID'",
            'type' => Schema::TYPE_BOOLEAN . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '公众号类型'",
            'app_id' => Schema::TYPE_STRING . "(50) NOT NULL DEFAULT '' COMMENT 'AppID'",
            'app_secret' => Schema::TYPE_STRING . "(50) NOT NULL DEFAULT '' COMMENT 'AppSecret'",
            'encoding_type' => Schema::TYPE_BOOLEAN . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '消息加密方式'",
            'encoding_aes_key' => Schema::TYPE_STRING . "(43) NOT NULL DEFAULT '' COMMENT '消息加密秘钥EncodingAesKey'",
            'avatar' => Schema::TYPE_STRING . " NOT NULL DEFAULT '' COMMENT '头像地址'",
            'qr_code' => Schema::TYPE_STRING . " NOT NULL DEFAULT '' COMMENT '二维码地址'",
            'address' => Schema::TYPE_STRING . " NOT NULL DEFAULT '' COMMENT '地址'",
            'description' => Schema::TYPE_STRING . " NOT NULL DEFAULT '' COMMENT '公众号简介'",
            'username' => Schema::TYPE_STRING . "(40) NOT NULL DEFAULT '' COMMENT '微信官网登录名'",
            'status' => Schema::TYPE_BOOLEAN . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态'",
            'password' => Schema::TYPE_STRING . "(32) NOT NULL DEFAULT '' COMMENT '微信官网登录密码'",
            'created_at' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间'"
        ]);
        $this->createIndex('hash', $tableName, 'hash', true);
        $this->createIndex('app_id', $tableName, 'app_id');

        // 规则表
        $tableName = Rule::tablename();
        $this->createTable($tableName, [
            'id' => Schema::TYPE_PK,
            'wid' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属微信公众号ID'",
            'name' => Schema::TYPE_STRING . "(50) NOT NULL DEFAULT '' COMMENT '规则名称'",
            'status' => Schema::TYPE_BOOLEAN . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态'",
            'priority' => Schema::TYPE_BOOLEAN . "(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '优先级'",
            'created_at' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间'"
        ]);

        // 规则关键字表
        $tableName = RuleKeyword::tablename();
        $this->createTable($tableName, [
            'id' => Schema::TYPE_PK,
            'rid' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属规则ID'",
            'processor' => Schema::TYPE_STRING . "(100) NOT NULL DEFAULT '' COMMENT '处理器, 可以是模块名或者action类的namspace'",
            'keyword' => Schema::TYPE_STRING . " NOT NULL DEFAULT '' COMMENT '规则关键字'",
            'type' => Schema::TYPE_STRING . " NOT NULL DEFAULT '' COMMENT '关键字类型'",
            'status' => Schema::TYPE_BOOLEAN . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态'",
            'priority' => Schema::TYPE_BOOLEAN . "(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '优先级'",
            'created_at' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间'"
        ]);
        $this->createIndex('keyword', $tableName, 'keyword');

//        //测试数据
//        $wechat = new Wechat([
//            'name' => '画卷人生',
//            'hash' => 'dVbLx',
//            'token' => 'lL2hcT4c41H3wL21Hlftlz4A4w22j12L',
//            'access_token' => [
//                'token' => 'CDo8N0BKhq61zMumyFYTr2noGsq37qJvGaacJhKNEUo09Yaur0e3hE0X9dn2Cs89Z-35jhk9pbHTgpPD0eWiTkzDJOhjtInxCVWTlFRg46A',
//                'expire' => 1413633588
//            ],
//            'account' => 'myslynfsl',
//            'original' => 'gh_c644bb981dee',
//            'app_id' => 'wx2ef4c6ce95a2b30f',
//            'app_secret' => '96916d01a08d154dd64c261eef3dea00',
//        ]);
//        $wechat->save();
//
//
//        $rule = new Rule([
//            'wid' => $wechat->id,
//            'name' => '测试规则',
//            'status' => Rule::STATUS_ACTIVE
//        ]);
//        $rule->save();
//
//        $ruleKeyword = new RuleKeyword([
//            'rid' => $rule->id,
//            'keyword' => '^test',
//            'type' => RuleKeyword::TYPE_REGULAR,
//            'status' => RuleKeyword::STATUS_ACTIVE,
//            'processor' => 'app\controllers\wechat\ApiAction'
//        ]);
//        $ruleKeyword->save();
    }

    public function down()
    {
        $this->dropTable(Wechat::tableName());
        $this->dropTable(Rule::tableName());
        $this->dropTable(RuleKeyword::tableName());
        return true;
    }
}
