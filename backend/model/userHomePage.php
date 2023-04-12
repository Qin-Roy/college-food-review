<?php

namespace app\model;

use think\Model;
use think\facade\Db;

class userHomePage extends Model {

    protected $name = 'userinfo';

    public function getFavouredStoreList($id) {
        if(is_null(Db::table('favouredstore')->where('userId',$id)->find())) {
            $data['favouredStoreList']=[];
            $ret['code']='success';
            $ret['data']=$data;
            return json_encode($ret);
        }
        else {
            $store=Db::table('favouredstore')->where('userId',$id)->select()->toArray();
            $arrLen=count($store);
            for($x=0;$x<$arrLen;$x++) {
                $tmp=Db::table('store')->where('storeId',$store[$x]['storeId'])->find(); 
                $favouredStoreList[$x]=[
                    'id'=>$tmp['storeId'],
                    'nickName'=>$tmp['storeName'],
                    'avatarUrl'=>$tmp['avatarUrl'],
                    'intro'=>$tmp['intro'],
                    'categoryList'=>Db::table('category')->where('storeId',$store[$x]['storeId'])->column('name')
                ];
            }
            $data['favouredStoreList']=$favouredStoreList;
            $ret['code']='success';
            $ret['data']=$data;
            return json_encode($ret);
        }
    }

    public function getCommentList($openid,$actId) {
        if(is_null(Db::table('comment')->where('userId',$openid)->find())) {
            $data['commentList']=[];
            $ret=[
                'code'=>'success',
                'data'=>$data
            ];
            return json_encode($ret);
        }
        else {
            $comments=Db::table('comment')->where('userId',$openid)->select()->toArray();
            $comments=array_reverse($comments);
            $comLen=count($comments);
            for($x=0;$x<$comLen;$x++) {
                if(is_null(Db::table('response')->where('commentId',$comments[$x]['id'])->find())) {
                    $fisrtResponse=[
                        'fromId'=>'',
                        'fromNickName'=>'',
                        'fromRemark'=>'',
                        'toId'=>'',
                        'toNickName'=>'',
                        'toRemark'=>'',
                        'detail'=>''
                    ];
                    $responseNum=0;
                }
                else {
                    $response=Db::table('response')->where('commentId',$comments[$x]['id'])->find();
                    $fromId=$response['userId'];
                    $toId=$response['towhomId'];
                    $detail=$response['content'];
                    $fromNickName=Db::table('userinfo')->where('openId',$fromId)->value('nickName');
                    $toNickName=Db::table('userinfo')->where('openId',$toId)->value('nickName');
                    $fromRemark=Db::table('friend')->where(['userId'=>$actId,'friendId'=>$fromId])->value('remark');
                    if(is_null($fromRemark))
                        $fromRemark='';
                    $toRemark=Db::table('friend')->where(['userId'=>$actId,'friendId'=>$toId])->value('remark');
                    if(is_null($toRemark))
                        $toRemark='';

                $fisrtResponse=[
                    'fromId'=>$fromId,
                    'toId'=>$toId,
                    'fromNickName'=>$fromNickName,
                    'toNickName'=>$toNickName,
                    'fromRemark'=>$fromRemark,
                    'toRemark'=>$toRemark,
                    'detail'=>$detail
                ];
                    //$fisrtResponse=Db::table('response')->where('commentId',$comments[$x]['id'])->find();
                    $responseNum=count(Db::table('response')->where('commentId',$comments[$x]['id'])->select()->toArray());
                }
                if($comments[$x]['isCanteen']) {
                    $dish=Db::table('canteendish')->where('id',$comments[$x]['dishId'])->find();
                    $dishName=$dish['dishName'];
                    $storeName=Db::table('canteen')->where('id',$dish['canteenId'])->value('name');
                }
                else {
                    $dish=Db::table('dish')->where('id',$comments[$x]['dishId'])->find();
                    $dishName=$dish['dishName'];
                    $storeName=Db::table('store')->where('storeId',$dish['storeId'])->value('storeName');
                }
                
                $isLiked=!is_null(Db::table('likes')->where(['userId'=>$openid,'commentId'=>$comments[$x]['id']])->find());
                $user=Db::table('userinfo')->where('openId',$comments[$x]['userId'])->find();
                $remark=Db::table('friend')->where(['userId'=>$openid,'friendId'=>$user['openId']])->value('remark');
                if(is_null($remark))
                    $remark='';

                $commentList[$x]=[
                    // 'dishId'=>strval($comments[$x]['dishId']),
                    // 'dishName'=>$dishName,
                    // 'merchantNickName'=>$storeName,
                    // 'commentId'=>strval($comments[$x]['id']),
                    // 'time'=>$comments[$x]['time'],
                    // 'content'=>$comments[$x]['content'],
                    // 'score'=>strval($comments[$x]['score']),
                    // 'likedNum'=>$comments[$x]['likedNum'],
                    // 'isLiked'=>strval($isLiked),
                    // 'responseNum'=>$responseNum,
                    // 'firstResponse'=>$fisrtResponse
                    'isCanteen'=>strval($comments[$x]['isCanteen']),
                    'userId'=>$user['openId'],
                    'nickName'=>$user['nickName'],
                    'remark'=>$remark,
                    'avatarUrl'=>$user['avatarUrl'],
                    'dishId'=>strval($comments[$x]['dishId']),
                    'dishName'=>$dishName,
                    'merchantNickName'=>$storeName,
                    'commentId'=>strval($comments[$x]['id']),
                    'time'=>$comments[$x]['time'],
                    'content'=>$comments[$x]['content'],
                    'score'=>strval($comments[$x]['score']),
                    'likedNum'=>$comments[$x]['likedNum'],
                    'isLiked'=>strval($isLiked),
                    'responseNum'=>$responseNum,
                    'firstResponse'=>$fisrtResponse
                ];
            }
            $data['commentList']=$commentList;
            $ret['code']='success';
            $ret['data']=$data;
            return json_encode($ret);
        }
    }

