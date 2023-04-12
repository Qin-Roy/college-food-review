<?php

namespace app\model;

use think\Model;
use think\facade\Db;

class common extends Model {
    protected $name = 'userinfo';

    public function postComment($dishId,$isCanteen,$id,$time,$content,$score) {
        if(Db::table('comment')->insert(
            ['content'=>$content,
             'time'=>$time,
             'userId'=>$id,
             'likedNum'=>0,
             'dishId'=>$dishId,
             'score'=>$score,
             'isCanteen'=>$isCanteen
            ])) {
            
            //重新计算评分
            $scores=Db::table('comment')->where(['dishId'=>$dishId,'isCanteen'=>$isCanteen])->column('score');
            $n=count($scores);
            $totalScore=(float)0;
            for($i=0;$i<$n;$i++) 
                $totalScore+=$scores[$i];
            
            if($isCanteen)
                Db::table('canteendish')->where('id',$dishId)->update(['score'=>number_format($totalScore/$n,1)]);
            else
                Db::table('dish')->where('id',$dishId)->update(['score'=>number_format($totalScore/$n,1)]);
            
            $ret['code']='success';
            return json_encode($ret);
        }
        else {
            $ret['code']='fail';
            return json_encode($ret);
        }        
    }

    public function replyToComment($commentId,$id,$toId,$detail) {
        if(is_null(Db::table('comment')->where('id',$commentId)->find())) {
            $ret['code']='fail';
            return json_encode($ret);
        }
        if(Db::table('response')->insert([
            'userId'=>$id,
            'towhomId'=>$toId,
            'commentId'=>$commentId,
            'content'=>$detail
        ])) {
            $ret['code']='success';
            return json_encode($ret);
        }
        else {
            $ret['code']='fail';
            return json_encode($ret);
        }
    }

    public function likeComment($commentId,$userId) {
        if(is_null(Db::table('likes')->where(['commentId'=>$commentId,'userId'=>$userId])->find()))  { 
            if(Db::table('comment')->where('id', $commentId)->inc('likedNum')->update()) 
                $ret['code']='success';
            else 
                $ret['code']='fail';
            Db::table('likes')->insert([
                'commentId'=>$commentId,
                'userId'=>$userId
            ]);
        }
        else {
            if(Db::table('comment')->where('id', $commentId)->dec('likedNum')->update()) 
                $ret['code']='success';
            else 
                $ret['code']='fail';
            Db::table('likes')->where(['commentId'=>$commentId,'userId'=>$userId])->delete();
        }
        return json_encode($ret);
    }

    public function favourStore($storeId,$userId) {
        if(Db::table('userinfo')->where('openId',$userId)->inc('favouredStoreNum')->update())
            $ret['code']='success';
        else
            $ret['code']='fail';
        Db::table('favouredstore')->insert([
            'userId'=>$userId,
            'storeId'=>$storeId
        ]);

        return json_encode($ret);
    }

    public function cancelFavourStore($storeId,$userId) {
        if(Db::table('favouredstore')->where(['userId'=>$userId,'storeId'=>$storeId])->delete()) {
            if(Db::table('userinfo')->where('openId',$userId)->dec('favouredStoreNum')->update())
                $ret['code']='success';
            else
                $ret['code']='fail';
            return json_encode($ret);
        }
        else {
            $ret['code']='fail';
            return json_encode($ret);
        }
    }
    
    public function followUser($userId,$otherUserId) {
        if(!is_null(Db::table('userinfo')->where('openId',$userId)->find())&&!is_null(Db::table('userinfo')->where('openId',$otherUserId)->find())) {
            Db::table('userinfo')->where('openId',$userId)->inc('followingNum')->update();
            Db::table('userinfo')->where('openId',$otherUserId)->inc('followerNum')->update();
            Db::table('follow')->insert([
                'followerId'=>$userId,
                'followedId'=>$otherUserId
            ]);
            $ret['code']='success';
        }
        else 
            $ret['code']='fail';
        return json_encode($ret);
    }

    public function cancelFollowUser($userId,$otherUserId) {
        if(!is_null(Db::table('userinfo')->where('openId',$userId)->find())&&!is_null(Db::table('userinfo')->where('openId',$otherUserId)->find())) {
            if(Db::table('follow')->where(['followerId'=>$userId,'followedId'=>$otherUserId])->delete()) {
                Db::table('userinfo')->where('openId',$userId)->dec('followingNum')->update();
                Db::table('userinfo')->where('openId',$otherUserId)->dec('followerNum')->update();
                $ret['code']='success';
            }
            else 
                $ret['code']='fail';
        }
        else
            $ret['code']='fail';
        return json_encode($ret);
    }

    public function reportComment($commentId,$time,$reason,$detail) {
        if(is_null(Db::table('comment')->where('id',$commentId)->find())) {
            $ret['code']='fail';
            return json_encode($ret);
        }
        Db::table('admin_comment_report')->insert([
            'commentId'=>$commentId,
            'time'=>$time,
            'reason'=>$reason,
            'detail'=>$detail
        ]);
        $ret['code']='success';
        return json_encode($ret);
    }

