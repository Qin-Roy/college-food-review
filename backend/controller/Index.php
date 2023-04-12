<?php
namespace app\controller;

use app\BaseController;
use think\facade\Db;
use app\model\loginRegister;
use app\model\userHomePage;
use app\model\storeHomePage;
use app\model\homePage;
use app\model\storePage;
use app\model\friend;
use app\model\common;
use app\model\chat;
use app\model\canteen;
use app\model\admin;
use app\model\systemMessage;
use app\model\dynamics;
use app\model\storeCanteen;



class Index extends BaseController
{
    public function index() {}

    /************************
     * 注册登录模块
    ************************/
    //获取openid
    public function getopenid() {
        if(!isset($_POST['code'])||!strlen($_POST['code'])) {
            $ret=[
                'code'=>'fail',
                'data'=>null
            ];
            return json_encode($ret);
        }
        $code=$_POST['code'];
        
        $object=new loginRegister();
        return $object->getopenid($code);
    }
    //登录
    public function login() {
        if(!isset($_POST['openid'])||!strlen($_POST['openid'])) {
            $ret=[
                'code'=>'fail',
                'data'=>null
            ];
            return json_encode($ret);
        }
        $openid = $_POST['openid'];
        $nickName=$_POST['nickName'];
        $avatarUrl=$_POST['avatarUrl'];

        $object=new loginRegister();
        return $object->login($openid,$nickName,$avatarUrl);   
    }
    //注册
    public function register() {
        if(!isset($_POST['openid'])||!isset($_POST['identity'])||!strlen($_POST['openid'])||!strlen($_POST['identity'])) {
            $ret=[
                'code'=>'fail',
                'data'=>null
            ];
            return json_encode($ret);
        }
        $openid=$_POST['openid'];
        $identity=intval($_POST['identity']);

        $object=new loginRegister();
        return $object->register($openid,$identity);
    }

    /************************
     * 用户个人主页模块
    ************************/
    //获取收藏商家
    public function getFavouredStoreList() {
        if(!isset($_GET['id'])||!strlen($_GET['id'])) {
            $ret=[
                'code'=>'fail',
                'data'=>['favouredStoreList'=>[]]
            ];
            return json_encode($ret);
        }
        $id=$_GET['id'];

        $object=new userHomePage();
        return $object->getFavouredStoreList($id);
    }
    //获取我的评论
    public function getCommentList() {
        if(!isset($_GET['id'])||!strlen($_GET['id'])) {
            $ret=[
                'code'=>'fail',
                'data'=>['commentList'=>[]]
            ];
            return json_encode($ret);
        }
        $openid=$_GET['id'];
        $actId=$_GET['userId'];

        $object=new userHomePage();
        return $object->getCommentList($openid,$actId);
    }
    //删除评论
    public function deleteComment() {
        if(!isset($_POST['id'])||!isset($_POST['commentId'])||!strlen($_POST['id'])||!strlen($_POST['commentId'])) {
            $ret=['code'=>'fail'];
            return json_encode($ret);
        }

        $comment_id=intval($_POST['commentId']);
        $openid=$_POST['id'];

        $object=new userHomePage();
        return $object->deleteComment($comment_id,$openid);
    }
    //修改权限设置
    public function authorize() {
        if(!isset($_POST['authority'])||!isset($_POST['id'])) {
            $ret['code']='fail';
            return json_encode($ret);
        }
        $authority=$_POST['authority'];
        $openid=$_POST['id'];
        
        $object=new userHomePage();
        return $object->authorize($authority,$openid);
    }
    //获取权限设置
    public function getAuthority() {
        if(!isset($_GET['id'])) {
            $ret['code']='fail';
            $ret['data']=null;
            return json_encode($ret);
        }
        $openid=$_GET['id'];
        $object=new userHomePage();
        return $object->getAuthority($openid);
    }
    //修改个人信息
    public function editProfile() {
        $info = file_get_contents('php://input');
        $info = json_decode($info,true);

        $openid=$info['id'];
        $avatarUrl=$info['avatarUrl'];
        $nickName=$info['nickName'];
        
        $object=new userHomePage();
        return $object->editProfile($openid,$avatarUrl,$nickName);
    }
    //获取关注列表
    public function getFollowList()
    {
        if(!isset($_GET['id'])||!strlen($_GET['id'])) {
            $ret=[
                'code'=>'fail',
                'data'=>['followingList'=>[]]
            ];
            return json_encode($ret);
        }
        $reqid=$_GET['id'];
        
        $object=new userHomePage();
        return $object->getFollowList($reqid);
    }
    //获取粉丝列表
    public function getFollowerList() {
        if(!isset($_GET['id'])||!strlen($_GET['id'])) {
            $ret=[
                'code'=>'fail',
                'data'=>null
            ];
            return json_encode($ret);
        }
        $id=$_GET['id'];
        
        $object=new userHomePage();
        return $object->getFollowerList($id);
    }

