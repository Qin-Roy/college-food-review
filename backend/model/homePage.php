<?php

namespace app\model;

use think\Model;
use think\facade\Db;

class homePage extends Model {

    protected $name = 'userinfo';
    //获取学校列表
    public function getSchoolList()
    {
        $code = 'success';
        $targetid = Db::table('school')->column('id');
        if (!sizeof($targetid)) {
            $ret['code'] = $code;
            $ret['data'] = ['schoolList'=>array()];
            return json_encode($ret);
        }
        $schoolList = [];
        for ($i = 0;$i < sizeof($targetid);$i++) {
            $schoolName = Db::table('school')->where('id',$targetid[$i])->value('name');
            $schoolList[$i]=[
                'schoolName'=>$schoolName,
                'schoolId'=>strval($targetid[$i])
            ];
        }
        $dat['schoolList'] = $schoolList;
        $ret['data'] = $dat;
        $ret['code'] = $code;
        return json_encode($ret);
    }

     //获取某一类别商家
    public function getStoresList($schoolid,$category)
    {
        //schoolid为学校，category为类别

        $storeList = [];
        $code = 'success';
        //先筛选符合schoolid的商家id
        $tempstoreid = Db::table('store')->where('schoolId',$schoolid)->column('storeId');
        $tempcanteenid = Db::table('canteen')->where('schoolId',$schoolid)->column('id');
        $targetstoreid = [];
        $targetcanteenid = [];
        if (!sizeof($tempstoreid) && !sizeof($tempcanteenid)) {
            $ret['code'] = $code;
            $ret['data'] = ['storeList'=>array()];
            return json_encode($ret);
        }
        //再筛选满足类别名字的商家id
        for ($i = 0;$i < sizeof($tempstoreid);$i++) {
            $lookfor = Db::table('category')->where(['name'=>$category,'storeId'=>$tempstoreid[$i]])->find();
            if (!is_null($lookfor)) {
                array_push($targetstoreid,$tempstoreid[$i]);
            }
        }
        //和食堂id
        for ($i = 0;$i < sizeof($tempcanteenid);$i++) {
            $lookfor = Db::table('canteen_category')->where('name',$category)->where('canteenId',$tempcanteenid[$i])->find();
            if (!is_null($lookfor)) {
                array_push($targetcanteenid,$tempcanteenid[$i]);
            }
        }
        if (!sizeof($targetstoreid) && !sizeof($targetcanteenid)) {
            $ret['code'] = $code;
            $ret['data'] = ['storeList'=>[]];
            return json_encode($ret);
        }
         
        $k = 0;//索引
        //商家
        for ($j = 0;$j < sizeof($targetstoreid);$j++) {
            $nicknameinfo = Db::table('store')->where('storeId',$targetstoreid[$j])->value('storeName');
            $avatarUrlinfo = Db::table('store')->where('storeId',$targetstoreid[$j])->value('avatarUrl');
            $addressinfo = Db::table('store')->where('storeId',$targetstoreid[$j])->value('address');
            $telinfo = Db::table('store')->where('storeId',$targetstoreid[$j])->value('tel');    
            $introinfo = Db::table('store')->where('storeId',$targetstoreid[$j])->value('intro');
            $pictureinfo = Db::table('storepicturelist')->where('storeId',$targetstoreid[$j])->column('Url');
            $categoryinfo = Db::table('category')->where('storeId',$targetstoreid[$j])->column('name');
 
            $storeList[$k++]=[
                'id'=>strval($targetstoreid[$j]),
                'avatarUrl'=>$avatarUrlinfo,
                'nickName'=>$nicknameinfo,
                'pictureList'=>$pictureinfo,
                'address'=>$addressinfo,
                'tel'=>$telinfo,
                'intro'=>$introinfo,
                'categoryList'=>$categoryinfo,
                'isCanteen'=>strval(0)
            ];
        }
        //食堂
        for ($j = 0;$j < sizeof($targetcanteenid);$j++) {
            $nicknameinfo = Db::table('canteen')->where('id',$targetcanteenid[$j])->value('name');
            $avatarUrlinfo = Db::table('canteen')->where('id',$targetcanteenid[$j])->value('avatarUrl');
            $addressinfo = Db::table('canteen')->where('id',$targetcanteenid[$j])->value('address');
            $telinfo = Db::table('canteen')->where('id',$targetcanteenid[$j])->value('tel');    
            $introinfo = Db::table('canteen')->where('id',$targetcanteenid[$j])->value('intro');
            $pictureinfo = Db::table('canteenpicturelist')->where('canteenId',$targetcanteenid[$j])->column('Url');
            $categoryinfo = Db::table('canteen_category')->where('canteenId',$targetcanteenid[$j])->column('name');
 
            $storeList[$k++]=[
                'id'=>strval($targetcanteenid[$j]),
                'avatarUrl'=>$avatarUrlinfo,
                'nickName'=>$nicknameinfo,
                'pictureList'=>$pictureinfo,
                'address'=>$addressinfo,
                'tel'=>$telinfo,
                'intro'=>$introinfo,
                'categoryList'=>$categoryinfo,
                'isCanteen'=>strval(1)
            ];
        }
        $dat['storeList'] = $storeList;
        $ret['data'] = $dat;
        $ret['code'] = $code;
        return json_encode($ret);
    }

