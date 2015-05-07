<?php

use yii\db\Schema;
use yii\db\Migration;
use callmez\wechat\models\Fans;
use callmez\wechat\models\Wechat;
use callmez\wechat\models\Module;
use callmez\wechat\models\ReplyRule;
use callmez\wechat\models\AddonModule;
use callmez\wechat\models\MessageHistory;
use callmez\wechat\models\ReplyRuleKeyword;

class m150217_131752_initWechat extends Migration
{
    public function safeUp()
    {
        $this->initWechatTable();
        $this->initAddonModuleTable();
        $this->initReplyRuleTable();
        $this->initFansTable();
        $this->initMessageHistoryTable();
    }

    public function safeDown()
    {
        $this->dropTable(Wechat::tableName());
        $this->dropTable(ReplyRule::tableName());
        $this->dropTable(ReplyRuleKeyword::tableName());
        $this->dropTable(Fans::tableName());
        $this->dropTable(MessageHistory::tableName());
    }

    /**
     * 公众号表
     */
    public function initWechatTable()
    {
        $tableName = Wechat::tableName();
        $this->createTable($tableName, [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . "(40) NOT NULL DEFAULT '' COMMENT '公众号名称'",
            'hash' => Schema::TYPE_STRING . "(5) NOT NULL DEFAULT '' COMMENT '公众号名称'",
            'token' => Schema::TYPE_STRING . "(32) NOT NULL DEFAULT '' COMMENT '微信服务访问验证token'",
            'access_token' => Schema::TYPE_STRING . " NOT NULL DEFAULT '' COMMENT '访问微信服务验证token'",
            'account' => Schema::TYPE_STRING . "(30) NOT NULL DEFAULT '' COMMENT '微信号'",
            'original' => Schema::TYPE_STRING . "(40) NOT NULL DEFAULT '' COMMENT '原始ID'",
            'type' => Schema::TYPE_BOOLEAN . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '公众号类型'",
            'app_id' => Schema::TYPE_STRING . "(50) NOT NULL DEFAULT '' COMMENT 'AppID'",
            'app_secret' => Schema::TYPE_STRING . "(50) NOT NULL DEFAULT '' COMMENT 'AppSecret'",
            'encoding_type' => Schema::TYPE_BOOLEAN . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '消息加密方式'",
            'encoding_aes_key' => Schema::TYPE_STRING . "(43) NOT NULL DEFAULT '' COMMENT '消息加密秘钥EncodingAesKey'",
            'avatar' => Schema::TYPE_STRING . " NOT NULL DEFAULT '' COMMENT '头像地址'",
            'qr_code' => Schema::TYPE_STRING . " NOT NULL DEFAULT '' COMMENT '二维码地址'",
            'address' => Schema::TYPE_STRING . " NOT NULL DEFAULT '' COMMENT '所在地址'",
            'description' => Schema::TYPE_STRING . " NOT NULL DEFAULT '' COMMENT '公众号简介'",
            'username' => Schema::TYPE_STRING . "(40) NOT NULL DEFAULT '' COMMENT '微信官网登录名'",
            'status' => Schema::TYPE_BOOLEAN . " NOT NULL DEFAULT '0' COMMENT '状态'",
            'password' => Schema::TYPE_STRING . "(32) NOT NULL DEFAULT '' COMMENT '微信官网登录密码'",
            'created_at' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间'"
        ]);
        $this->createIndex('hash', $tableName, 'hash', true);
        $this->createIndex('app_id', $tableName, 'app_id');
    }