    public function getFollowerNum() {
        if(!isset($_GET['id'])||!strlen($_GET['id'])) {
            $ret['code']='fail';
            return json_encode($ret);
        }
        $userId=$_GET['id'];

        $object=new userHomePage();
        return $object->getFollowerNum($userId);
    }

    /************************
     * 首页模块
    ************************/
    //获取学校列表
    public function getSchoolList() {
        $object=new homePage();
        return $object->getSchoolList();
    }
    //获取某一类别商家
    public function getStoresList() {
        if(!isset($_GET['schoolId'])||!strlen($_GET['schoolId'])||!isset($_GET['category'])||!strlen($_GET['category'])) {
            $ret=[
                'code'=>'fail',
                'data'=>['storeList'=>[]]
            ];
            return json_encode($ret);
        }
        $schoolid = $_GET['schoolId'];
        $cata = $_GET['category'];

        $object=new homePage();
        return $object->getStoresList($schoolid,$cata);
    }
    //通过学校获取商家信息
    public function getStore_Act_throughSchoolid()
    {
        if(!isset($_GET['schoolId'])||!strlen($_GET['schoolId'])) {
            $ret=[
                'code'=>'fail',
                'data'=>[
                    'schoolList'=>[],
                    'category'=>[],
                    'activityList'=>[]
                ]
            ];
            return json_encode($ret);
        }
        $reqid = intval($_GET['schoolId']);

        $object=new homePage();
        return $object->getStore_Act_throughSchoolid($reqid);
    }
    //搜索菜品
    public function searchDish()
    {
        if(!isset($_GET['keywords'])||!strlen($_GET['keywords'])) {
            $ret=[
                'code'=>'success',
                'data'=>[
                    'storeList'=>[],
                    'dishList'=>[]]
            ];
            return json_encode($ret);
        }
        $reqname = $_GET['keywords'];

        $object=new homePage();
        return $object->searchDish($reqname);
    }
    //更新首页学校信息
    public function updateSchoolInfo()
    {
        $info = file_get_contents('php://input');
        $info = json_decode($info,true);

        $userid =$info['id'];
        $schoolid = intval($info['schoolId']);

        $object=new homePage();
        return $object->updateSchoolInfo($userid,$schoolid);
    }

    /************************
     * 用户商家界面模块
    ************************/
    //获取商家详细信息
    public function getMerchant() {
        if(!isset($_GET['merchantId'])||!strlen($_GET['merchantId'])||!isset($_GET['userId'])||!strlen($_GET['userId'])) {
            $ret=[
                'code'=>'fail',
                'data'=>[
                    'nickName'=>'',
                    'isCanteen'=>'',
                    'address'=>'',
                    'tel'=>'',
                    'intro'=>'',
                    'categoryList'=>[],
                    'pictureList'=>[],
                    'dishList'=>[],
                    'activityList'=>[],
                    'isFavoured'=>[]
                ]
            ];
            return json_encode($ret);
        }
        $storeId = $_GET['merchantId'];
        $userId=$_GET['userId'];

        $object=new storePage();
        return $object->getMerchant($storeId,$userId);
    }
    //获取菜品具体信息
    public function getMerchantDishes() {
        if(!isset($_GET['dishId'])||!strlen($_GET['dishId'])||!isset($_GET['userId'])||!strlen($_GET['userId'])) {
            $ret=[
                'code'=>'fail',
                'data'=>[]
            ];
            return json_encode($ret);
        }
        $isCanteen=$_GET['isCanteen'];
        $dishId = intval($_GET['dishId']);
        $userId = $_GET['userId'];

        $object=new storePage();
        return $object->getMerchantDishes($dishId,$userId,$isCanteen);
    }

