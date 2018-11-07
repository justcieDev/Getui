<?php

namespace Getui;

use Getui\libarys\IGtPush;
use Getui\libarys\igetui\model\IGtSend;
use Getui\libarys\igetui\model\IGtNotify;

class Push {

    // 参数赋值，host地址，appKey，appId,masterSecret,clientId
    protected $host = 'https://sdk.open.api.igexin.com/apiex.htm';
    protected $appKey;
    protected $appId;
    protected $masterSecret;
    protected $IGtPush;
    protected $sendModel;

    public function __construct($appKey, $masterSecret, $appId, IGtSend $sendModel)
    {
        $this->appKey = $appKey;
        $this->masterSecret = $masterSecret;
        $this->appId = $appId;
        $this->sendModel = $sendModel;
        $this->IGtPush = new IGtPush($this->host, $this->appKey, $this->masterSecret);
    }

    // 大数据综合分析用户得到的标签:即用户画像
    public function getPersonaTags()
    {
        $igt = $this->IGtPush;
        $ret = $igt->getPersonaTags($this->appId);

        return $ret;
    }

    // 通过标签获取用户总数
    public function getUserCountByTagsDemo($tagList)
    {
        $igt = $this->IGtPush;
        $ret = $igt->getUserCountByTags($this->appId, $tagList);

        return $ret;
    }

    // 获取推送状态
    public function getPushMessageResult($taskId)
    {
        $igt = $this->IGtPush;
        $ret = $igt->getPushResult($taskId);

        return $ret;
    }


    // 获取用户状态
    public function getUserStatus($clientId)
    {
        $igt = $this->IGtPush;
        $rep = $igt->getClientIdStatus($this->appId, $clientId);

        return $rep;
    }

    // 推送任务停止
    public function stopTask($taskId)
    {
        $igt = $this->IGtPush;
        $rep = $igt->stop($taskId);

        return $rep;
    }

    // 通过服务端设置ClientId的标签
    public function setTag($clientId, $tagList)
    {
        $igt = $this->IGtPush;
        $rep = $igt->setClientTag($this->appId, $clientId, $tagList);

        return $rep;
    }

    // 获得单个用户标签
    public function getUserTags($clientId)
    {
        $igt = $this->IGtPush;
        $rep = $igt->getUserTags($this->appId, $clientId);

        return $rep;
    }

    // 选择模板
    public function choseTemplete($templateName)
    {
        //消息模版：
        // 1.TransmissionTemplate:透传功能模板
        // 2.LinkTemplate:通知打开链接功能模板
        // 4.NotyPopLoadTemplate：通知弹框下载功能模板
        // 3.NotificationTemplate：通知透传功能模板
        switch ($templateName) {
            case 'IGtNotificationTemplate':
                $template = self::IGtNotificationTemplate();
                break;
            case 'IGtLinkTemplate':
                $template = self::IGtLinkTemplate();
                break;
            case 'IGtNotyPopLoadTemplate':
                $template = self::IGtNotyPopLoadTemplate();
                break;
            case 'IGtTransmissionTemplate':
                $template = $this->IGtTransmissionTemplate();
                break;
            default:
                $template = $this->IGtTransmissionTemplate();
                break;
        }

        return $template;
    }

    // 单推接口
    public function pushMessageToSingle($templateName)
    {
        $igt = $this->IGtPush;
        // 选择模板
        $template = $this->choseTemplete($templateName);
        $message = new \IGtSingleMessage();
        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600 * 12 * 1000);//离线时间
        $message->set_data($template);//设置推送消息类型
        //$message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
        //接收方
        $target = new \IGtTarget();
        $target->set_appId($this->appId);
        $target->set_clientId($this->sendModel->clientId);
        //$target->set_alias(Alias);

