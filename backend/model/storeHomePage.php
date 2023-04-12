<?php

namespace app\model;

use think\Model;
use think\facade\Db;

class storeHomePage extends Model {

    protected $name = 'userinfo';

    public function getStoreInfo($id) {
        $store=Db::table('store')->where('storeId',$id)->find();
        if(is_null($store)) {
            $ret['code']='fail';
            $ret['data']=[
                'nickName'=>'',
                'avatarUrl'=>'',
                'categoryList'=>[],
                'intro'=>'',
                'tel'=>'',
                'address'=>'',
                'pictureList'=>[],
                'activityList'=>[],
                'dishList'=>[]
            ];
            return json_encode($ret);
        }
        $ret['code']='success';
        $ret['data']=[
            'nickName'=>$store['storeName'],
            'avatarUrl'=>$store['avatarUrl'],
            'intro'=>$store['intro'],
            'tel'=>$store['tel'],
            'address'=>$store['address']
        ];
        
        if(!is_null(Db::table('category')->where('storeId',$id)->column('name')))
            $ret['data']['categoryList']=Db::table('category')->where('storeId',$id)->column('name');
        else
            $ret['data']['categoryList']=[];
        if(!is_null(Db::table('storepicturelist')->where('storeId',$id)->column('Url')))
            $ret['data']['pictureList']=Db::table('storepicturelist')->where('storeId',$id)->column('Url');
        else
            $ret['data']['pictureList']=[];

        if(is_null(Db::table('activity')->where('storeId',$id)->find())) 
            $ret['data']['activityList']=[];
        else {
            $activity=Db::table('activity')->where('storeId',$id)->select()->toArray();
            for($i=0;$i<count($activity);$i++) {
                $tmp1[$i]=[
                    'activityName'=>$activity[$i]['activityName'],
                    'activityId'=>strval($activity[$i]['id']),
                    'intro'=>$activity[$i]['intro'],
                    'slogan'=>$activity[$i]['slogan'],
                    'startTime'=>$activity[$i]['startTime'],
                    'endTime'=>$activity[$i]['endTime'],
                    'picture'=>$activity[$i]['pictureUrl']
                ];
            }
            $ret['data']['activityList']=$tmp1;
        }
        $dish=Db::table('dish')->where('storeId',$id)->select()->toArray();
        if(count($dish)) {
            for($i=0;$i<count($dish);$i++) {
                $dishPictureList=Db::table('dishpicturelist')->where('dishId',$dish[$i]['id'])->column('Url');
                $tmp2[$i]=[
                    'dishId'=>strval($dish[$i]['id']),
                    'dishName'=>$dish[$i]['dishName'],
                    'dishPictureList'=>$dishPictureList,
                    'price'=>strval($dish[$i]['price']),
                    'score'=>strval($dish[$i]['score']),
                    'intro'=>$dish[$i]['intro']
                ];
            }
            $ret['data']['dishList']=$tmp2;
        }
        else
            $ret['data']['dishList']=[];
        return json_encode($ret);
    }

    public function getCategory() {
        $category=Db::table('const_category')->column('category');
        $ret['code']='success';
        $ret['data']=['TotalCategoryList'=>$category];
        return json_encode($ret);
    }

    public function editActivity($merchantId,$activityId,$activityName,$intro,$slogan,$picture,$startTime,$endTime) {
        if(Db::table('activity')->where('id',$activityId)->update([
            'activityName'=>$activityName,
            'intro'=>$intro,
            'pictureUrl'=>$picture,
            'slogan'=>$slogan,
            'startTime'=>$startTime,
            'endTime'=>$endTime,
            'storeId'=>$merchantId
        ]))
            $ret['code']='success';
        else
            $ret['code']='fail';
        $users=Db::table('favouredstore')->where('storeId',$merchantId)->column('userId');
        for($i=0;$i<count($users);$i++) {
            Db::table('activityfeedback')->insert([
                'userId'=>$users[$i],
                'storeId'=>$merchantId,
                'activityName'=>$activityName,
                'startTime'=>$startTime,
                'endTime'=>$endTime,
                'detail'=>$intro
            ]);   
        } 

        return json_encode($ret);
    }

    public function addActivity($merchantId,$activityName,$intro,$slogan,$picture,$startTime,$endTime) {
        if(Db::table('activity')->insert([
            'activityName'=>$activityName,
            'intro'=>$intro,
            'pictureUrl'=>$picture,
            'slogan'=>$slogan,
            'startTime'=>$startTime,
            'endTime'=>$endTime,
            'storeId'=>$merchantId
        ]))
            $ret['code']='success';
        else
            $ret['code']='fail';

        $users=Db::table('favouredstore')->where('storeId',$merchantId)->column('userId');
        for($i=0;$i<count($users);$i++) {
            Db::table('activityfeedback')->insert([
                'userId'=>$users[$i],
                'storeId'=>$merchantId,
                'activityName'=>$activityName,
                'startTime'=>$startTime,
                'endTime'=>$endTime,
                'detail'=>$intro
            ]);  
        }     
        return json_encode($ret);
    }

    public function deleteActivity($activityId) {
        if(Db::table('activity')->where('id',$activityId)->delete())
            $ret['code']='success';
        else
            $ret['code']='fail';
        return json_encode($ret);
    }
}