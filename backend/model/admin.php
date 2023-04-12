<?php

namespace app\model;

use think\Model;
use think\facade\Db;

class admin extends Model {
    protected $name = 'userinfo';
    public function getUserReport() {
        $flag=Db::table('admin_user_report')->where('isDealed',false)->value('userId');
        if(is_null($flag)) {
            $ret['code']='success';
            $ret['data']=['reportList'=>[]];
            return json_encode($ret);
        }
        $report=Db::table('admin_user_report')->where('isDealed',false)->select()->toArray();
        $n=count($report);
        for($i=0;$i<$n;$i++) {
            $pictureList=Db::table('admin_user_report_picturelist')->where('userReportId',$report[$i]['id'])->column('pictureUrl');
            $nickName=Db::table('userinfo')->where('openId',$report[$i]['userId'])->value('nickName');
            $reportList[$i]=[
                'reason'=>$report[$i]['reason'],
                'detail'=>$report[$i]['detail'],
                'id'=>$report[$i]['userId'],
                'reportId'=>strval($report[$i]['id']),
                'nickName'=>$nickName,
                'pictureList'=>$pictureList
            ];
        }
        $ret['data']=['reportList'=>$reportList];
        $ret['code']='success';

        return json_encode($ret);
    }

    public function getCommentReport() {
        $flag=Db::table('admin_comment_report')->value('commentId');
        if(is_null($flag)) {
            $ret['code']='success';
            $ret['data']=['reportList'=>[]];
            return json_encode($ret);
        }
        $report=Db::table('admin_comment_report')->select()->toArray();


        $n=count($report);
        for($i=0;$i<$n;$i++) {
            $userId=Db::table('comment')->where('id',$report[$i]['commentId'])->value('userId');
            $nickName=Db::table('userinfo')->where('openId',$userId)->value('nickName');
            $reportList[$i]=[
                'reason'=>$report[$i]['reason'],
                'detail'=>$report[$i]['detail'],
                'id'=>$userId,
                'reportId'=>strval($report[$i]['id']),
                'nickName'=>$nickName,
                'commentId'=>strval($report[$i]['commentId'])
            ];
        }
        $ret['data']=['reportList'=>$reportList];
        $ret['code']='success';

        return json_encode($ret);
    }

    public function getAppeal() {
        $flag=Db::table('admin_appeal')->value('id');
        if(is_null($flag)) {
            $ret['code']='success';
            $ret['data']=['appealList'=>[]];
            return json_encode($ret);
        }
        $appeal=Db::table('admin_appeal')->select()->toArray();

        $n=count($appeal);
        for($i=0;$i<$n;$i++) {
            $appealId=$appeal[$i]['id'];
            $appealDetail=$appeal[$i]['detail'];
            $appealReason=$appeal[$i]['reason'];
            $appealPictureList=Db::table('admin_appeal_picturelist')->where('appealId',$appealId)->column('pictureUrl');
            $userId=$appeal[$i]['userId'];
            $nickName=Db::table('userinfo')->where('openId',$userId)->value('nickName');

            $reportList=[];
            $reports=Db::table('admin_user_report')->where('userId',$userId)->select()->toArray();
            $m=count($reports);
            for($j=0;$j<$m;$j++) {
                $reportReason=$reports[$j]['reason'];
                $reportDetail=$reports[$j]['detail'];
                $reportDate=$reports[$j]['time'];
                $reportPictureList=Db::table('admin_user_report_picturelist')->where('userReportId',$reports[$j]['id'])->column('pictureUrl');
                
                $reportList[$j]=[
                    'reportReason'=>$reportReason,
                    'reportDetail'=>$reportDetail,
                    'reportDate'=>$reportDate,
                    'reportPictureList'=>$reportPictureList
                ];
            }
            $appealList[$i]=[
                'appealDetail'=>$appealDetail,
                'appealReason'=>$appealReason,
                'id'=>$userId,
                'appealId'=>strval($appealId),
                'nickName'=>$nickName,
                'reportList'=>$reportList,
                'appealPictureList'=>$appealPictureList
            ];
            unset($reportList);
        }
        $ret['code']='success';
        $ret['data']=['appealList'=>$appealList];
        return json_encode($ret);
    }

    public function dealAppeal($userId,$appealId,$result) {
        if($result) {
            Db::table('userinfo')->where('openId',$userId)->update(['status'=>$result]);
            $appealID=Db::table('admin_appeal')->where('userId',$userId)->column('id');
            Db::table('admin_appeal')->where('userId',$userId)->delete();

            $reportIds=Db::table('admin_user_report')->where('userId',$userId)->column('id');
            Db::table('admin_user_report')->where('userId',$userId)->delete();

            for($i=0;$i<count($reportIds);$i++)
                Db::table('admin_user_report_picturelist')->where('userReportId',$reportIds[$i])->delete();

            $m=count($appealID);
            for($i=0;$i<$m;$i++) 
                Db::table('admin_appeal_picturelist')->where('appealId',$appealID[$i])->delete();
        }
        Db::table('admin_appeal')->where('id',$appealId)->delete();
        Db::table('admin_appeal_picturelist')->where('appealId',$appealId)->delete();
           
        Db::table('appealfeedback')->insert([
            'userId'=>$userId,
            'result'=>($result==1?true:false),
            'time'=>strval(time()*1000)
        ]);
        $ret['code']='success';
        return json_encode($ret);
    }

    public function dealUserReport($userId,$result,$reportId) {
        if($result==1)
            Db::table('userinfo')->where('openId',$userId)->update(['status'=>!$result]);
        Db::table('admin_user_report')->where('id',$reportId)->update(['isDealed'=>true]);
        if($result==0) {
            Db::table('admin_user_report')->where('id',$reportId)->delete();
            Db::table('admin_user_report_picturelist')->where('userReportId',$reportId)->delete();
        }
        $ret['code']='success';
        return json_encode($ret);
    }

    public function dealCommentReport($commentId,$result,$reportId) {
        Db::table('admin_comment_report')->where('id',$reportId)->delete();
        $isCanteen=Db::table('comment')->where('id',$commentId)->value('isCanteen');
        $dishId=Db::table('comment')->where('id',$commentId)->value('dishId');
        if($result==1) {
            Db::table('admin_comment_report')->where('commentId',$commentId)->delete();   //同时删除所有对该评论的举报
            Db::table('comment')->where('id',$commentId)->delete();

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

            Db::table('response')->where('commentId',$commentId)->delete();
        }
        $ret['code']='success';
        return json_encode($ret);
    }

    public function getAllActivities() {
        if(Db::table('activity')->count()==0) {
            $ret['code']='success';
            $ret['data']=['activityList'=>[]];
            return json_encode($ret);
        }
        $activities=Db::table('activity')->select()->toArray();
        $n=count($activities);
        for($i=0;$i<$n;$i++) {
            $store=Db::table('store')->where('storeId',$activities[$i]['storeId'])->find();
            $activityList[$i]=[
                'activityName'=>$activities[$i]['activityName'],
                'activityId'=>strval($activities[$i]['id']),
                'picture'=>$activities[$i]['pictureUrl'],
                'slogan'=>$activities[$i]['slogan'],
                'intro'=>$activities[$i]['intro'],
                'merchantId'=>$activities[$i]['storeId'],
                'merchantNickName'=>$store['storeName'],
                'merchantAvatarUrl'=>$store['avatarUrl']
            ];
        }
        $ret['code']='success';
        $ret['data']=['activityList'=>$activityList];
        return json_encode($ret);
    }
}