    public function getActivityInfo() {
        if(!isset($_GET['activityId'])||!strlen($_GET['activityId']))
            $ret['code']='fail';
        $activityId=intval($_GET['activityId']);
        $object= new storePage();
        return $object->getActivityInfo($activityId);
    }

    /************************
     * 商家个人主页模块
    ************************/
    //获取商家信息
    public function getStoreInfo() {
        if(!isset($_GET['id'])||!strlen($_GET['id'])) {
            $ret=[
                'code'=>'fail',
                'data'=>null
            ];
            return json_encode($ret);
        }
        $id=$_GET['id'];

        $object=new storeHomePage();
        return $object->getStoreInfo($id);
    }
    //获取分类信息
    public function getCategory() {
        $object=new storeHomePage();
        return $object->getCategory();
    }
    
    //编辑活动信息
    public function editActivity() {
        if(!isset($_POST['activityId'])||!strlen($_POST['activityId'])||!isset($_POST['activityName'])||!strlen($_POST['activityName'])||
           !isset($_POST['intro'])||!strlen($_POST['intro'])||!isset($_POST['picture'])||!strlen($_POST['picture'])||
           !isset($_POST['merchantId'])||!strlen($_POST['merchantId'])||!isset($_POST['slogan'])||!strlen($_POST['slogan'])||
           !isset($_POST['startTime'])||!strlen($_POST['startTime'])||!isset($_POST['endTime'])||!strlen($_POST['endTime'])) {
            $ret=['code'=>'fail'];
            return json_encode($ret);
        }
        $activityId=intval($_POST['activityId']);
        $activityName=$_POST['activityName'];
        $intro=$_POST['intro'];
        $slogan=$_POST['slogan'];
        $picture=$_POST['picture'];
        $merchantId=$_POST['merchantId'];
        $startTime=$_POST['startTime'];
        $endTime=$_POST['endTime'];

        $object=new storeHomePage();
        return $object->editActivity($merchantId,$activityId,$activityName,$intro,$slogan,$picture,$startTime,$endTime);
    }
    //开启新活动
    public function addActivity() {
        if(!isset($_POST['activityName'])||!strlen($_POST['activityName'])||
           !isset($_POST['intro'])||!strlen($_POST['intro'])||!isset($_POST['picture'])||!strlen($_POST['picture'])||
           !isset($_POST['merchantId'])||!strlen($_POST['merchantId'])||!isset($_POST['slogan'])||!strlen($_POST['slogan'])||
           !isset($_POST['startTime'])||!strlen($_POST['startTime'])||!isset($_POST['endTime'])||!strlen($_POST['endTime'])) {
            $ret=['code'=>'fail'];
            return json_encode($ret);
        }
        $activityName=$_POST['activityName'];
        $intro=$_POST['intro'];
        $slogan=$_POST['slogan'];
        $picture=$_POST['picture'];
        $merchantId=$_POST['merchantId'];
        $startTime=$_POST['startTime'];
        $endTime=$_POST['endTime'];

        $object=new storeHomePage();
        return $object->addActivity($merchantId,$activityName,$intro,$slogan,$picture,$startTime,$endTime);
    }
    //删除活动
    public function deleteActivity() {
        if(!isset($_GET['activityId'])||!strlen($_GET['activityId'])) {
            $ret=[
                'code'=>'fail',
                'data'=>null
            ];
            return json_encode($ret);
        }
        $activityId=intval($_GET['activityId']);

        $object=new storeHomePage();
        return $object->deleteActivity($activityId);
    }

