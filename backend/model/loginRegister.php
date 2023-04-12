<?php

namespace app\model;
use think\Model;
use think\facade\Db;

class loginRegister extends Model {

    protected $name = 'userinfo';

    public function https_request($url, $data = null)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    public function login($openid,$nickname,$avatarUrl) {
        if(is_null(Db::table('userinfo')->where('openId', $openid)->find())) {
            $data=[
                'openId'=>$openid,
                'nickName'=>$nickname,
                'avatarUrl'=>$avatarUrl,
                'identity'=> 4,
                'status'=> true,
                'school'=>Db::table('school')->value('name'), //这里学校应设为默认值
                'followingNum'=>0,
                'followerNum'=>0,
                'favouredStoreNum'=>0,
                'authority'=>true,
            ];
            Db::table('userinfo')->insert($data);

            $data['schoolId']=strval(Db::table('school')->value('id'));
            //$data['schoolName']=$data['school'];
            $data['identity']='4';
            $data['status']='1';

            unset($data['school']);
            unset($data['openId']);
            unset($data['authority']);
            $data['id']=$openid;

            $ret['code']='success';
            $ret['data']=$data;
            return json_encode($ret);
        }
        else {
            $data=Db::table('userinfo')->where('openId', $openid)->find();
            $data['id']=$data['openId'];
            $data['schoolName']=$data['school'];
            $data['schoolId']=strval(Db::table('school')->where('name',$data['schoolName'])->value('id'));
            $data['identity']=strval($data['identity']);
            $data['status']=strval($data['status']);

            unset($data['school']);
            unset($data['openid']);
            unset($data['authority']);

            $ret['code']='success';
            $ret['data']=$data;
            return json_encode($ret);
        }
    }

    public function register($openid,$identity) {
        $tmp=Db::table('userinfo')->where('openId',$openid)->find();

        //openid给错了
        if(is_null($tmp)) {
            $ret=['code'=>'fail','data'=>['id'=>'','identity'=>'']];
            return json_encode($ret);
        }
        //identity给错了
        if($identity!=1&&$identity!=2&&$identity!=0) {
            $ret=['code'=>'fail','data'=>['id'=>'','identity'=>'']];
            return json_encode($ret);
        }

        $flag=Db::table('userinfo')->where('openId',$openid)->update(['identity'=>$identity]);
        $code='success';

        if($identity==2) {
            //管理员审核 
            // $pic=['msg'=> $_POST['pic']];
            // Db::table('admin_request')->insert($pic); 
            if(is_null(Db::table('store')->where('storeId',$openid)->find())) 
                Db::table('store')->insert([
                    'storeName'=>'',
                    'avatarUrl'=>'',
                    'intro'=>'',
                    'address'=>'',
                    'tel'=>'',
                    'storeId'=>$openid,
                    'schoolId'=>1
                ]);    
        }
        $data=[
            'id'=>$tmp['openId'],
            // 'avatarUrl'=>$tmp['avatarUrl'],
            // 'nickName'=>$tmp['nickName'],
            'identity'=>strval($tmp['identity']),
        ];
        $ret=['code'=>$code,'data'=>$data];
        return json_encode($ret);
    }

    public function getopenid($code) {
        $secret="2ddc4c550108ce770ca95dd26febf84b";
        $appid="wxdf027ab9c0f3ff07";

        $url="https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=". $secret
        ."&js_code=". $code ."&grant_type=authorization_code";

        //$jsonResult = $this->https_request($url);

        $opts = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n"));
        $context = stream_context_create($opts);
        $jsonResult=file_get_contents($url, false,$context);
 
        $resultArray1 = json_decode($jsonResult, true);
        if(isset($resultArray1['openid'])) {
            $data['id']=$resultArray1['openid'];
            $ret['code']='success';
            $ret['data']=$data;
        }
        else 
            $ret=['code'=>'fail','data'=>['id'=>'']];

        return json_encode($ret);
    }
}