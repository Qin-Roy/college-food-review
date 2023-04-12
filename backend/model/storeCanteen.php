<?php

namespace app\model;

use think\Model;
use think\facade\Db;

class storeCanteen extends Model {
    protected $name = 'userinfo';

    public function editInfo($info) {
        if(intval($info['isCanteen'])==0) {
            $flag1=Db::table('store')->where('storeId',$info['id'])->find();
            $schoolId=Db::table('school')->where('name',$info['schoolName'])->value('id');
            Db::table('userinfo')->where('openId',$info['id'])->update(['nickName'=>$info['nickName'],'avatarUrl'=>$info['avatarUrl'],'school'=>$info['schoolName']]);
            if(!is_null($flag1)) 
                Db::table('store')->where('storeId',$info['id'])->update(['storeName'=>$info['nickName'],'avatarUrl'=>$info['avatarUrl'],'intro'=>$info['intro'],'address'=>$info['address'],'tel'=>$info['tel'],'schoolId'=>$schoolId]);
            else 
                Db::table('store')->insert(['storeId'=>$info['id'],'storeName'=>$info['nickName'],'avatarUrl'=>$info['avatarUrl'],'intro'=>$info['intro'],'address'=>$info['address'],'tel'=>$info['tel'],'schoolId'=>$schoolId]);
            
            Db::table('category')->where('storeId',$info['id'])->delete();
            for($i=0;$i<count($info['categoryList']);$i++) 
            {
                $Url=Db::table('const_category')->where('category',$info['categoryList'][$i])->value('pictureUrl');
                if (is_null($Url))
                    $Url="";
                $data=['storeId'=>$info['id'],'name'=>$info['categoryList'][$i],'pictureUrl'=>$Url];

                Db::table('category')->insert($data);
            }
            Db::table('storepicturelist')->where('storeId',$info['id'])->delete();
            for($i=0;$i<count($info['pictureList']);$i++)
                Db::table('storepicturelist')->insert(['storeId'=>$info['id'],'Url'=>$info['pictureList'][$i]]);
        }
        else if(intval($info['isCanteen'])==1) {
            $key = intval($info['id']);
            $picture_list = $info['pictureList'];
            $category_list = $info['categoryList'];
            Db::table('canteen')->where('id',$key)->update(['name'=>$info['nickName']]);
            Db::table('canteen')->where('id',$key)->update(['avatarUrl'=>$info['avatarUrl']]);
            Db::table('canteen')->where('id',$key)->update(['intro'=>$info['intro']]);
            Db::table('canteen')->where('id',$key)->update(['tel'=>$info['tel']]);
            Db::table('canteen')->where('id',$key)->update(['address'=>$info['address']]);

            //修改类别
            //先删除旧类别
            Db::table('canteen_category')->where('canteenId',$key)->delete();
            //再加新类别
            for ($i = 0;$i < count($category_list);$i++) {
                $categoryname = Db::table('const_category')->where('id',intval($category_list[$i]))->value('category');
                $categoryurl = Db::table('const_category')->where('id',intval($category_list[$i]))->value('pictureUrl');
                $categorydata = [
                    'canteenId'=>$key,
                    'name'=>$categoryname,
                    'pictureUrl'=>$categoryurl
                ];
                Db::table('canteen_category')->insert($categorydata);
            }
            
            //修改图片
            //先删除旧图片url
            Db::table('canteenpicturelist')->where('canteenId',$key)->delete();
            //再加新图片url
            for ($i = 0;$i < count($picture_list);$i++) {
                $picturedata = [
                    'canteenId'=>$key,
                    'Url'=>$picture_list[$i]
                ];
                Db::table('canteenpicturelist')->insert($picturedata);
            }
        }   
        $ret['code']='success';
        return json_encode($ret);
    }

    public function addDish($info) {
        if($info['isCanteen']=='0') {
            if(Db::table('store')->where('storeId',$info['merchantId'])->find()) {
                Db::table('dish')->insert([
                    'dishName'=>$info['dishName'],
                    'intro'=>$info['intro'],
                    'price'=>$info['price'],
                    'storeId'=>$info['merchantId'],
                    'score'=>-1
                ]);
                $id=Db::table('dish')->where(['dishName'=>$info['dishName'],'storeId'=>$info['merchantId']])->value('id');
                for($i=0;$i<count($info['pictureList']);$i++)
                    Db::table('dishpicturelist')->insert(['dishId'=>$id,'Url'=>$info['pictureList'][$i]]);
                $ret['code']='success';
            } 
            else
                $ret['code']='fail';
        }
        else if($info['isCanteen']=='1'){
            if(Db::table('canteen')->where('id',intval($info['merchantId']))->find()) {
                Db::table('canteendish')->insert([
                    'dishName'=>$info['dishName'],
                    'intro'=>$info['intro'],
                    'price'=>$info['price'],
                    'canteenId'=>intval($info['merchantId']),
                    'score'=>-1
                ]);
                $id=Db::table('canteendish')->getLastInsID();
                for($i=0;$i<count($info['pictureList']);$i++)
                    Db::table('canteendishpicturelist')->insert(['dishId'=>$id,'Url'=>$info['pictureList'][$i]]);
                $ret['code']='success';
            } 
            else
                $ret['code']='fail';
        }
        
        return json_encode($ret);
    }