    /************************
     * 食堂模块
    ************************/
    public function changeCanteenInfo() {
        $info = file_get_contents('php://input');
        $info = json_decode($info,true);

        $object=new canteen();
        return $object->changeCanteenInfo($info);
    }

    public function postdishUpdateRequest() {
        $info = file_get_contents('php://input');
        $info = json_decode($info,true);

        $object=new canteen();
        return $object->postdishUpdateRequest($info);
    }

    public function getdishUpdateRequest() {
        $canteenId=intval($_GET['canteenId']);
        $object=new canteen();
        return $object->getdishUpdateRequest($canteenId);
    }

    public function reviewdishUpdateRequest() {
        $info = file_get_contents('php://input');
        $info = json_decode($info,true);

        $object=new canteen();
        return $object->reviewdishUpdateRequest($info);
    }

    public function createCanteen() {
        $info = file_get_contents('php://input');
        $info = json_decode($info,true);

        $object=new canteen();
        return $object->createCanteen($info);
    }

    public function getOneCanteenInfo() {
        if(!isset($_GET['userId'])||!strlen($_GET['userId'])||!isset($_GET['canteenId'])||!strlen($_GET['canteenId'])) {
            $ret['code'] = 'fail';
            $ret['data'] = [
                'nickName'=>'',
                'isCanteen'=>'',
                'categoryList'=>[],
                'address'=>'',
                'tel'=>'',
                'intro'=>'',
                'isFavoured'=>'',
                'pictureList'=>[],
                'dishList'=>[],
                'schoolId'=>''
            ];
            return json_encode($ret);
        }
        $userid = $_GET['userId'];
        $canteenid = intval($_GET['canteenId']);

        $object=new canteen();
        return $object->getOneCanteenInfo($userid,$canteenid);
    }

    public function getAllCanteeninfo() {
        $object=new canteen();
        return $object->getAllCanteeninfo();
    }

    public function deleteCanteen() {
        if(!isset($_GET['canteenId'])||!strlen($_GET['canteenId'])) {
            $ret=['code'=>'fail'];
            return json_encode($ret);
        }
        $canteenId = intval($_GET['canteenId']);

        $object=new canteen();
        return $object->deleteCanteen($canteenId);
    }

    /************************
     * 商家食堂公共接口
    ************************/

    //修改店铺信息
    public function editInfo() {
        $info= file_get_contents('php://input');
        $info=json_decode($info, true);

        $object=new storeCanteen();
        // var_dump($info);
        return $object->editInfo($info);
    }

    //添加菜品
    public function addDish() {
        $post= file_get_contents('php://input');
        $info=json_decode($post,true);

        $object=new storeCanteen();
        return $object->addDish($info);
    }
    //编辑菜品
    public function editDish() {
        $info= file_get_contents('php://input');
        $info=json_decode($info, true);

        $object=new storeCanteen();
        return $object->editDish($info);
    }
    //删除菜品
    public function deleteDish() {
        if(!isset($_GET['dishId'])||!strlen($_GET['dishId'])||!isset($_GET['isCanteen'])||!strlen($_GET['isCanteen'])) {
            $ret=['code'=>'fail'];
            return json_encode($ret);
        }
        $dishId=intval($_GET['dishId']);
        $isCanteen=$_GET['isCanteen'];

        $object=new storeCanteen();
        return $object->deleteDish($dishId,$isCanteen);
    }

    public function getSingleDishInfo() {
        if(!isset($_GET['dishId'])||!strlen($_GET['dishId'])||!isset($_GET['isCanteen'])||!strlen($_GET['isCanteen'])) {
            $ret=['code'=>'fail'];
            $ret['data']=[
                'dishName'=>'',
                'dishId'=>'',
                'intro'=>'',
                'price'=>'',
                'pictureList'=>[]
            ];
        }
        $dishId=intval($_GET['dishId']);
        $isCanteen=$_GET['isCanteen'];

        $object=new storeCanteen();
        return $object->getSingleDishInfo($dishId,$isCanteen);
    }

