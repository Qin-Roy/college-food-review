<?php

namespace app\model;

use think\Model;
use think\facade\Db;

class chat extends Model {
    protected $name = 'userinfo';
    //获取聊天列表
    public function getChatList($targetid) 
    {  
        $code = 'success';
        $otherid1 = [];
        $otherid2 = [];
        $messageList1 = [];
        $messageList2 = [];

        $key1 = Db::table('chatmainlist')->where('userId',$targetid)->where('haschatRecord',1)->column('id');
        $key2 = Db::table('chatmainlist')->where('anotherId',$targetid)->where('haschatRecord',1)->column('id');

        if (!sizeof($key1) && !sizeof($key2)) {
            $ret['code'] = $code;
            $ret['data'] = ['messageList'=>[]];
            return json_encode($ret);
        }
        for ($i = 0;$i < sizeof($key1);$i++) {
            $otherid1[$i] = Db::table('chatmainlist')->where('id',$key1[$i])->value('anotherId');
        }
        for ($i = 0;$i < sizeof($key2);$i++) {
            $otherid2[$i] = Db::table('chatmainlist')->where('id',$key2[$i])->value('userId');
        }

        for ($i = 0;$i < sizeof($key1);$i++) {
            $nickNameinfo = Db::table('userinfo')->where('openId',$otherid1[$i])->value('nickName');
            $avatarUrlinfo = Db::table('userinfo')->where('openId',$otherid1[$i])->value('avatarUrl');
            $remarkinfo = Db::table('friend')->where('userId',$targetid)->where('friendId',$otherid1[$i])->value('remark');
            if(is_null($remarkinfo))
                $remarkinfo='';
            $lastChatTimeinfo = Db::table('chatcontentlist')->where('chatmainId',$key1[$i])
            ->where('islastmsg',1)->value('time');
            if(is_null($lastChatTimeinfo))
                $lastChatTimeinfo='';
            $lastChatcontentinfo = Db::table('chatcontentlist')->where('chatmainId',$key1[$i])
            ->where('islastmsg',1)->value('content');
            if(is_null($lastChatcontentinfo))
                $lastChatcontentinfo='';
            $messageList1[$i]=[
                'otherUserId'=>$otherid1[$i],
                'nickName'=>$nickNameinfo,
                'avatarUrl'=>$avatarUrlinfo,
                'remark'=>$remarkinfo,
                'lastChatTime'=>$lastChatTimeinfo, //->format("Y-m-d H:i:s")
                'lastChatContent'=>$lastChatcontentinfo
            ];
        }

        for ($i = 0;$i < sizeof($key2);$i++) {
            $nickNameinfo = Db::table('userinfo')->where('openId',$otherid2[$i])->value('nickName');
            $avatarUrlinfo = Db::table('userinfo')->where('openId',$otherid2[$i])->value('avatarUrl');
            $remarkinfo = Db::table('friend')->where('userId',$targetid)->where('friendId',$otherid2[$i])->value('remark');
            if(is_null($remarkinfo))
                $remarkinfo='';

            $lastChatTimeinfo = Db::table('chatcontentlist')->where('chatmainId',$key2[$i])
            ->where('islastmsg',1)->value('time');
            if(is_null($lastChatTimeinfo))
                $lastChatTimeinfo='';

            $lastChatcontentinfo = Db::table('chatcontentlist')->where('chatmainId',$key2[$i])
            ->where('islastmsg',1)->value('content');
            if(is_null($lastChatcontentinfo))
                $lastChatcontentinfo='';

            $messageList2[$i]=[
                'otherUserId'=>$otherid2[$i],
                'nickName'=>$nickNameinfo,
                'avatarUrl'=>$avatarUrlinfo,
                'remark'=>$remarkinfo,
                'lastChatTime'=>$lastChatTimeinfo, //->format("Y-m-d H:i:s")
                'lastChatContent'=>$lastChatcontentinfo
            ];
        }

        $messageList = array_merge($messageList1,$messageList2);
        $dat['messageList'] = $messageList;
        $ret['data'] = $dat;
        $ret['code'] = $code;
        return json_encode($ret);
    }

    //获取与某人的聊天记录
    public function getChatRecord($id,$otherUserId)
    {
        $code = 'success';
        $messageList = [];

        //拿双方id去找chatmainid
        $msgid = Db::table('chatmainlist')->where('userId',$id)->where('anotherId',$otherUserId)->value('id');
        if ($msgid == null) {
            $msgid = Db::table('chatmainlist')->where('userId',$otherUserId)->where('anotherId',$id)->value('id');
            if ($msgid == null) {//没找到mainid，返回错误状态
                $ret['code'] = 'fail:nochatmainrecord';
                $ret['data'] = ['msgList'=>[]];
                return json_encode($ret);
            }
        }
        //找到chat主表id后开始找具体聊天记录
        $key = Db::table('chatcontentlist')->where('chatmainId',$msgid)->column('id');
        for ($i = 0;$i < sizeof($key);$i++) {
            $contentinfo = Db::table('chatcontentlist')->where('id',$key[$i])->value('content');
            $timeinfo = Db::table('chatcontentlist')->where('id',$key[$i])->value('time');
            $messageList[$i] = [
                'id'=>Db::table('chatcontentlist')->where('id',$key[$i])->value('sendId'),
                'content'=>$contentinfo,
                'time'=>$timeinfo, //->format("Y-m-d H:i:s")
            ];
        }
        $count=count($messageList);

        for($i=0;$i<$count;$i++) {
            for($j=$i+1;$j<$count;$j++) {
                if(floatval($messageList[$i]['time'])>floatval($messageList[$j]['time'])) {
                    $temp=$messageList[$i];
                    $messageList[$i]=$messageList[$j];
                    $messageList[$j]=$temp;
                }
            }
        }
        $dat['msgList'] = $messageList;
        $ret['data'] = $dat;
        $ret['code'] = $code;
        return json_encode($ret);
    }

