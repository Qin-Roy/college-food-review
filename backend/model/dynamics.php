<?php

namespace app\model;

use think\Model;
use think\facade\Db;



class dynamics extends Model {
    protected $name = 'userinfo';

    public function getfollow_selfcomment($targetid)
    {

        $code = 'success';
        $commentList = [];
        
        $k = 0;
        //k为目标commentList总索引
        //先获取自己的所有评论
        $mycommentId_list = Db::table('comment')->where('userId',$targetid)->column('id');
        for ($i = 0;$i < sizeof($mycommentId_list);$i++) {
            $dishid = Db::table('comment')->where('id',$mycommentId_list[$i])->value('dishId');
            // $storeid = Db::table('dish')->where('id',$dishid)->value('storeId');
            $isLiked = Db::table('likes')->where('userId',$targetid)->where('commentId',$mycommentId_list[$i])->find();
            $responseNum = Db::table('response')->where('commentId',$mycommentId_list[$i])->count();

            if ($responseNum === 0) { //该评论没有回复
                $fromid = '';
                $fromnickname = '';
                $fromremark = '';
                $toid = '';
                $tonickname = '';
                $toremark = '';
                $detail = '';
            }
            else {
                // $mintime = Db::table('response')->where('commentId',$mycommentId_list[$i])->min('time');
                // $fromid = Db::table('response')->where('time',$mintime)->value('userId');
                // $fromnickname = Db::table('userinfo')->where('openId',$fromid)->value('nickName');
                // $toid = Db::table('response')->where('time',$mintime)->value('towhomId');
                // $tonickname = Db::table('userinfo')->where('openId',$toid)->value('nickName');
                // $detail =  Db::table('response')->where('time',$mintime)->value('content');

                $Response=Db::table('response')->where('commentId',$mycommentId_list[$i])->find();
                $fromid=$Response['userId'];
                $toid=$Response['towhomId'];
                $detail=$Response['content'];
                $fromnickname=Db::table('userinfo')->where('openId',$fromid)->value('nickName');
                $tonickname=Db::table('userinfo')->where('openId',$toid)->value('nickName');

                $isfriend1 = Db::table('friend')->where('userId',$fromid)->where('friendId',$toid)->find();
                $isfriend2 = Db::table('friend')->where('userId',$toid)->where('friendId',$fromid)->find();
                //评论双方不是好友关系
                if ($isfriend1 === null || $isfriend2 === null) {
                    $fromremark = '';
                    $toremark = '';
                }
                else {
                    $fromremark = Db::table('friend')->where('userId',$toid)->where('friendId',$fromid)->value('remark');
                    $fromremark = ($fromremark === null ? '' : $fromremark);
                    $toremark = Db::table('friend')->where('userId',$fromid)->where('friendId',$toid)->value('remark');
                    $toremark = ($toremark === null ? '' : $toremark);
                }
            }
            $isCanteen=Db::table('comment')->where('id',$mycommentId_list[$i])->value('isCanteen');
            if($isCanteen) {
                $storeid = Db::table('canteendish')->where('id',$dishid)->value('canteenId');
                $dishName=Db::table('canteendish')->where('id',$dishid)->value('dishName');
                $merchantName=Db::table('canteen')->where('id',$storeid)->value('name');
            }
            else {
                $storeid = Db::table('dish')->where('id',$dishid)->value('storeId');
                $dishName= Db::table('dish')->where('id',$dishid)->value('dishName');
                $merchantName=Db::table('store')->where('storeId',$storeid)->value('storeName');
            }
            $commentList[$k++] = [
                'commentId'=>strval($mycommentId_list[$i]),//进循环则一定非空
                'userId'=>$targetid,//进此函数则一定非空
                'nickName'=>Db::table('userinfo')->where('openId',$targetid)->value('nickName'),
                'avatarUrl'=>Db::table('userinfo')->where('openId',$targetid)->value('avatarUrl'),
                'dishId'=>strval($dishid),
                'remark'=>Db::table('userinfo')->where('openId',$targetid)->value('nickName'),
                'dishName'=>$dishName,
                'merchantNickName'=>$merchantName,
                'isCanteen'=>strval($isCanteen),
                'time'=>Db::table('comment')->where('id',$mycommentId_list[$i])->value('time'),
                'score'=>strval(Db::table('comment')->where('id',$mycommentId_list[$i])->value('score')),
                'content'=>Db::table('comment')->where('id',$mycommentId_list[$i])->value('content'),
                'likedNum'=>Db::table('comment')->where('id',$mycommentId_list[$i])->value('likedNum'),
                'isLiked'=>($isLiked === null ? '0' : '1'),
                'responseNum'=>$responseNum,
                'firstResponse'=>[
                    'fromId'=>$fromid,
                    'fromNickName'=>$fromnickname,
                    'fromRemark'=>$fromremark,
                    'toId'=>$toid,
                    'toNickName'=>$tonickname,
                    'toRemark'=>$toremark,
                    'detail'=>$detail
                ]
            ];
        }

        //i为关注的人id列表索引，j为关注的人i的评论列表索引
        //再获取关注的人的所有评论
        $followedid_list = Db::table('follow')->where('followerId',$targetid)->column('followedId');
        for ($i = 0;$i < sizeof($followedid_list);$i++) {

            //该关注用户的备注
            $friendRemark=Db::table('friend')->where(['userId'=>$targetid,'friendId'=>$followedid_list[$i]])->value('remark');
            if(is_null($friendRemark))
                $friendRemark='';

            //该关注用户的所有评论的主键id
            $targetcommentId_list = Db::table('comment')->where('userId',$followedid_list[$i])->column('id');
            for ($j = 0;$j < sizeof($targetcommentId_list);$j++) {
                $dishid = Db::table('comment')->where('id',$targetcommentId_list[$j])->value('dishId');
                
                $isLiked = Db::table('likes')->where('userId',$targetid)->where('commentId',$targetcommentId_list[$j])->find();
                $responseNum = Db::table('response')->where('commentId',$targetcommentId_list[$j])->count();
                if ($responseNum === 0) { //该评论没有回复
                    $fromid = '';
                    $fromnickname = '';
                    $fromremark = '';
                    $toid = '';
                    $tonickname = '';
                    $toremark = '';
                    $detail = '';
                }
                else {
                    $firstRes = Db::table('response')->where('commentId',$targetcommentId_list[$j])->find();
                    $fromid = $firstRes['userId'];
                    $fromnickname = Db::table('userinfo')->where('openId',$fromid)->value('nickName');
                    $toid = $firstRes['towhomId'];
                    $tonickname = Db::table('userinfo')->where('openId',$toid)->value('nickName');
                    $detail = $firstRes['content'];
                    $isfriend1 = Db::table('friend')->where('userId',$fromid)->where('friendId',$toid)->find();
                    $isfriend2 = Db::table('friend')->where('userId',$toid)->where('friendId',$fromid)->find();
                    //评论双方不是好友关系
                    if ($isfriend1 === null || $isfriend2 === null) {
                        $fromremark = '';
                        $toremark = '';
                    }
                    else {
                        $fromremark = Db::table('friend')->where('userId',$targetid)->where('friendId',$fromid)->value('remark');
                        $fromremark = ($fromremark === null ? '' : $fromremark);
                        $toremark = Db::table('friend')->where('userId',$targetid)->where('friendId',$toid)->value('remark');
                        $toremark = ($toremark === null ? '' : $toremark);
                    }
                }
                $isCanteen=Db::table('comment')->where('id',$targetcommentId_list[$j])->value('isCanteen');
                if($isCanteen) {
                    $storeid = Db::table('canteendish')->where('id',$dishid)->value('canteenId');
                    $dishName=Db::table('canteendish')->where('id',$dishid)->value('dishName');
                    $merchantName=Db::table('canteen')->where('id',$storeid)->value('name');
                }
                else {
                    $storeid = Db::table('dish')->where('id',$dishid)->value('storeId');
                    $dishName= Db::table('dish')->where('id',$dishid)->value('dishName');
                    $merchantName=Db::table('store')->where('storeId',$storeid)->value('storeName');
                }

                $commentList[$k++] = [
                    'commentId'=>strval($targetcommentId_list[$j]),
                    'userId'=>$followedid_list[$i],
                    'nickName'=>Db::table('userinfo')->where('openId',$followedid_list[$i])->value('nickName'),
                    'avatarUrl'=>Db::table('userinfo')->where('openId',$followedid_list[$i])->value('avatarUrl'),
                    'dishId'=>strval($dishid),
                    'remark'=>$friendRemark,
                    'dishName'=>$dishName,
                    'isCanteen'=>strval($isCanteen),
                    'merchantNickName'=>$merchantName,
                    'time'=>Db::table('comment')->where('id',$targetcommentId_list[$j])->value('time'),
                    'score'=>strval(Db::table('comment')->where('id',$targetcommentId_list[$j])->value('score')),
                    'content'=>Db::table('comment')->where('id',$targetcommentId_list[$j])->value('content'),
                    'likedNum'=>Db::table('comment')->where('id',$targetcommentId_list[$j])->value('likedNum'),
                    'isLiked'=>($isLiked === null ? '0' : '1'),
                    'responseNum'=>$responseNum,
                    'firstResponse'=>[
                        'fromId'=>$fromid,
                        'fromNickName'=>$fromnickname,
                        'fromRemark'=>$fromremark,
                        'toId'=>$toid,
                        'toNickName'=>$tonickname,
                        'toRemark'=>$toremark,
                        'detail'=>$detail
                    ]
                ];
            }
        }
        //未找到任何自己的和关注用户的评论
        if ($k === 0) {
            $ret=[
                'code'=>'success',
                'data'=>['commentList'=>[]]
            ];
        }
        else {  
            $count=count($commentList);
            for($i=0;$i<$count;$i++) {
                for($j=$i+1;$j<$count;$j++) {
                    if(intval($commentList[$i]['time'])<intval($commentList[$j]['time'])) {
                        $temp=$commentList[$i];
                        $commentList[$i]=$commentList[$j];
                        $commentList[$j]=$temp;
                    }
                }
            }

            $dat['commentList'] = $commentList;
            $ret['data'] = $dat;
            $ret['code'] = $code;
        }
        return json_encode($ret);
    }
}