<?php

namespace app\model;

use think\Model;
use think\facade\Db;

class canteen extends Model {
    protected $name = 'userinfo';

    //用户申请菜品更新
    public function postdishUpdateRequest($info)
    {
        $code = 'success';
        //添加申请菜品信息
        $dishrequestdata = [
            'dishName'=>$info['dishName'],
            'userId'=>$info['userId'],
            'canteenId'=>intval($info['merchantId']),
            'price'=>isset($info['price']) ? $info['price'] : '',
            'intro'=>isset($info['intro']) ? $info['intro'] : ''
        ];
        Db::table('dishrequestlist')->insert($dishrequestdata);
        //获取添加记录的主键，用于图片url存储
        $targetid = Db::table('dishrequestlist')->getLastInsID();

        $picture_list = $info['pictureList'];
        //图片url存储
        for ($i = 0;$i < sizeof($picture_list);$i++)
        {
            $picturedata = [
                'dishrequestId'=>$targetid,
                'Url'=>$picture_list[$i]
            ];
            Db::table('dishrequestpicturelist')->insert($picturedata);
        }
        $ret['code'] = $code;
        return json_encode($ret);
    }

    //获取菜品更新请求
    public function getdishUpdateRequest($canteenId)
    {
        $code = 'success';
        $updateList = [];
        $i = 0;
        //游标遍历查询dishrequestlist表
        $cursor=Db::table('dishrequestlist')->where('canteenId',$canteenId)->cursor();
        foreach($cursor as $record)
        {
            $reqid = $record['id'];
            if (isset($record['intro'])) {
                $updateList[$i]['intro'] = $record['intro'];
            }
            else {
                $updateList[$i]['intro'] = '';
            }
            if (isset($record['price'])) {
                $updateList[$i]['price'] = strval($record['price']);
            }
            else {
                $updateList[$i]['price'] = '';
            }
            $pictureurlinfo = Db::table('dishrequestpicturelist')->where('dishrequestId',$reqid)->column('Url');
            $updateList[$i]['dishName'] = $record['dishName'];
            $updateList[$i]['userId'] = $record['userId'];
            $updateList[$i]['merchantId'] = strval($record['canteenId']);
            $updateList[$i]['pictureList'] = $pictureurlinfo;
            $updateList[$i]['requestid'] = strval($record['id']);

            $i++;
        }
        $dat['updateList'] = $updateList;
        $ret['data'] = $dat;
        $ret['code'] = $code;
        return json_encode($ret);
    }

    //管理员审核更新菜品请求
    public function reviewdishUpdateRequest($info)
    {
        $code = 'success';
        
        $dishName = $info['dishName'];
        $userId = $info['userId'];
        $price = $info['price'];
        $canteenid = intval($info['merchantId']);
        $picture_list = $info['pictureList'];
        $intro = $info['intro'];
        $reqid = intval($info['requestid']);

        //将该申请的菜品插入canteendish表中
        $insertdata = [
            'dishName'=>$dishName,
            'canteenId'=>$canteenid,
            'price'=>$price,
            'score'=>-1,
            'intro'=>$intro
        ];
        Db::table('canteendish')->insert($insertdata);

        //获得插入数据的id，用于canteendishpicturelist里面图片url的插入
        $targetid = Db::name('canteendish')->getLastInsID();
        //插入图片url
        for ($i = 0;$i < sizeof($picture_list);$i++) {
            $picturedata = [
                'dishId'=>$targetid,
                'Url'=>$picture_list[$i]
            ];
            Db::table('canteendishpicturelist')->insert($picturedata);
        }

        //从申请表中移除该菜品更新请求
        Db::table('dishrequestlist')->where('id',$reqid)->delete();

        $ret['code'] = $code;
        return json_encode($ret);
    }

    //创建食堂
    public function createCanteen($info)
    {
        $code = 'success';
        $picture_list = $info['pictureList'];
        $category_list = $info['categoryList'];
        $insertdata = [
            'name'=>$info['name'],
            'avatarUrl'=>$info['avatarUrl'],
            'intro'=>$info['intro'],
            'schoolId'=>intval($info['schoolId']),
            'tel'=>$info['tel'],
            'address'=>$info['address']
        ];
        Db::table('canteen')->insert($insertdata);
        //获取插入数据的主键作为canteenId
        $canteenId = Db::name('canteen')->getLastInsID();

        //得到新建key canteenid后向canteenpicturelist中插入图片数据
        for ($i = 0;$i < count($picture_list);$i++) {
            $picturedata[$i] = [
                'canteenId'=>$canteenId,
                'Url'=>$picture_list[$i]
            ];
            Db::table('canteenpicturelist')->insert($picturedata[$i]);
        }

        //向canteencategory插入类别标签数据
        for ($i = 0;$i < count($category_list);$i++) {
            $categoryname = Db::table('const_category')->where('id',intval($category_list[$i]))->value('category');
            $categoryurl = Db::table('const_category')->where('id',intval($category_list[$i]))->value('pictureUrl');
            $categorydata[$i] = [
                'canteenId'=>$canteenId,
                'name'=>$categoryname,
                'pictureUrl'=>$categoryurl
            ];
            Db::table('canteen_category')->insert($categorydata[$i]);
        }

        $dat['canteenId'] = strval($canteenId);
        $ret['data'] = $dat;
        $ret['code'] = $code;
        return json_encode($ret);
    }