    /************************
     * 通用模块
    ************************/
    public function postComment() {
        $info= file_get_contents('php://input');
        $info=json_decode($info, true);
        $dishId=intval($info['dishId']);
        $isCanteen=intval($info['isCanteen']);
        $id=$info['id'];
        $time=$info['time'];
        $content=$info['content'];
        $score=floatval($info['score']);

        $object=new common();
        return $object->postComment($dishId,$isCanteen,$id,$time,$content,$score);
    }

    public function replyToComment() {
        $info= file_get_contents('php://input');
        $info=json_decode($info, true);
        $commentId=intval($info['commentId']);
        $id=$info['fromId'];
        $toId=$info['toId'];
        $detail=$info['detail'];

        $object=new common();
        return $object->replyToComment($commentId,$id,$toId,$detail);
    }

    public function likeComment() {
        $info= file_get_contents('php://input');
        $info=json_decode($info, true);
        $commentId=intval($info['commentId']);
        $userId=$info['id'];

        $object=new common();
        return $object->likeComment($commentId,$userId);
    }

    public function favourStore() {
        if(!isset($_POST['storeId'])||!strlen($_POST['storeId'])||!isset($_POST['id'])||!strlen($_POST['id'])) {
            $ret['code']='fail';
            return json_encode($ret);
        }
        $storeId=$_POST['storeId'];
        $userId=$_POST['id'];

        $object=new common();
        return $object->favourStore($storeId,$userId);
    }

    public function cancelFavourStore() {
        if(!isset($_POST['storeId'])||!strlen($_POST['storeId'])||!isset($_POST['id'])||!strlen($_POST['id'])) {
            $ret['code']='fail';
            return json_encode($ret);
        }
        $storeId=$_POST['storeId'];
        $userId=$_POST['id'];

        $object=new common();
        return $object->cancelFavourStore($storeId,$userId);
    }

    public function followUser() {
        $info= file_get_contents('php://input');
        $info=json_decode($info, true);
        $userId=$info['id'];
        $otherUserId=$info['otherUserId'];

        $object=new common();
        return $object->followUser($userId,$otherUserId);
    }

    public function cancelFollowUser() {
        $info= file_get_contents('php://input');
        $info=json_decode($info, true);
        $userId=$info['id'];
        $otherUserId=$info['otherUserId'];

        $object=new common();
        return $object->cancelFollowUser($userId,$otherUserId);
    }

    public function reportComment() {
        $info= file_get_contents('php://input');
        $info=json_decode($info, true);
        $commentId=intval($info['commentId']);
        $time=$info['time'];
        $detail=$info['detail'];
        $reason=$info['reason'];

        $object=new common();
        return $object->reportComment($commentId,$time,$reason,$detail);
    }

    public function reportMerchant() {
        $info= file_get_contents('php://input');
        $info=json_decode($info, true);

        $storeId=$info['id'];
        $time=$info['time'];
        $reason=$info['reason'];
        $detail=$info['detail'];
        $pictureList=$info['pictureList'];

        $object=new common();
        return $object->reportMerchant($storeId,$time,$reason,$detail,$pictureList);
    }

    public function reportOtherUser() {
        $info= file_get_contents('php://input');
        $info=json_decode($info, true);

        $userId=$info['id'];
        $time=$info['time'];
        $reason=$info['reason'];
        $detail=$info['detail'];
        $pictureList=$info['pictureList'];

        $object=new common();
        return $object->reportOtherUser($userId,$time,$reason,$detail,$pictureList);
    }

    public function appeal() {
        $info= file_get_contents('php://input');
        $info=json_decode($info, true);

        $userId=$info['id'];
        $reason=$info['reason'];
        $detail=$info['detail'];
        $pictureList=$info['pictureList'];

        $object=new common();
        return $object->appeal($userId,$reason,$detail,$pictureList);
    }
    
    public function getReport() {
        if(!isset($_GET['id'])||!strlen($_GET['id'])) {
            $ret['code']='fail';
            return json_encode($ret);
        }
        $userId=$_GET['id'];

        $object=new common();
        return $object->getReport($userId);
    }

