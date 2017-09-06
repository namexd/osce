<?php
/*
* Copyright (c) 2017 Baidu.com, Inc. All Rights Reserved
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may not
* use this file except in compliance with the License. You may obtain a copy of
* the License at
*
* Http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
* WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
* License for the specific language governing permissions and limitations under
* the License.
*/
namespace App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Support\Facades\Cache;
/**
 * 百度语音
 */
class SpeechController extends BaseController{

    /**
     * appId
     * @var string
     */
    public $appId;

    /**
     * appId
     * @var string
     */
    public $secretKey;

    /**
     * get token url
     * @var string
     */
    public $getTokenUrl = 'https://openapi.baidu.com/oauth/2.0/token';

    /**
     * get speech url
     * @var string
     */
    public $getSpeechUrl = 'http://tsn.baidu.com/text2audio';

    /**
     * construct
     * @var string appId
     * @var string secretKey
     */
    public function __construct($appId='oSVWVSMSf9WF58sDyqOAzyhU', $secretKey='62f04f33212434e526f0883a97a2c3e0'){
        $this->appId = trim($appId);
        $this->secretKey = trim($secretKey);
    }


    /**
     * 获取token
     * @param  array   $authObj
     * @return boolean
     */
    protected function getToken()
    {
        $speech_token = Cache::get('speech_token');
        if(empty($speech_token) ||  ($speech_token->current_time+$speech_token->expires_in) < time()){
            $param['grant_type']='client_credentials';
            $param['client_id']=$this->appId;
            $param['client_secret']=$this->secretKey;

            $speech_token = json_decode($this->https_request($this->getTokenUrl,$param));
            $speech_token->current_time=time();;
            Cache::put('speech_token', $speech_token, ($speech_token->expires_in)/60);
        }
        return $speech_token->access_token;
    }

    public function getSpeechUrl($text='合成的文本，使用UTF-8编码，请注意文本长度必须小于1024字节', $lang='zh', $ctp=1){
        $data['tex'] = urlencode($text);//tex	必填	合成的文本，使用UTF-8编码，请注意文本长度必须小于1024字节
        $data['lan'] = $lang;//lan	必填	语言选择,目前只有中英文混合模式，填写固定值zh
        $data['tok'] = $this->getToken();//tok	必填	开放平台获取到的开发者access_token（见上面的“鉴权认证机制”段落）
        $data['ctp'] = $ctp;//ctp必填	客户端类型选择，web端填写固定值1
        $data['cuid'] = uniqid();//cuid 必填	用户唯一标识，用来区分用户，计算UV值。建议填写能区分用户的机器 MAC 地址或 IMEI 码，长度为60字符以内
        //$data['spd'] = 5;//spd 选填	语速，取值0-9，默认为5中语速
        //$data['pit'] = 5;//pit 选填	音调，取值0-9，默认为5中语调
        //$data['vol'] = 5;//vol 选填	音量，取值0-15，默认为5中音量
        //$data['per'] = 0;//per 选填	发音人选择, 0为普通女声，1为普通男生，3为情感合成-度逍遥，4为情感合成-度丫丫，默认为普通女声
        $string=$this->getSpeechUrl.'?';
        foreach($data as $key =>$val){
            $string.=$key.'='.$val.'&';
        }
        $string =rtrim($string,'&');
        return $string;
    }
    //https请求（支持GET和POST）
    protected function https_request($url, $data = null){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}
