<?php

namespace app\model;

use think\Model;
use think\facade\Db;

class friend extends Model {

    protected $name = 'userinfo';
    
    //获取添加好友请求
    public function getFriendRequest($id) 
    {
        $friend_id=Db::table('friend_request')->where('friendId',$id)->column('userId');
        $reasons=Db::table('friend_request')->where('friendId',$id)->column('reason');
        if(!sizeof($friend_id)) {
            $ret['code']='success';
            $ret['data']=['requestList'=>[]];
            return json_encode($ret);
        }
        for ($i = 0;$i < sizeof($friend_id);$i++) {
            $nickName = Db::table('userinfo')->where('openId',$friend_id[$i])->value('nickName');
            $avatarUrl = Db::table('userinfo')->where('openId',$friend_id[$i])->value('avatarUrl');
            $remark = Db::table('friend_request')->where('friendId',$id)->where('userId',$friend_id[$i])->value('tempremark');
            $requestList[$i]=[
                'id'=>$friend_id[$i],
                'nickName'=>$nickName,
                'avatarUrl'=>$avatarUrl,
                'remark'=>$remark,
                'detail'=>$reasons[$i]
            ];
        }
        $ret['code']='success';
        $ret['data']=['requestList'=>$requestList];
        return json_encode($ret);
    }

    //确定好友请求
    public function confirmFriendRequest($id,$friend_id,$add) 
    {   
        $request=['userId'=>$friend_id,'friendId'=>$id];
        if($add==1) {
            $remark=Db::table('friend_request')->where($request)->value('tempremark');
            $data=['userId'=>$friend_id,'friendId'=>$id,'remark'=>$remark];
            Db::table('friend')->insert($data);
            $data=['userId'=>$id,'friendId'=>$friend_id,'remark'=>''];
            Db::table('friend')->insert($data);
            $data = ['userId'=>$id,'anotherId'=>$friend_id,'haschatRecord'=>intval(0)];
            Db::table('chatmainlist')->insert($data);
        }
        //request表删除相应记录
        $request=['userId'=>$friend_id,'friendId'=>$id];
        $flag = Db::table('friend_request')->where($request)->delete();

        Db::table('friendrequestfeedback')->insert([
            'result'=>($add==1?true:false),
            'receiveId'=>$friend_id,
            'sendId'=>$id,
            'time'=>strval(time()*1000)
        ]);

        if ($flag === false) {
            $ret['code']='fail';
            return json_encode($ret);
        }
        $ret['code']='success';
        return json_encode($ret);
    }

    //获取好友列表
    public function getFriendList($reqid)
    {
        $code = 'success';
        $friendlist = [];
        $targetid = Db::table('friend')->where('userId',$reqid)->column('friendId');

        if (!sizeof($targetid)) {
            $ret['code'] = 'success';
            $ret['data'] = ['friendList'=>[]];
            return json_encode($ret);
        }
        for ($i = 0;$i < sizeof($targetid);$i++) {
            $nicknameinfo = Db::table('userinfo')->where('openId',$targetid[$i])->value('nickName');
            //column('openid','nickname','avatarUrl');
            $avatarUrlinfo = Db::table('userinfo')->where('openId',$targetid[$i])->value('avatarUrl');
            //$info = array($targetid[$i],$nicknameinfo,$avatarUrlinfo);
            $remarkinfo = Db::table('friend')->where('userId',$reqid)->where('friendId',$targetid[$i])->value('remark');
            if(is_null($remarkinfo))
                $remarkinfo='';
            $friendList[$i]=[
                'id'=>$targetid[$i],
                'nickName'=>$nicknameinfo,
                'remark'=>$remarkinfo,
                'avatarUrl'=>$avatarUrlinfo
            ];
        }
        $dat['friendList']=$friendList;
        $ret['data']=$dat;
        $ret['code']=$code;
        return json_encode($ret);
    }

    //添加好友请求
    public function addFriendRequest($id,$friend_id,$detail,$remark) {
        $data=['userId'=>$id,'friendId'=>$friend_id,'reason'=>$detail,'tempremark'=>$remark];
        Db::table('friend_request')->insert($data);
        $ret['code']='success';
        return json_encode($ret);
    }

    //删除好友
    public function deleteFriend($id,$friend_id) {
        $request=['userId'=>$id,'friendId'=>$friend_id];
        $flag1 = Db::table('friend')->where($request)->delete();
        $request=['userId'=>$friend_id,'friendId'=>$id];
        $flag2 = Db::table('friend')->where($request)->delete();

        //同时删除双方的chatmain记录
        $deletereq = ['userId'=>$id,'anotherId'=>$friend_id];
        $flag3 = Db::table('chatmainlist')->where($deletereq)->delete();
            $deletereq = ['userId'=>$friend_id,'anotherId'=>$id];
            $flag3 = Db::table('chatmainlist')->where($deletereq)->delete();
            if ($flag3 === false) {//删除chatmain record失败
                $ret['code']='fail:nochatmainrecord';
                return json_encode($ret);
            }

        //删除失败
        if ($flag1 === false || $flag2 === false) {
            $ret['code']='fail';
            return json_encode($ret);
        }
        $ret['code']='success';
        return json_encode($ret);
    }

    //修改好友备注
    public function changeFriendRemark($id,$friend_id,$remark) {
        $flag = Db::table('friend')->where('userId',$id)->where('friendId',$friend_id)->update(['remark'=>$remark]);
        if ($flag === false) {
            $ret['code']='fail';
            return json_encode($ret);
        }
        $ret['code']='success';
        return json_encode($ret);
    }
}