    public function adminDeleteComment() {
        if(!isset($_GET['commentId'])||!strlen($_GET['commentId'])) {
            $ret['code']='fail';
            return json_encode($ret);
        }
        $commentId=intval($_GET['commentId']);

        $object=new common();
        return $object->deleteComment($commentId);
    }

    public function getOtherUserInfo() {
        if(!isset($_GET['id'])||!strlen($_GET['id'])||!isset($_GET['otherUserId'])||!strlen($_GET['otherUserId'])) {
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
        $userId=$_GET['id'];
        $otherUserId=$_GET['otherUserId'];

        $object=new common();
        return $object->getOtherUserInfo($userId,$otherUserId);
    }

    public function getDetailedResponseList() {
        if(!isset($_GET['id'])||!strlen($_GET['id'])||!isset($_GET['commentId'])||!strlen($_GET['commentId'])) {
            $ret['code']='fail';   
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
            return json_encode($ret);
        }
        $userId=$_GET['id'];
        $commentId=intval($_GET['commentId']);

        $object=new common();
        return $object->getDetailedResponseList($userId,$commentId);
    }

    public function getUserIdentity() {
        if(!isset($_GET['id'])||!strlen($_GET['id'])) {
            $ret['code']='fail';
            $ret['data']=['identity'=>''];
            return json_encode($ret);
        }
        $userId=$_GET['id'];

        $object=new common();
        return $object->getUserIdentity($userId);
    }

    /************************
     * 管理员模块
    ************************/
    public function getUserReport() {
        $object=new admin();
        return $object->getUserReport();
    }

    public function getCommentReport() {
        $object=new admin();
        return $object->getCommentReport();
    }

    public function getAppeal() {
        $object=new admin();
        return $object->getAppeal();
    }

    public function dealAppeal() {
        if(!isset($_GET['id'])||!strlen($_GET['id'])||!isset($_GET['appealId'])||!strlen($_GET['appealId'])
         ||!isset($_GET['result'])||!strlen($_GET['result'])) {
            $ret['code']='fail';
            return json_encode($ret);
        }
        $userId=$_GET['id'];
        $appealId=intval($_GET['appealId']);
        $result=intval($_GET['result']);
        
        $object=new admin();
        return $object->dealAppeal($userId,$appealId,$result);
    }

    public function dealUserReport() {
        if(!isset($_GET['id'])||!strlen($_GET['id'])||!isset($_GET['result'])||!strlen($_GET['result'])
        ||!isset($_GET['reportId'])||!strlen($_GET['reportId'])) {
            $ret['code']='fail';
            return json_encode($ret);
        }
        $userId=$_GET['id'];
        $result=intval($_GET['result']);
        $reportId=intval($_GET['reportId']);

        $object=new admin();
        return $object->dealUserReport($userId,$result,$reportId);
    }

    public function dealCommentReport() {
        if(!isset($_GET['commentId'])||!strlen($_GET['commentId'])||!isset($_GET['reportId'])||!strlen($_GET['reportId'])
        ||!isset($_GET['result'])||!strlen($_GET['result'])) {
            $ret['code']='fail';
            return json_encode($ret);
        }
        $commentId=intval($_GET['commentId']);
        $result=intval($_GET['result']);
        $reportId=intval($_GET['reportId']);

        $object=new admin();
        return $object->dealCommentReport($commentId,$result,$reportId);
    }

    public function getAllActivities() {
        $object=new admin();
        return $object->getAllActivities();
    }

    /************************
     * 好友模块
    ************************/
    //获取好友列表
    public function getFriendList()
    {
        if(!isset($_GET['id'])||!strlen($_GET['id'])) {
            $ret=[
                'code'=>'fail',
                'data'=>null
            ];
            return json_encode($ret);
        }
        $reqid = $_GET['id'];
       
        $object=new friend();
        return $object->getFriendList($reqid);
    }
    //获取添加好友请求
    public function getFriendRequest() {
        if(!isset($_GET['id'])||!strlen($_GET['id'])) {
            $ret=[
                'code'=>'fail',
                'data'=>['requestList'=>[]]
            ];
            return json_encode($ret);
        }
        $id=$_GET['id'];

        $object=new friend();
        return $object->getFriendRequest($id);
    }
    //确定好友请求
    public function confirmFriendRequest() {
        $info= file_get_contents('php://input');
        $info=json_decode($info, true);
        $id=$info['id'];
        $friend_id=$info['otherUserId'];
        $add=intval($info['add']);
        $object=new friend();
        return $object->confirmFriendRequest($id,$friend_id,$add);
    }
    //添加好友请求
    public function addFriendRequest() {
        $info= file_get_contents('php://input');
        $info=json_decode($info, true);
        $id=$info['id'];
        $friend_id=$info['otherUserId'];
        $detail=$info['detail'];
        $remark=$info['remark'];

        $object=new friend();
        return $object->addFriendRequest($id,$friend_id,$detail,$remark);
    }

    //删除好友
    public function deleteFriend() {
        $info= file_get_contents('php://input');
        $info= json_decode($info, true);
        $id=$info['id'];
        $friend_id=$info['friendId'];

        $object=new friend();
        return $object->deleteFriend($id,$friend_id);
    }

    //修改好友备注
    public function changeFriendRemark() {
        $info= file_get_contents('php://input');
        $info= json_decode($info, true);
        $id=$info['id'];
        $friendId=$info['friendId'];
        $remark=$info['remark'];

        $object=new friend();
        return $object->changeFriendRemark($id,$friendId,$remark);
    }

    /************************
     * 聊天模块
    ************************/
    public function getChatList() 
    {  
        if(!isset($_GET['id'])||!strlen($_GET['id'])) {
            $ret['code'] = 'fail';
            $ret['data'] = ['messageList'=>[]];
            return json_encode($ret);
        }
        $targetid = $_GET['id'];

        $object=new chat();
        return $object->getChatList($targetid);
    }

    public function getChatRecord()
    {
        if(!isset($_GET['id'])||!strlen($_GET['id'])||!isset($_GET['otherUserId'])||!strlen($_GET['otherUserId'])) {
            $ret['code'] = 'fail';
            $ret['data'] = ['msgList'=>[]];
            return json_encode($ret);
        }
        $id = $_GET['id'];
        $otherUserId = $_GET['otherUserId'];

        $object=new chat();
        return $object->getChatRecord($id,$otherUserId);
    }

    public function getChatInfo()
    {
        if(!isset($_GET['id'])||!strlen($_GET['id'])||!isset($_GET['otherUserId'])||!strlen($_GET['otherUserId'])) {
            $ret['code'] = 'fail';
            $ret['data'] = [
                'otherAvatarUrl'=>'',
                'otherNickName'=>'',
                'otherRemark'=>'',
                'myNickName'=>'',
                'myAvatarUrl'=>''
            ];
            return json_encode($ret);
        }
        $targetid = $_GET['id'];
        $otherUserId = $_GET['otherUserId'];

        $object=new chat();
        return $object->getChatInfo($targetid,$otherUserId);
    }

    public function sendMessage()
    {
        $info = file_get_contents('php://input');
        $info = json_decode($info,true);

        $object=new chat();
        return $object->sendMessage($info);
    }

    public function getSystemMessage()
    {
        if(!isset($_GET['id'])||!strlen($_GET['id'])) {
            $ret=[
                'code'=>'fail',
                'data'=>[
                    'activityList'=>[],
                    'friendList'=>[],
                    'appealList'=>[],
                    'reportList'=>[]
                ]
            ];
            return json_encode($ret);
        }
        $targetid = $_GET['id'];

        $object=new systemMessage();
        return $object->getSystemMessage($targetid);
    }

    public function getAllComment()
    {
        if(!isset($_GET['id'])||!strlen($_GET['id'])) {
            $ret=[
                'code'=>'fail',
                'data'=>['commentList'=>[]]
            ];
            return json_encode($ret);
        }
        $targetid = $_GET['id'];

        $object=new dynamics();
        return $object->getfollow_selfcomment($targetid);
    }
}