    public function editDish($info) {
        if($info['isCanteen']=='0') {
            Db::table('dish')->where('id',intval($info['dishId']))->update(['dishName'=>$info['dishName'],'price'=>$info['price'],'intro'=>$info['intro']]);
            Db::table('dishpicturelist')->where('dishId',intval($info['dishId']))->delete();
            for($i=0;$i<count($info['pictureList']);$i++)
                Db::table('dishpicturelist')->insert(['dishId'=>intval($info['dishId']),'Url'=>$info['pictureList'][$i]]);
            $ret['code']='success';
            return json_encode($ret);
        }
        else if($info['isCanteen']=='1') {
            Db::table('canteendish')->where('id',intval($info['dishId']))->update(['dishName'=>$info['dishName'],'price'=>$info['price'],'intro'=>$info['intro']]);
            Db::table('canteendishpicturelist')->where('dishId',intval($info['dishId']))->delete();
            for($i=0;$i<count($info['pictureList']);$i++)
                Db::table('canteendishpicturelist')->insert(['dishId'=>intval($info['dishId']),'Url'=>$info['pictureList'][$i]]);
            $ret['code']='success';
            return json_encode($ret);
        }

    }

    public function deleteDish($dishId,$isCanteen) {
        if($isCanteen=='0') {
            if(Db::table('dish')->where('id',$dishId)->delete()) {
                Db::table('dishpicturelist')->where('dishId',$dishId)->delete();
                $commentId=Db::table('comment')->where(['dishId'=>$dishId,'isCanteen'=>0])->column('id');
                Db::table('comment')->where(['dishId'=>$dishId,'isCanteen'=>0])->delete();
                $n=count($commentId);
                for($i=0;$i<$n;$i++)
                    Db::table('response')->where('commentId',$commentId[$i])->delete();
                $ret['code']='success';
            }
            else
                $ret['code']='fail';
        }
        else if($isCanteen=='1'){
            if(Db::table('canteendish')->where('id',$dishId)->delete()) {
                Db::table('canteendishpicturelist')->where('dishId',$dishId)->delete();
                $commentId=Db::table('comment')->where(['dishId'=>$dishId,'isCanteen'=>1])->column('id');
                Db::table('comment')->where(['dishId'=>$dishId,'isCanteen'=>1])->delete();
                $n=count($commentId);
                for($i=0;$i<$n;$i++)
                    Db::table('response')->where('commentId',$commentId[$i])->delete();

                $ret['code']='success';
            }
            else
                $ret['code']='fail';
        }
        return json_encode($ret);
    }

    public function getSingleDishInfo($dishId,$isCanteen) {
        if($isCanteen=='0') {
            $dish=Db::table('dish')->where('id',$dishId)->find();
            if(is_null($dish)) {
                $ret['data']=[
                    'dishName'=>'',
                    'dishId'=>'',
                    'intro'=>'',
                    'price'=>'',
                    'pictureList'=>[]
                ];
                $ret['code']='success';
                return json_encode($ret);
            }   

            $dishPictureList=Db::table('dishpicturelist')->where('dishId',$dishId)->column('Url');
            $tmp2=[
                'dishId'=>strval($dishId),
                'dishName'=>$dish['dishName'],
                'pictureList'=>$dishPictureList,
                'price'=>strval($dish['price']),
                'intro'=>$dish['intro']
            ];
            $ret['data']=$tmp2;
        }
        else if($isCanteen=='1') {
            $dish=Db::table('canteendish')->where('id',$dishId)->find();
            if(is_null($dish)) {
                $ret['data']=[
                    'dishName'=>'',
                    'dishId'=>'',
                    'intro'=>'',
                    'price'=>'',
                    'pictureList'=>[]
                ];
                $ret['code']='success';
                return json_encode($ret);
            }   
            $dishPictureList=Db::table('canteendishpicturelist')->where('dishId',$dishId)->column('Url');
            $tmp2=[
                'dishId'=>strval($dishId),
                'dishName'=>$dish['dishName'],
                'pictureList'=>$dishPictureList,
                'price'=>strval($dish['price']),
                'intro'=>$dish['intro']
            ];
            $ret['data']=$tmp2;
        }
        $ret['code']='success';
        return json_encode($ret);
    }
}