    public function reportMerchant($storeId,$time,$reason,$detail,$pictureList) {
        Db::table('admin_store_report')->insert([
            'storeId'=>$storeId,
            'time'=>$time,
            'reason'=>$reason,
            'detail'=>$detail
        ]);
        $id=Db::table('admin_store_report')->where([
            'storeId'=>$storeId,
            'time'=>$time,
            'reason'=>$reason,
            'detail'=>$detail  
        ])->value('id');

        $n=count($pictureList);
        for($i=0;$i<$n;$i++) 
            Db::table('admin_store_report_picturelist')->insert([
                'storeReportId'=>$id,
                'pictureUrl'=>$pictureList[$i]
            ]);
        $ret['code']='success';
        return json_encode($ret);
    }    

    public function reportOtherUser($userId,$time,$reason,$detail,$pictureList) {
        Db::table('admin_user_report')->insert([
            'userId'=>$userId,
            'time'=>$time,
            'reason'=>$reason,
            'detail'=>$detail,
            'isDealed'=>false
        ]);
        $id=Db::table('admin_user_report')->where([
            'userId'=>$userId,
            'time'=>$time,
            'reason'=>$reason,
            'detail'=>$detail  
        ])->value('id');

        $n=count($pictureList);
        for($i=0;$i<$n;$i++) 
            Db::table('admin_user_report_picturelist')->insert([
                'userReportId'=>$id,
                'pictureUrl'=>$pictureList[$i]
            ]);
        $ret['code']='success';
        return json_encode($ret);
    }

    public function appeal($userId,$reason,$detail,$pictureList) {
        Db::table('admin_appeal')->insert([
            'userId'=>$userId,
            'reason'=>$reason,
            'detail'=>$detail
        ]);
        $id=Db::table('admin_appeal')->where([
            'userId'=>$userId,
            'reason'=>$reason,
            'detail'=>$detail  
        ])->value('id');

        $n=count($pictureList);
        for($i=0;$i<$n;$i++) 
            Db::table('admin_appeal_picturelist')->insert([
                'appealId'=>$id,
                'pictureUrl'=>$pictureList[$i]
            ]);
        $ret['code']='success';
        return json_encode($ret);
    }

    public function getReport($userId) {
        $report=Db::table('admin_user_report')->where('userId',$userId)->find();
        if(is_null($report)) {
            $ret['code']='fail';
            $ret['data']=['reason'=>'','detail'=>'','time'=>'','picturelist'=>[]];
            return json_encode($ret);
        }
        $report=Db::table('admin_user_report')->where('userId',$userId)->select()->toArray();
        $n=count($report);
        for($i=0;$i<$n;$i++) {
            $pictureList=Db::table('admin_user_report_picturelist')->where('userReportId',$report[$i]['id'])->column('pictureUrl');
            $reportList[$i]=[
                'reason'=>$report[$i]['reason'],
                'time'=>$report[$i]['time'],
                'detail'=>$report[$i]['detail'],
                'pictureList'=>$pictureList
            ];
        }

        $ret['data']=['reportList'=>$reportList];
        $ret['code']='success';

        return json_encode($ret);
    }

    public function deleteComment($comment_id) {
        $isCanteen=Db::table('comment')->where('id',$comment_id)->value('isCanteen');
        $dishId=Db::table('comment')->where('id',$comment_id)->value('dishId');
        if(Db::table('comment')->where('id',$comment_id)->delete()) {
            $ret['code']='success';
            Db::table('admin_comment_report')->where('commentId',$comment_id)->delete();  //同时删除所有对该评论的举报
            //重新计算评分
            $scores=Db::table('comment')->where(['dishId'=>$dishId,'isCanteen'=>$isCanteen])->column('score');
            $n=count($scores);
            $totalScore=0;

            if($n==0)
                $n=1;
            else 
                for($i=0;$i<$n;$i++) 
                    $totalScore=$totalScore+$scores[$i];
            if($isCanteen)
                Db::table('canteendish')->where('id',$dishId)->update(['score'=>number_format($totalScore/$n,1)]);
            else
                Db::table('dish')->where('id',$dishId)->update(['score'=>number_format($totalScore/$n,1)]);

            Db::table('response')->where('commentId',$comment_id)->delete();
        }
        else
            $ret['code']='fail';
        
        return json_encode($ret);
    } 

    public function getOtherUserInfo($userId,$otherUserId){
        $otherUser=Db::table('userinfo')->where('openId',$otherUserId)->find();
        if(is_null($otherUser)) {
            $ret['code']='fail';
            $ret['data']=[
                'nickName'=>'',
                'remark'=>'',
                'avatarUrl'=>'',
                'isFriend'=>'',
                'followerNum'=>'',
                'followingNum'=>'',
                'favouredStoreNum'=>'',
                'status'=>'',
                'isFollow'=>''
            ];
            return json_encode($ret);
        }
        $follow=Db::table('follow')->where(['followedId'=>$otherUserId,'followerId'=>$userId])->find();
        $friend=Db::table('friend')->where(['userId'=>$userId,'friendId'=>$otherUserId])->find();
        if(is_null($friend))
            $remark='';
        else
            $remark=$friend['remark'];
        $ret['data']=[
            'nickName'=>$otherUser['nickName'],
            'remark'=>$remark,
            'avatarUrl'=>$otherUser['avatarUrl'],
            'isFriend'=>strval(!is_null($friend)),
            'followerNum'=>$otherUser['followerNum'],
            'followingNum'=>$otherUser['followingNum'],
            'favouredStoreNum'=>$otherUser['favouredStoreNum'],
            'status'=>strval($otherUser['status']),
            'isFollow'=>strval(!is_null($follow))
        ];
        $ret['code']='success';
        return json_encode($ret);
    }