    //首页通过学校获取商家、菜品类别及活动信息
    public function getStore_Act_throughSchoolid($reqid)   //schoolid
    {
        $cate=[];

        $storeList = [];
        $category = [];
        $activityList = [];
        $code = 'success';
        $targetid = Db::table('store')->where('schoolId',$reqid)->column('storeId');
        // if (!sizeof($targetid)) {
        //     $ret['code'] = $code;
        //     $ret['data'] = [
        //         'storeList'=>array(),
        //         'category'=>array(),
        //         'activityList'=>array()
        //     ];
        //     return json_encode($ret);
        // }
        $k = 0;
        for ($i = 0;$i < sizeof($targetid);$i++) {
            //storelist,此部分不需要修改
            $nicknameinfo = Db::table('store')->where('storeId',$targetid[$i])->value('storeName');
            $avatarUrlinfo = Db::table('store')->where('storeId',$targetid[$i])->value('avatarUrl');
            $addressinfo = Db::table('store')->where('storeId',$targetid[$i])->value('address');
            $telinfo = Db::table('store')->where('storeId',$targetid[$i])->value('tel');    
            $introinfo = Db::table('store')->where('storeId',$targetid[$i])->value('intro');
            $pictureinfo = Db::table('storepicturelist')->where('storeId',$targetid[$i])->column('Url');
            $categoryinfo = Db::table('category')->where('storeId',$targetid[$i])->column('name');
            $storeList[$k++]=[
                'id'=>strval($targetid[$i]),
                'avatarUrl'=>$avatarUrlinfo,
                'nickName'=>$nicknameinfo,
                'pictureList'=>$pictureinfo,
                'address'=>$addressinfo,
                'tel'=>$telinfo,
                'intro'=>$introinfo,
                'categoryList'=>$categoryinfo,
                'isCanteen'=>'0'
            ];
            for($j=0;$j<count($categoryinfo);$j++)
                array_push($cate,$categoryinfo[$j]);

            //活动部分，只有商家才有活动，此部分不需要修改
            if(is_null(Db::table('activity')->where('storeId',$targetid[$i])->find()))
                ;
            else {
                $activity=Db::table('activity')->where('storeId',$targetid[$i])->select()->toArray();
                if(count($activity)>5)
                    $count=5;
                else
                    $count=count($activity);
                for($j=0;$j<$count;$j++) {
                    $act=[
                        'activityName'=>$activity[$j]['activityName'],
                        'activityId'=>strval($activity[$j]['id']),
                        'intro'=>$activity[$j]['intro'],
                        'picture'=>$activity[$j]['pictureUrl'],
                        'slogan'=>$activity[$j]['slogan'],
                        'startTime'=>$activity[$j]['startTime'],
                        'endTime'=>$activity[$j]['endTime'],
                        'merchantId'=>$activity[$j]['storeId']
                    ];
                    array_push($activityList,$act);
                }
            }
        }

        //storeList还需要添加食堂,在下面实现 **update 5/26 20:57
        $canteenid = Db::table('canteen')->where('schoolId',$reqid)->column('id');
        for ($i = 0;$i < sizeof($canteenid);$i++) {
            //storelist,此部分不需要修改
            $nicknameinfo = Db::table('canteen')->where('id',$canteenid[$i])->value('name');
            $avatarUrlinfo = Db::table('canteen')->where('id',$canteenid[$i])->value('avatarUrl');
            $addressinfo = Db::table('canteen')->where('id',$canteenid[$i])->value('address');
            $telinfo = Db::table('canteen')->where('id',$canteenid[$i])->value('tel');    
            $introinfo = Db::table('canteen')->where('id',$canteenid[$i])->value('intro');
            $pictureinfo = Db::table('canteenpicturelist')->where('canteenId',$canteenid[$i])->column('Url');
            $categoryinfo = Db::table('canteen_category')->where('canteenId',$canteenid[$i])->column('name');
            $storeList[$k++]=[
                'id'=>strval($canteenid[$i]),
                'avatarUrl'=>$avatarUrlinfo,
                'nickName'=>$nicknameinfo,
                'pictureList'=>$pictureinfo,
                'address'=>$addressinfo,
                'tel'=>$telinfo,
                'intro'=>$introinfo,
                'categoryList'=>$categoryinfo,
                'isCanteen'=>'1'
            ];
            for($j=0;$j<count($categoryinfo);$j++)
                array_push($cate,$categoryinfo[$j]);
        }
        
        //categorylist,这里需要修改，直接获取所有分类即可 **update 5/26 20:57
 
        $counter=count($cate); 
        $record = Db::table('const_category')->select()->toArray();
        // var_dump($cate);
        // var_dump($record);
        for($i=0;$i<count($record);$i++) {
            for($j=0;$j<$counter;$j++) {
                if($cate[$j]==$record[$i]['category']) {
                    $data=['name'=>$record[$i]['category'],'pictureUrl'=>$record[$i]['pictureUrl']];
                    array_push($category,$data);
                    break;
                }
            }
        }

        $dat['storeList'] = $storeList;
        $dat['category'] = $category;
        $dat['activityList'] = $activityList;
        $ret['data'] = $dat;
        $ret['code'] = $code;
        return json_encode($ret);
    }