        try {
            $rep = $igt->pushMessageToSingle($message, $target);

            return $rep;

        } catch (\RequestException $e) {
            $requstId = $e->getRequestId();
            $rep = $igt->pushMessageToSingle($message, $target, $requstId);

            return $rep;
        }

    }

    /***
     * @param $templateName
     * @param string $taskGroupName
     * @param array ...$AppIdToClientIds
     * @return libarys\Array
     * @throws \Exception
     */
    public function pushMessageToList($templateName, $taskGroupName = '', ...$AppIdToClientIds)
    {
        $igt = $this->IGtPush;
        $template = $this->choseTemplete($templateName);
        //个推信息体
        $message = new \IGtListMessage();
        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600 * 12 * 1000);//离线时间
        $message->set_data($template);//设置推送消息类型

        if (empty($taskGroupName)) {
            $contentId = $igt->getContentId($message);
        } else {
            $contentId = $igt->getContentId($message, $taskGroupName); //根据TaskId设置组名，支持下划线，中文，英文，数字
        }
        $targetList = [];
        foreach ($AppIdToClientIds as $AppIdToClientId) {
            $target = new \IGtTarget();
            $target->set_appId($AppIdToClientId['appId']);
            $target->set_clientId($AppIdToClientId['clientId']);
            array_push($targetList, $target);
        }
        $rep = $igt->pushMessageToList($contentId, $targetList);

        return $rep;
    }

    // App群推接口
    function pushMessageToApp($templateName, array $appIds, $phoneType = [], $provinces = [], $tags = [], $ages = [])
    {
        $igt = $this->IGtPush;
        $template = $this->choseTemplete($templateName);
        $message = new \IGtAppMessage();
        $message->set_isOffline(true);
        $message->set_offlineExpireTime(10 * 60 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
        $message->set_data($template);
        $appIdList = $appIds;
        $cdt = new \AppConditions();
        // 根据手机类型，省份，标签，年龄进行分组
        if ( !empty($phoneType)) {
            $cdt->addCondition(AppConditions::PHONE_TYPE, $phoneType);
        }
        if ( !empty($provinces)) {
            $cdt->addCondition(AppConditions::REGION, $provinces);
        }
        if ( !empty($tags)) {
            $cdt->addCondition(AppConditions::TAG, $tags);
        }
        if ( !empty($tags)) {
            $cdt->addCondition("age", $ages);
        }

        $message->set_appIdList($appIdList);
        $message->set_conditions($cdt);

        $rep = $igt->pushMessageToApp($message);

        return $rep;
    }

    // 透传模板
    public function IGtTransmissionTemplate()
    {
        $template = new \IGtTransmissionTemplate();
        $template->set_appId($this->appId);// 应用appid
        $template->set_appkey($this->appKey);// 应用appkey
        $template->set_transmissionType(1);//透传消息类型
        $template->set_transmissionContent($this->sendModel->payload);//透传内容
        // 第三方厂商透传消息
        $notify = new IGtNotify();
        $notify->set_title($this->sendModel->title);
        $notify->set_content($this->sendModel->body);
        $notify->set_intent($this->sendModel->intent);
        $notify->set_type(\NotifyInfo_type::_intent);
        $template->set3rdNotifyInfo($notify);

        // 如下有两个推送模版，一个简单一个高级，可以互相切换使用。(Ios推送一般使用APN透传模板)
        if ($this->sendModel->APNType == "SIMPLE") {
            // APN简单推送
            $apn = new \IGtAPNPayload();
            $alertmsg = new \SimpleAlertMsg();
            $alertmsg->alertMsg = $this->sendModel->body;
            $apn->alertMsg = $alertmsg;
            $apn->badge = 2;
            $apn->sound = "";
            $apn->add_customMsg("payload", 'golinks://');
            $apn->contentAvailable = 1;
            $apn->category = "ACTIONABLE";
            $template->set_apnInfo($apn);
        } else {
            // APN高级推送
            $apn = new \IGtAPNPayload();
            $alertmsg = new \DictionaryAlertMsg();
            $alertmsg->body = $this->sendModel->body;
            $alertmsg->actionLocKey = "ActionLockey";
            $alertmsg->locKey = "LocKey";
            $alertmsg->locArgs = ["locargs"];
            $alertmsg->launchImage = "launchimage";
            $alertmsg->set_logo = $this->sendModel->logo;
            $alertmsg->set_logoURL = $this->sendModel->logoUrl;
            $alertmsg->title = $this->sendModel->title;
            $alertmsg->titleLocKey = "TitleLocKey";
            $alertmsg->titleLocArgs = ["TitleLocArg"];

            $apn->alertMsg = $alertmsg;
            $apn->badge = 7;
            $apn->sound = "";
            $apn->add_customMsg("payload", $this->sendModel->payload);
            $apn->contentAvailable = 1;
            $apn->category = "ACTIONABLE";
            $template->set_apnInfo($apn);
        }

        return $template;
    }

    // 点击通知打开应用模板
    public function IGtNotificationTemplate()
    {
        // 数据
        $template = new \IGtNotificationTemplate();
        $template->set_appId($this->appId);//应用appid
        $template->set_appkey($this->appKey);//应用appkey
        $template->set_transmissionType(1); //透传消息类型
        $template->set_transmissionContent($this->sendModel->payload); //透传内容
        $template->set_title($this->sendModel->title); //通知栏标题
        $template->set_text($this->sendModel->body); //通知栏内容
        $template->set_logo($this->sendModel->logo); //通知栏logo
        $template->set_logoURL($this->sendModel->logoUrl); //通知栏logo链接
        $template->set_isRing(true); //是否响铃
        $template->set_isVibrate(true); //是否震动
        $template->set_isClearable(true); //通知栏是否可清除

        return $template;
    }


    // 点击通知打开网页模板
    public function IGtLinkTemplate()
    {
        $template = new \IGtLinkTemplate();
        $template->set_appId($this->appId); //应用appid
        $template->set_appkey($this->appKey); //应用appkey
        $template->set_title($this->sendModel->title); //通知栏标题
        $template->set_text($this->sendModel->body); //通知栏内容
        $template->set_logo($this->sendModel->logo); //通知栏logo
        $template->set_logoURL($this->sendModel->logoUrl); //通知栏logo链接
        $template->set_isRing(true); //是否响铃
        $template->set_isVibrate(true); //是否震动
        $template->set_isClearable(true); //通知栏是否可清除
        $template->set_url($this->sendModel->webUrl); //打开连接地址

        return $template;
    }

    // 点击通知弹窗下载模板(iOS 不支持使用该模板)
    public function IGtNotyPopLoadTemplate()
    {
        $template = new \IGtNotyPopLoadTemplate();
        $template->set_appId($this->appId);
        $template->set_appkey($this->appKey);
        $type = $this->sendModel->NotyPopLoadType;
        if ($type == "notice") {
            //通知栏
            $template->set_notyTitle($this->sendModel->title); //通知栏标题
            $template->set_notyContent($this->sendModel->body); //通知栏内容
            $template->set_notyIcon($this->sendModel->logo); //通知栏logo
            $template->set_isBelled(true); //是否响铃
            $template->set_isVibrationed(true); //是否震动
            $template->set_isCleared(true); //通知栏是否可清除
        } else if ($type == "bomb") {
            //弹框
            $template->set_popTitle($this->sendModel->title); //弹框标题
            $template->set_popContent($this->sendModel->body); //弹框内容
            $template->set_popImage($this->sendModel->loadIcon); //等待框
            $template->set_popButton1("下载"); //左键
            $template->set_popButton2("取消"); //右键
        } else {
            //下载
            $template->set_loadIcon($this->sendModel->loadIcon);
            $template->set_loadTitle($this->sendModel->title);
            $template->set_loadUrl($this->sendModel->loadUrl);
            $template->set_isAutoInstall(false);
            $template->set_isActived(true);
        }

        return $template;
    }

}