    public function getDetailedResponseList($id,$commentId) {
        $comment=Db::table('comment')->where('id',$commentId)->find();
        if(is_null($comment)) {
            $ret['data']=[
                'commentId'=>'',
                'userId'=>'',
                'nickName'=>'',
                'remark'=>'',
                'avatarUrl'=>'',
                'dishId'=>'',
                'dishName'=>'',
                'merchantNickName'=>'',
                'time'=>'',
                'score'=>'',
                'content'=>'',
                'likedList'=>[],
                'isLiked'=>'',
                'responseList'=>[]
            ];
            $ret['code']='fail';
            return json_encode($ret);
        }
        $userId=$comment['userId'];
        $score=$comment['score'];
        $time=$comment['time'];
        $content=$comment['content'];

        $commentor=Db::table('userinfo')->where('openId',$userId)->find();
        $nickName=$commentor['nickName'];
        $avatarUrl=$commentor['avatarUrl'];

        $remark=Db::table('friend')->where(['userId'=>$id,'friendId'=>$userId])->value('remark');
        if(is_null($remark))
            $remark='';
        $dishId=$comment['dishId'];
        
        if($comment['isCanteen']==1) {
            $dish=Db::table('canteendish')->where('id',$dishId)->find();
            $dishName=$dish['dishName'];
            $storeName=Db::table('canteen')->where('id',$dish['canteenId'])->value('name');
        }   
        else { 
            $dish=Db::table('dish')->where('id',$dishId)->find();
            $dishName=$dish['dishName'];
            $storeName=Db::table('store')->where('storeId',$dish['storeId'])->value('storeName');
        }
        $isLiked=!is_null(Db::table('likes')->where(['commentId'=>$commentId,'userId'=>$id])->find());
        if(is_null(Db::table('likes')->where('commentId',$commentId)->find()))
            $likedList=[];
        else {
            $likedId=Db::table('likes')->where('commentId',$commentId)->column('userId');
            $n=count($likedId);
            for($i=0;$i<$n;$i++) {
                $name=Db::table('userinfo')->where('openId',$likedId[$i])->value('nickName');
                $rmk=Db::table('friend')->where(['userId'=>$id,'friendId'=>$likedId[$i]])->value('remark');
                if(is_null($rmk))
                    $rmk='';
                $likedList[$i]=[
                    'nickName'=>$name,
                    'remark'=>$rmk,
                    'id'=>$likedId[$i]
                ];
            }
        }
        if(is_null(Db::table('response')->where('commentId',$commentId)->find())) 
            $responseList=[];
        else {
            $responses=Db::table('response')->where('commentId',$commentId)->select()->toArray();
            $n=count($responses);
            for($i=0;$i<$n;$i++) {
                $fromId=$responses[$i]['userId'];
                $toId=$responses[$i]['towhomId'];
                $detail=$responses[$i]['content'];
                $fromNickName=Db::table('userinfo')->where('openId',$fromId)->value('nickName');
                $toNickName=Db::table('userinfo')->where('openId',$toId)->value('nickName');
                $fromRemark=Db::table('friend')->where(['userId'=>$id,'friendId'=>$fromId])->value('remark');
                if(is_null($fromRemark))
                    $fromRemark='';
                $toRemark=Db::table('friend')->where(['userId'=>$id,'friendId'=>$toId])->value('remark');
                if(is_null($toRemark))
                    $toRemark='';
                $responseList[$i]=[
                    'fromId'=>$fromId,
                    'toId'=>$toId,
                    'fromNickName'=>$fromNickName,
                    'toNickName'=>$toNickName,
                    'fromRemark'=>$fromRemark,
                    'toRemark'=>$toRemark,
                    'detail'=>$detail
                ];
            }
        }

            $ret['data']=[
                'commentId'=>strval($commentId),
                'userId'=>$userId,
                'nickName'=>$nickName,
                'remark'=>$remark,
                'avatarUrl'=>$avatarUrl,
                'dishId'=>strval($dishId),
                'dishName'=>$dishName,
                'merchantNickName'=>$storeName,
                'time'=>$time,
                'score'=>strval($score),
                'content'=>$content,
                'likedList'=>$likedList,
                'isLiked'=>strval($isLiked),
                'responseList'=>$responseList
            ];
        $ret['code']='success';
        return json_encode($ret);
        
    }

    public function getUserIdentity($userId) {
        $ret['data']=['identity'=>strval(Db::table('userinfo')->where('openId',$userId)->value('identity'))];
        $ret['code']='success';
        return json_encode($ret);
    }

}