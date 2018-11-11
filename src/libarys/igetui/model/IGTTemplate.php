<?php
/**
 * Created by PhpStorm.
 * User: justice
 * Date: 2018/11/7
 * Time: 2:48 PM
 */

namespace Getui\libarys\igetui\model;


class IGTTemplate {
    // 1.IGTTRANSMISSION:透传功能模板
    // 2.IGTLINK:通知打开链接功能模板
    // 3.IGTNOTIFICATION：通知透传功能模板
    // 4.IGTNOTYPOPLOAD：通知弹框下载功能模板

    public static $IGTNOTIFICATION = 'IGtNotificationTemplate';
    public static $IGTLINK = 'IGtLinkTemplate';
    public static $IGTNOTYPOPLOAD = 'IGtNotyPopLoadTemplate';
    public static $IGTTRANSMISSION = 'IGtTransmissionTemplate';
}