   //搜索菜品
   public function searchDish($reqname)    //还需要能搜到食堂
   {
       $storeList = [];
       $dishList = [];
       $code = 'success';
       //$targetstring = $reqname;
       //$searchdish['dishName'] = ['like','%'.$targetstring.'%'];
       //$searchstore['storeName'] = ['like','%'.$targetstring.'%'];
       //$searchcanteen['name'] = ['like','%'.$targetstring.'%'];

       //$storenameid = Db::table('store')->where($searchstore)->column('id');
       $storenameid = Db::table('store')->where('storeName','like','%'.$reqname.'%')->column('id');
       $k = 0;
       //搜索商家名字 **update 2022.5.30 11:24(只改了storeList索引K)
       for ($i = 0;$i < sizeof($storenameid);$i++) {
           $storeIdinfo = Db::table('store')->where('id',$storenameid[$i])->value('storeId');
           $nicknameinfo = Db::table('store')->where('id',$storenameid[$i])->value('storeName');
           $avatarUrlinfo = Db::table('store')->where('id',$storenameid[$i])->value('avatarUrl');
           $addressinfo = Db::table('store')->where('id',$storenameid[$i])->value('address');
           $telinfo = Db::table('store')->where('id',$storenameid[$i])->value('tel');    
           $introinfo = Db::table('store')->where('id',$storenameid[$i])->value('intro');
           $pictureinfo = Db::table('storepicturelist')->where('storeId',$storeIdinfo)->column('Url');
           $categoryinfo = Db::table('category')->where('storeId',$storeIdinfo)->column('name');

           $storeList[$k++]=[
               'id'=>$storeIdinfo,
               'avatarUrl'=>$avatarUrlinfo,
               'nickName'=>$nicknameinfo,
               'pictureList'=>$pictureinfo,
               'address'=>$addressinfo,
               'tel'=>$telinfo,
               'intro'=>$introinfo,
               'categoryList'=>$categoryinfo,
               'isCanteen'=>'0'
           ];
       }
       //搜索食堂名字 **update 张智淋 2022.5.40 11:25
       //$canteenid = Db::table('canteen')->where($searchcanteen)->column('id');
       $canteenid = Db::table('canteen')->where('name','like','%'.$reqname.'%')->column('id');
       for ($i = 0;$i < sizeof($canteenid);$i++) {
           $nicknameinfo = Db::table('canteen')->where('id',$canteenid[$i])->value('name');
           $avatarUrlinfo = Db::table('canteen')->where('id',$canteenid[$i])->value('avatarUrl');
           $addressinfo = Db::table('canteen')->where('id',$canteenid[$i])->value('address');
           $telinfo = Db::table('canteen')->where('id',$canteenid[$i])->value('tel');    
           $introinfo = Db::table('canteen')->where('id',$canteenid[$i])->value('intro');
           $pictureinfo = Db::table('canteenpicturelist')->where('canteenId',$canteenid[$i])->column('Url');
           $categoryinfo = Db::table('canteen_category')->where('canteenId',$canteenid[$i])->column('name');

           $storeList[$k++]=[
               'id'=>strval($canteenid[$i]),
               'avatarUrl'=>$avatarUrlinfo,
               'nickName'=>$nicknameinfo,
               'pictureList'=>$pictureinfo,
               'address'=>$addressinfo,
               'tel'=>$telinfo,
               'intro'=>$introinfo,
               'categoryList'=>$categoryinfo,
               'isCanteen'=>'1'
           ];
       }

       $p = 0;
       //搜索商家菜品 **update 张智淋 2022.5.30 11:31
       //$storedishid = Db::table('dish')->where($searchdish)->column('id');
       $storedishid = Db::table('dish')->where('dishName','like','%'.$reqname.'%')->column('id');
       for ($i = 0;$i < sizeof($storedishid);$i++) {
           $dishnameinfo = Db::table('dish')->where('id',$storedishid[$i])->value('dishName');
           $priceinfo =  Db::table('dish')->where('id',$storedishid[$i])->value('price');
           $scoreinfo =  Db::table('dish')->where('id',$storedishid[$i])->value('score');
           $introinfo = Db::table('dish')->where('id',$storedishid[$i])->value('intro');
           $pictureinfo = Db::table('dishpicturelist')->where('dishId',$storedishid[$i])->value('Url');

           $dishList[$p++]=[
               'dishId'=>strval($storedishid[$i]),
               'dishName'=>$dishnameinfo,
               'price'=>$priceinfo,
               'score'=>strval($scoreinfo),
               'picture'=>$pictureinfo,
               'intro'=>$introinfo,
               'isCanteen'=>'0'
           ];
       }

       //搜索食堂菜品 **update 张智淋 2022.5.30 11:57
       //$canteendishid = Db::table('canteendish')->where($searchdish)->column('id');
       $canteendishid = Db::table('canteendish')->where('dishName','like','%'.$reqname.'%')->column('id');
       for ($i = 0;$i < sizeof($canteendishid);$i++) {
           $dishnameinfo = Db::table('canteendish')->where('id',$canteendishid[$i])->value('dishName');
           $priceinfo =  Db::table('canteendish')->where('id',$canteendishid[$i])->value('price');
           $scoreinfo =  Db::table('canteendish')->where('id',$canteendishid[$i])->value('score');
           $introinfo = Db::table('canteendish')->where('id',$canteendishid[$i])->value('intro');
           $pictureinfo = Db::table('canteendishpicturelist')->where('dishId',$canteendishid[$i])->value('Url');

           $dishList[$p++]=[
               'dishId'=>strval($canteendishid[$i]),
               'dishName'=>$dishnameinfo,
               'price'=>$priceinfo,
               'score'=>strval($scoreinfo),
               'picture'=>$pictureinfo,
               'intro'=>$introinfo,
               'isCanteen'=>'1'
           ];  
       } 

       $dat['storeList'] = $storeList;
       $dat['dishList'] = $dishList;
       $ret['data'] = $dat;
       $ret['code'] = $code;
       return json_encode($ret);
   }

    //更新用户学校信息
    public function updateSchoolInfo($userid,$schoolid)
    {
        $schoolname = Db::table('school')->where('id',$schoolid)->value('name');
        if (is_null($schoolname)) {
            $ret=['code'=>'fail'];
            return json_encode($ret);
        }
        $usertype = Db::table('userinfo')->where('identity',$userid)->value('identity');
        if (is_null($usertype)) {
            $ret=['code'=>'fail'];
            return json_encode($ret);
        }

        //更新userinfo表中的信息
        $flag = Db::table('userinfo')->where('openId',$userid)->update(['school'=>$schoolname]);
        if ($flag == 0) {
            $ret=['code'=>'fail'];
            return json_encode($ret);
        }

        //如果为商家用户，更新store中的schoolid
        if ($usertype == 2) {
            $flag_mer = Db::table('store')->where('storeId',$userid)->update(['schoolId'=>$schoolid]);
            if ($flag_mer == 0) {
                $ret=['code'=>'fail'];
                return json_encode($ret);
            }
        }
        $code = 'success';
        $ret['code'] = $code;
        return json_encode($ret);
    }
}