<?php

namespace app\model;

use think\Model;
use think\facade\Db;

class systemMessage extends Model {
    protected $name = 'userinfo';

    public function getSystemMessage($id)
    {
        $code = 'success';
        $activityList=[];
        $friendList=[];
        $appealList=[];

        //活动信息发送
        $k=0;
            $actid = Db::table('activityfeedback')->where('userId',$id)->column('id');
            $store =  Db::table('activityfeedback')->where('userId',$id)->column('storeId');
            array_reverse($actid);
            array_reverse($store);
            for ($j = 0;$j < sizeof($actid);$j++) {
                $activityList[$j] = [
                    'merchantNickName'=>Db::table('store')->where('storeId',$store[$j])->value('storeName'),
                    'merchantAvatarUrl'=>Db::table('store')->where('storeId',$store[$j])->value('avatarUrl'),
                    'merchantId'=>$store[$j],
                    'detail'=>Db::table('activityfeedback')->where('id',$actid[$j])->value('detail'),
                    'startTime'=>Db::table('activityfeedback')->where('id',$actid[$j])->value('startTime'),
                    'endTime'=>Db::table('activityfeedback')->where('id',$actid[$j])->value('endTime'),
                    'activityName'=>Db::table('activityfeedback')->where('id',$actid[$j])->value('activityName'),
                ];
                //更新activityfeedback表中isread状态
                //Db::table('activityfeedback')->where('id',$actid[$j])->update(['isread'=>1]);
            }

        //好友申请信息
        $friendfeedbackid_list = Db::table('friendrequestfeedback')->where('receiveId',$id)->column('id');
        for ($i = 0;$i < sizeof($friendfeedbackid_list);$i++) {
            $friendid = Db::table('friendrequestfeedback')->where('id',$friendfeedbackid_list[$i])->value('sendId');
            $friendList[$i] = [
                'friendNickName'=>Db::table('userinfo')->where('openId',$friendid)->value('nickName'),
                'friendAvatarUrl'=>Db::table('userinfo')->where('openId',$friendid)->value('avatarUrl'),
                'friendId'=>$friendid,
                'result'=>strval(Db::table('friendrequestfeedback')->where('id',$friendfeedbackid_list[$i])->value('result')),
                'time'=>Db::table('friendrequestfeedback')->where('id',$friendfeedbackid_list[$i])->value('time')
            ];
            //更新friendfeedback表中的isread状态
            //Db::table('friendrequestfeedback')->where('id',$friendfeedbackid_list[$i])->update(['isread'=>1]);
        }

        $count=count($friendList);
        for($i=0;$i<$count;$i++) {
            for($j=$i+1;$j<$count;$j++) {
                if(intval($friendList[$i]['time'])>intval($friendList[$j]['time'])) {
                    $temp=$friendList[$i];
                    $friendList[$i]=$friendList[$j];
                    $friendList[$j]=$temp;
                }
            }
        }

        //申诉反馈信息
        $appealfeedback_list = Db::table('appealfeedback')->where('userId',$id)->column('id');
        for ($i = 0;$i < sizeof($appealfeedback_list);$i++) {
            $appealList[$i] = [
                'result'=>strval(Db::table('appealfeedback')->where('id',$appealfeedback_list[$i])->value('result')),
                'time'=>Db::table('appealfeedback')->where('id',$appealfeedback_list[$i])->value('time')
            ];
            //更新appealfeedback表中的isread状态
            //Db::table('appealfeedback')->where('id',$appealfeedback_list[$i])->update(['isread'=>1]);
        }
        $count=count($appealList);
        for($i=0;$i<$count;$i++) {
            for($j=$i+1;$j<$count;$j++) {
                if(intval($appealList[$i]['time'])>intval($appealList[$j]['time'])) {
                    $temp=$appealList[$i];
                    $appealList[$i]=$appealList[$j];
                    $appealList[$j]=$temp;
                }
            }
        }


        //举报反馈信息
        /*
        $reportfeedback_list = Db::table('rpeortfeedback')->where('userId',$id)->column('id');
        for ($i = 0;$i < sizeof($reportfeedback_list);$i++) {
            $targetid = Db::table('reportfeedback')->where('id',$reportfeedback_list[$i])->value('targetId');
            $reportList[$i] = [
                'isUserOrComment'=>Db::table('reportfeedback')->where('id',$reportfeedback_list[$i])->value('isUserorComment'),
                'result'=>strval(Db::table('reportfeedback')->where('id',$reportfeedback_list[$i])->value('result')),
                'time'=>Db::table('reportfeedback')->where('id',$reportfeedback_list[$i])->value('time'),
                'nickName'=>Db::table('userinfo')->where('openId',$targetid)->value('nickName')
            ];
            //更新reportfeedback表中的isread状态
            //Db::table('reportfeedback')->where('id',$reportfeedback_list[$i])->update(['isread'=>1]);
        }
        */

        $dat['activityList'] = $activityList;
        $dat['friendList'] = $friendList;
        $dat['appealList'] = $appealList;
        //$dat['reportList'] = $reportList;
        $ret['code'] = $code;
        $ret['data'] = $dat;
        return json_encode($ret);
    }

}