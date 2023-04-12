<?php

namespace app\model;

use think\Model;
use think\facade\Db;

class storePage extends Model {

    protected $name = 'userinfo';

    public function getMerchant($storeId,$userId) {
        $store = Db::table('store')->where('storeId',$storeId)->find();  
        $pictureinfo = Db::table('storepicturelist')->where('storeId',$storeId)->column('Url');
        $categoryinfo = Db::table('category')->where('storeId',$storeId)->column('name');
    
        $data=[
            'nickName'=>$store['storeName'],
            'avatarUrl'=>$store['avatarUrl'],
            'isCanteen'=>'0',
            'address'=>$store['address'],
            'tel'=>$store['tel'],
            'intro'=>$store['intro'],
            'categoryList'=>$categoryinfo,
            'pictureList'=>$pictureinfo,
            'isFavoured'=>strval((int)!is_null(Db::table('favouredstore')->where(['userId'=>$userId,'storeId'=>$storeId])->find())),
            'status'=>strval(Db::table('userinfo')->where('openId',$storeId)->value('status'))
        ];
    
        //activitylist
        if(is_null(Db::table('activity')->where('storeId',$storeId)->find()))
            $data['activityList']=[];
        else {
                $activity=Db::table('activity')->where('storeId',$storeId)->select()->toArray();
                $count=count($activity);
                for($j=0;$j<$count;$j++) 
                    $data['activityList'][$j]=[
                        'activityName'=>$activity[$j]['activityName'],
                        'activityId'=>strval($activity[$j]['id']),
                        'intro'=>$activity[$j]['intro'],
                        'picture'=>$activity[$j]['pictureUrl'],
                        'slogan'=>$activity[$j]['slogan'],
                        'startTime'=>$activity[$j]['startTime'],
                        'endTime'=>$activity[$j]['endTime'],
                        'merchantId'=>$activity[$j]['storeId']
                    ];
        }
        if(is_null(Db::table('dish')->where('storeId',$storeId)->find()))
            $data['dishList']=[];
        else {
            $dishList=Db::table('dish')->where('storeId',$storeId)->select()->toArray();
            for($i=0;$i<count($dishList);$i++) {
                $dishPic=Db::table('dishpicturelist')->where('dishId',$dishList[$i]['id'])->value('Url');
                if(is_null($dishPic))
                    $dishPic='';
                $data['dishList'][$i]=[
                    'dishId'=>strval($dishList[$i]['id']),
                    'dishName'=>$dishList[$i]['dishName'],
                    'intro'=>$dishList[$i]['intro'],
                    'price'=>strval($dishList[$i]['price']),
                    'score'=>strval($dishList[$i]['score']),
                    'picture'=>$dishPic
                ];
            }
        }
        $ret['code']='success';
        $ret['data']=$data;
        return json_encode($ret);
    }

    public function getMerchantDishes($dishId,$userId,$isCanteen) {
        if($isCanteen=='0') {
            $dish=Db::table('dish')->where('id',$dishId)->find();
            $dishName=$dish['dishName'];
            $dishPic=Db::table('dishpicturelist')->where('dishId',$dishId)->column('Url');
            $storeName=Db::table('store')->where('storeId',$dish['storeId'])->value('storeName');
        }
        else {
            $dish=Db::table('canteendish')->where('id',$dishId)->find();
            $dishName=$dish['dishName'];
            $dishPic=Db::table('canteendishpicturelist')->where('dishId',$dishId)->column('Url');
            $storeName=Db::table('canteen')->where('id',$dish['canteenId'])->value('name');
        }
        if(is_null($dish)){
            $data=[
                'dishId'=>'',
                'dishName'=>'',
                'intro'=>'',
                'score'=>'',
                'price'=>'',
                'pictureList'=>[],
                'commentList'=>[]
            ];
            $ret['code']='success';
            $ret['data']=$data;
            return json_encode($ret);
        }

        if(is_null(Db::table('comment')->where(['dishId'=>$dishId,'isCanteen'=>intval($isCanteen)])->find()))
            $commentList=[];
        else {
            $comments=Db::table('comment')->where(['dishId'=>$dishId,'isCanteen'=>intval($isCanteen)])->select()->toArray();
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
                    $fromRemark=Db::table('friend')->where(['userId'=>$userId,'friendId'=>$fromId])->value('remark');
                    if(is_null($fromRemark))
                        $fromRemark='';
                    $toRemark=Db::table('friend')->where(['userId'=>$userId,'friendId'=>$toId])->value('remark');
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
                    $responseNum=count(Db::table('response')->where('commentId',$comments[$x]['id'])->select()->toArray());
                }

                $isLiked=!is_null(Db::table('likes')->where(['userId'=>$userId,'commentId'=>$comments[$x]['id']])->find());
                $user=Db::table('userinfo')->where('openId',$comments[$x]['userId'])->find();
                $remark=Db::table('friend')->where(['userId'=>$userId,'friendId'=>$user['openId']])->value('remark');
                if(is_null($remark))
                    $remark='';
                $commentList[$x]=[
                    'userId'=>$user['openId'],
                    'nickName'=>$user['nickName'],
                    'remark'=>$remark,
                    'avatarUrl'=>$user['avatarUrl'],
                    'dishId'=>strval($dishId),
                    'dishName'=>$dishName,
                    'merchantNickName'=>$storeName,
                    'commentId'=>strval($comments[$x]['id']),
                    'time'=>$comments[$x]['time'],
                    'content'=>$comments[$x]['content'],
                    'score'=>strval($comments[$x]['score']),
                    'likedNum'=>$comments[$x]['likedNum'],
                    'isLiked'=>strval((int)$isLiked),
                    'responseNum'=>$responseNum,
                    'firstResponse'=>$fisrtResponse
                ];
            }
        }

        $data=[
            'dishId'=>strval($dishId),
            'dishName'=>$dish['dishName'],
            'intro'=>$dish['intro'],
            'score'=>strval($dish['score']),
            'price'=>strval($dish['price']),
            'pictureList'=>$dishPic,
            'commentList'=>$commentList
        ];
        $ret['code']='success';
        $ret['data']=$data;
        return json_encode($ret);
    }

    public function getActivityInfo($activityId) {
        $activity=Db::table('activity')->where('id',$activityId)->find();
        if(is_null($activity)) {
            $data=[
                'activityName'=>'',
                'intro'=>'',
                'picture'=>'',
                'slogan'=>'',
                'startTime'=>'',
                'endTime'=>'',
                'merchantId'=>'',
                'merchantNickName'=>''
            ];
            return json_encode($data);
        }

        $nickName=Db::table('store')->where('storeId',$activity['storeId'])->value('storeName');
            $data=[
                'activityName'=>$activity['activityName'],
                'intro'=>$activity['intro'],
                'picture'=>$activity['pictureUrl'],
                'slogan'=>$activity['slogan'],
                'startTime'=>$activity['startTime'],
                'endTime'=>$activity['endTime'],
                'merchantId'=>$activity['storeId'],
                'merchantNickName'=>$nickName
            ];

        return json_encode($data);
    }
}