    //获取食堂详细信息
    public function getOneCanteenInfo($userid,$canteenid)
    {
        $code = 'success';
        $nickName = Db::table('canteen')->where('id',$canteenid)->value('name');
        $address = Db::table('canteen')->where('id',$canteenid)->value('address');
        $tel = Db::table('canteen')->where('id',$canteenid)->value('tel');
        $intro = Db::table('canteen')->where('id',$canteenid)->value('intro');
        $schoolid = Db::table('canteen')->where('id',$canteenid)->value('schoolId');
        $avatarUrl=Db::table('canteen')->where('id',$canteenid)->value('avatarUrl');
       
        $categorylist = [];
        $categorynamelist = Db::table('canteen_category')->where('canteenId',$canteenid)->column('name');
        for ($i = 0;$i < sizeof($categorynamelist);$i++) {
            $constid = Db::table('const_category')->where('category',$categorynamelist[$i])->value('id');
            $categorylist[$i] = strval($constid);
        }
        $picturelist = Db::table('canteenpicturelist')->where('canteenId',$canteenid)->column('Url');

        $dishlist = [];
        $dishidlist = Db::table('canteendish')->where('canteenId',$canteenid)->column('id');
        for ($i = 0;$i < sizeof($dishidlist);$i++) {
            $dishNameinfo = Db::table('canteendish')->where('id',$dishidlist[$i])->value('dishName');
            $dishpriceinfo = Db::table('canteendish')->where('id',$dishidlist[$i])->value('price');
            $dishintroinfo = Db::table('canteendish')->where('id',$dishidlist[$i])->value('intro');
            $dishscoreinfo = Db::table('canteendish')->where('id',$dishidlist[$i])->value('score');
            if ($dishscoreinfo === null || !isset($dishscoreinfo)) {
                $dishscoreinfo = -1;
            }
            $dishpictureinfo = Db::table('canteendishpicturelist')->where('dishId',$dishidlist[$i])->find();
            if(is_null($dishpictureinfo))
                $dishpictureinfo['Url']='';
            $dishlist[$i] = [
                'dishId'=>strval($dishidlist[$i]),
                'dishName'=>$dishNameinfo,
                'picture'=>$dishpictureinfo['Url'],//选中其中一条
                'intro'=>$dishintroinfo,
                'price'=>$dishpriceinfo,
                'score'=>strval($dishscoreinfo)
            ];
            
        }

        $ret['code'] = $code;
        $ret['data'] = [
            'nickName'=>$nickName,
            'avatarUrl'=>$avatarUrl,
            'isCanteen'=>strval((int)1),
            'categoryList'=>$categorylist,
            'address'=>$address,
            'tel'=>$tel,
            'intro'=>$intro,
            'pictureList'=>$picturelist,
            'dishList'=>$dishlist,
            'schoolId'=>strval($schoolid)
        ];
        return json_encode($ret);
    }

    //获取全部食堂信息
    public function getAllCanteeninfo()
    {
        $code = 'success';
        $key = Db::table('canteen')->column('id');
        if(!sizeof($key)) {
            $ret['code'] = $code;
            $ret['data'] = ['canteenList'=>[]];
            return json_encode($ret);
        }
        $canteenlist = [];
        $code = 'success';
        for ($i = 0;$i < sizeof($key);$i++) {
            $nameinfo = Db::table('canteen')->where('id',$key[$i])->value('name');
            $avatarUrl = Db::table('canteen')->where('id',$key[$i])->value('avatarUrl');
            $schoolId = Db::table('canteen')->where('id',$key[$i])->value('schoolId');
            $schoolname = Db::table('school')->where('id',$schoolId)->value('name');
            $canteenlist[$i] = [
                'name'=>$nameinfo,
                'canteenId'=>strval($key[$i]),
                'avatarUrl'=>$avatarUrl,
                'school'=>$schoolname,
                'schoolId'=>strval($schoolId)
            ];
        }
        $dat['canteenList'] = $canteenlist;
        $ret['data'] = $dat;
        $ret['code'] = $code;
        return json_encode($ret);
    }

    //删除食堂
    public function deleteCanteen($canteenId)
    {
        $canteenId = intval($_GET['canteenId']);
        $code = 'success';
        $flag1 = Db::table('canteen')->where('id',$canteenId)->delete();
        $flag2 = Db::table('canteenpicturelist')->where('canteenId',$canteenId)->delete();
        $flag3 = Db::table('canteen_category')->where('canteenId',$canteenId)->delete();
        $userId=Db::table('favouredstore')->where('storeId',$canteenId)->column('userId');
        $n=count($userId);
        for($i=0;$i<$n;$i++) 
            Db::table('userinfo')->where('openId',$userId[$i])->dec()->update('favouredStoreNum');

        //删除失败
        if ($flag1 === false || $flag2 === false || $flag3 === false) {
            $ret['code']='fail';
            return json_encode($ret);
        }
        $ret['code']='success';
        return json_encode($ret);
    }
}