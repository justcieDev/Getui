<?php
/**
 * Created by PhpStorm.
 * User: justice
 * Date: 2018/11/2
 * Time: 3:44 PM
 */

namespace Getui\libarys\igetui\model;

class IGtSend {

    /**
     * 通知标题
     * @var
     */
    var $title;

    /**
     * 通知内容
     * @var
     */
    var $body;

    /**
     * 通知内容中携带的内容
     * @var
     */
    var $payload;

    /**
     * APN发送类型（simple模式或者高级模式）
     * @var
     */
    var $APNType = 'SIMPLE';

    /**
     * App的Logo
     * @var
     */
    var $logo = '';

    /**
     * 通知栏logo链接
     * @var
     */
    var $logoUrl = '';
    /**
     * android内部通信的内容
     * @var
     */
    var $intent;
    /**
     * 下载时的等待图片
     * @var
     */
    var $loadIcon;
    /**
     * 下载链接
     * @var
     */
    var $loadUrl;
    /**
     * NotyPopLoad的类型（分为notice 通知栏，bomb 弹框，download 直接下载）三种类型
     * @var
     */
    var $NotyPopLoadType;
    /**
     * webUrl的网页跳转地址
     * @var
     */
    var $webUrl;
    /**
     * 透传内容
     * @var
     */
    var $transmissionContent;
}