    public function deleteComment($comment_id,$openid) {
        $comment=Db::table('comment')->where('id',$comment_id)->find();
        if(is_null($comment)) 
            $ret['code']='fail';
        else {
            $user_id=$comment['userId'];
            $isCanteen=Db::table('comment')->where('id',$comment_id)->value('isCanteen');
            $dishId=Db::table('comment')->where('id',$comment_id)->value('dishId');

            //删除评论
            if($user_id==$openid) {
                Db::table('comment')->where('id',$comment_id)->delete();
                Db::table('response')->where('commentId',$comment_id)->delete();
                Db::table('admin_comment_report')->where('commentId',$comment_id)->delete();  //同时删除所有对该评论的举报
                //重新计算评分
                $scores=Db::table('comment')->where(['dishId'=>$dishId,'isCanteen'=>$isCanteen])->column('score');
                $n=count($scores);
                $totalScore=0;
                if($n==0)
                    $totalScore=-1;
                else {
                    for($i=0;$i<$n;$i++) 
                        $totalScore=$totalScore+$scores[$i];
                    $totalScore=number_format($totalScore/$n,1);
                }
                if($isCanteen)
                    Db::table('canteendish')->where('id',$dishId)->update(['score'=>$totalScore]);
                else
                    Db::table('dish')->where('id',$dishId)->update(['score'=>$totalScore]);
                $ret['code']='success';
            }
            else 
                $ret['code']='fail';
        }
        return json_encode($ret);
    }

    public function authorize($authority,$openid) {
        if($authority=='1') {
            if(Db::table('userinfo')->where('openId',$openid)->update(['authority'=>1])) {
                $ret['code']='success';
                return json_encode($ret);
            }
            else {
                $ret['code']='fail';
                return json_encode($ret);
            }
        }
        else if($authority=='0'){
            if(Db::table('userinfo')->where('openId',$openid)->update(['authority'=>0])) {
                $ret['code']='success';
                return json_encode($ret);
            }
            else {
                $ret['code']='fail';
                return json_encode($ret);
            } 
        }
        else {
            $ret['code']='fail';
            return json_encode($ret);
        }
    }