    //获取对话双方简介，
    //如果双方创建了聊天界面，则正常返回双方信息；若双方没有聊天界面连接，则在聊天主表创建记录，再正常返回双方信息
    public function getChatInfo($targetid,$otherUserId)
    {
        $code = 'success';
        $targetidentity = Db::table('userinfo')->where('openId',$targetid)->value('identity');
        $otheridentity = Db::table('userinfo')->where('openId',$otherUserId)->value('identity');
        $isbothusers = false;
        //双方都是普通用户才会返回备注
        if ($targetidentity === 1 && $otheridentity === 1) {
            $isbothusers = true;
        }
        //去主表查询双方是否为第一次聊天，如果是则在主表创建新的记录并获取该记录的id，否则查找该记录并返回该记录id
        $mainid = Db::table('chatmainlist')->where('userId',$targetid)->where('anotherId',$otherUserId)->value('id');
        if ($mainid == null) {
            $mainid = Db::table('chatmainlist')->where('userId',$otherUserId)->where('anotherId',$targetid)->value('id');
            if ($mainid == null) {//两次查询都未找到，则创建新纪录
                $insertdata = [
                    'userId'=>$targetid,
                    'haschatRecord'=>0,
                    'anotherId'=>$otherUserId
                ];
                Db::table('chatmainlist')->insert($insertdata);
                $mainid = Db::table('chatmainlist')->getLastInsId();
            }
        }

        $myavatarUrlinfo = Db::table('userinfo')->where('openid',$targetid)->value('avatarUrl');
        $mynicknameinfo = Db::table('userinfo')->where('openid',$targetid)->value('nickName');
        $otheravatarUrlinfo = Db::table('userinfo')->where('openid',$otherUserId)->value('avatarUrl');
        $othernicknameinfo = Db::table('userinfo')->where('openid',$otherUserId)->value('nickName');
        $otherremarkinfo = $isbothusers === true ? (Db::table('friend')->where('userId',$targetid)->where('friendId',$otherUserId)->value('remark')) : '';
        if(is_null($otherremarkinfo))
            $otherremarkinfo='';
        $dat['otherAvatarUrl'] = $otheravatarUrlinfo;
        $dat['otherNickName'] = $othernicknameinfo;
        $dat['myAvatarUrl'] = $myavatarUrlinfo;
        $dat['myNickName'] = $mynicknameinfo;
        $dat['otherRemark'] = $otherremarkinfo;
        $ret['data'] = $dat;
        $ret['code'] = $code;
        return json_encode($ret);
    }
    
    //发送信息
    public function sendMessage($info)
    {
        $code = 'success';
        //先寻找chat主表的mainid
        $msgid = Db::table('chatmainlist')->where('userId',$info['id'])->where('anotherId',$info['otherUserId'])->value('id');
        if ($msgid == null) {
            $msgid = Db::table('chatmainlist')->where('userId',$info['otherUserId'])->where('anotherId',$info['id'])->value('id');
            if ($msgid == null) {
                $ret=['code'=>'fail:nochatmainrecord'];
                return json_encode($ret);
            }
        }

        //判断双方之前是否没有任何消息记录，如果是则将main表中hasChatRecord置为1
        $record = Db::table('chatmainlist')->where('id',$msgid)->value('haschatRecord');
        if ($record == 0) {
            Db::table('chatmainlist')->where('id',$msgid)->update(['haschatRecord'=>1]);
        }

        //将添加记录前的islast=1的消息置0
        $key = Db::table('chatcontentlist')->where('chatmainId',$msgid)->where('islastmsg',1)->value('id');
        if ($key !== null) { //如果有记录则更新islast，否则不操作
            Db::table('chatcontentlist')->where('id',$key)->update(['islastmsg'=>0]);
        }

        //向chatcontentlist添加记录，并标记为islast=1，**update 张智淋 2022.5.30 9:32
        $insertdata = [
            'time'=>$info['time'],
            'content'=>$info['content'],
            'chatmainId'=>$msgid,
            'islastmsg'=>intval(1),
            'sendId'=>$info['id'],
            'receiveId'=>$info['otherUserId']
        ];
        Db::table('chatcontentlist')->insert($insertdata);

        $ret['code'] = $code;
        return json_encode($ret);
    }
}