    /**
     * 插件模块表
     */
    public function initAddonModuleTable()
    {
        $tableName = AddonModule::tableName();
        $this->createTable($tableName, [
            'id' => Schema::TYPE_STRING . "(20) NOT NULL DEFAULT '' COMMENT '模块ID'",
            'name' => Schema::TYPE_STRING . "(50) NOT NULL DEFAULT '' COMMENT '模块名称'",
            'type' => Schema::TYPE_STRING . "(20) NOT NULL DEFAULT '' COMMENT '模块类型'",
            'version' => Schema::TYPE_STRING . "(10) NOT NULL DEFAULT '' COMMENT '模块版本'",
            'ability' => Schema::TYPE_STRING . "(100) NOT NULL DEFAULT '' COMMENT '模块功能简述'",
            'description' => Schema::TYPE_TEXT . " NOT NULL COMMENT '模块详细描述'",
            'author' => Schema::TYPE_STRING . "(50) NOT NULL DEFAULT '' COMMENT '模块作者'",
            'site' => Schema::TYPE_STRING . " NOT NULL DEFAULT '' COMMENT '模块详情地址'",
            'migration' => Schema::TYPE_BOOLEAN . " NOT NULL DEFAULT '0' COMMENT '是否有迁移数据'",
            'created_at' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间'",
        ]);
        $this->addPrimaryKey('id', $tableName, 'id');
    }

    /**
     * 回复规则表
     */
    public function initReplyRuleTable()
    {
        $tableName = ReplyRule::tablename();
        $this->createTable($tableName, [
            'id' => Schema::TYPE_PK,
            'wid' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属微信公众号ID'",
            'name' => Schema::TYPE_STRING . "(50) NOT NULL DEFAULT '' COMMENT '规则名称'",
            'module' => Schema::TYPE_STRING . "(20) NOT NULL DEFAULT '' COMMENT '处理的插件模块'",
            'status' => Schema::TYPE_BOOLEAN . " NOT NULL DEFAULT '0' COMMENT '状态'",
            'priority' => Schema::TYPE_BOOLEAN . "(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '优先级'",
            'created_at' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间'"
        ]);
        $this->createIndex('wid', $tableName, 'wid');

        // 回复规则关键字表
        $tableName = ReplyRuleKeyword::tablename();
        $this->createTable($tableName, [
            'id' => Schema::TYPE_PK,
            'rid' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属规则ID'",
            'keyword' => Schema::TYPE_STRING . " NOT NULL DEFAULT '' COMMENT '规则关键字'",
            'type' => Schema::TYPE_BOOLEAN . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '关键字类型'",
            'priority' => Schema::TYPE_BOOLEAN . "(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '优先级'",
            'created_at' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'updated_at' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间'"
        ]);
        $this->createIndex('keyword', $tableName, 'keyword');
        $this->createIndex('rid', $tableName, 'rid');
    }

    /**
     * 粉丝表
     */
    public function initFansTable()
    {
        $tableName = Fans::tableName();
        $this->createTable($tableName, [
            'id' => Schema::TYPE_PK,
            'wid' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属微信公众号ID'",
            'open_id' => Schema::TYPE_STRING . "(50) NOT NULL DEFAULT '' COMMENT '微信ID'",
            'status' => Schema::TYPE_BOOLEAN . " NOT NULL DEFAULT '0' COMMENT '关注状态'",
            'created_at' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注时间'",
            'updated_at' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '修改时间'"
        ]);
        $this->createIndex('wid', $tableName, 'wid');
        $this->createIndex('open_id', $tableName, 'open_id');
    }

    /**
     * 消息记录表
     */
    public function initMessageHistoryTable()
    {
        $tableName = MessageHistory::tableName();
        $this->createTable($tableName, [
            'id' => Schema::TYPE_PK,
            'wid' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属微信公众号ID'",
            'rid' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属规则ID'",
            'kid' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属关键字ID'",
            'open_id' => Schema::TYPE_STRING . "(50) NOT NULL DEFAULT '' COMMENT '请求用户微信ID'",
            'module' => Schema::TYPE_STRING . "(20) NOT NULL DEFAULT '' COMMENT '处理模块'",
            'message' => Schema::TYPE_TEXT . " NOT NULL COMMENT '消息体内容'",
            'type' => Schema::TYPE_STRING . "(10) NOT NULL DEFAULT '' COMMENT '发送类型'",
            'created_at' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注时间'",
        ]);
        $this->createIndex('wid', $tableName, 'wid');
        $this->createIndex('open_id', $tableName, 'open_id');
        $this->createIndex('module', $tableName, 'module');
    }
}