    public function getAuthority($openid) {
        $authority=Db::table('userinfo')->where('openId',$openid)->value('authority');
        if(!is_null($authority)) {
            $ret['code']='success';
            if($authority==1)
                $data='1';
            else
                $data='0';
            $ret['data']=['authority'=>$data];
            return json_encode($ret);
        }
        else {
            $ret['code']='fail';
            $ret['data']=null;
            return json_encode($ret);
        }
    }

    public function editProfile($openid,$avatarUrl,$nickName) {
        if(Db::table('userinfo')->where('openId',$openid)->update(['avatarUrl'=>$avatarUrl,'nickName'=>$nickName]))
            $ret['code']='success';
        else
            $ret['code']='fail';
        return json_encode($ret);
    }

    

    public function getFollowList($reqid)
    {
        $code = 'success';
        $followlist = [];
        $targetid = Db::table('follow')->where('followerId',$reqid)->column('followedId');
        if (!sizeof($targetid)) {
            $ret['code'] = 'success';
            $ret['data']=['followingList'=>[]];
            return json_encode($ret);
        }
        for ($i = 0;$i < sizeof($targetid);$i++) {
            $nicknameinfo = Db::table('userinfo')->where('openId',$targetid[$i])->value('nickName');
            //column('openid','nickname','avatarUrl');
            $avatarUrlinfo = Db::table('userinfo')->where('openId',$targetid[$i])->value('avatarUrl');
            // $info = array($targetid[$i],$nicknameinfo,$avatarUrlinfo);
            // array_push($followlist,$info);

            //查找备注remark
            $remark=Db::table('friend')->where(['userId'=>$reqid,'friendId'=>$targetid[$i]])->value('remark');
            if(is_null($remark))
                $remark='';

            $followingList[$i]=[
                'id'=>$targetid[$i],
                'nickName'=>$nicknameinfo,
                'avatarUrl'=>$avatarUrlinfo,
                'remark'=>$remark
            ];
        }
        $dat['followingList']=$followingList;
        $ret['data']=$dat;
        $ret['code']=$code;
        return json_encode($ret);
    }

    public function getFollowerList($id) {
        $code = 'success';
        $followlist = [];
        $targetid = Db::table('follow')->where('followedId',$id)->column('followerId');
        if (!sizeof($targetid)) {
            $ret['code'] = 'success';
            $ret['data']=['followerList'=>[]];
            return json_encode($ret);
        }
        for ($i = 0;$i < sizeof($targetid);$i++) {
            $nicknameinfo = Db::table('userinfo')->where('openId',$targetid[$i])->value('nickName');
            //column('openid','nickname','avatarUrl');
            $avatarUrlinfo = Db::table('userinfo')->where('openId',$targetid[$i])->value('avatarUrl');
            // $info = array($targetid[$i],$nicknameinfo,$avatarUrlinfo);
            // array_push($followlist,$info);

            $remark=Db::table('friend')->where(['userId'=>$id,'friendId'=>$targetid[$i]])->value('remark');
            if(is_null($remark))
                $remark='';
            $followerList[$i]=[
                'id'=>$targetid[$i],
                'nickName'=>$nicknameinfo,
                'avatarUrl'=>$avatarUrlinfo,
                'remark'=>$remark
            ];
        }
        $dat['followerList']=$followerList;
        $ret['data']=$dat;
        $ret['code']=$code;
        return json_encode($ret);
    }

    public function getFollowerNum($userId) {
        $followerNum=Db::table('userinfo')->where('openId',$userId)->value('followerNum');
        $ret['code']='succees';
        $ret['data']=['followerNum'=>$followerNum];
        return json_encode($ret);
    }
}