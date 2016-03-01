<?php

namespace App\Repositories;

use App\Jobs\SendReminderSms;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Overtrue\Wechat\Message;
use Overtrue\Wechat\Messages\BaseMessage;
use Overtrue\Wechat\Staff;
use Overtrue\Wechat\Broadcast;
use Queue;
use App;
use Illuminate\Http\Request;


class Common{

    /**
     * 给指定手机号发送系统短信
     *
     * @param $mobile string 手机号
     * @param $message string 消息内容
     *
     * @return void
     *
     * @version 1.0
     * @author limingyao <limingyao@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public static function sendSms($mobile,$message){
        $sender=\App::make('messages.sms');
        $sender->send($mobile,$message);
        //(new SendReminderSms($mobile,$message))->onQueue('sms');

    }

    /**
     * 上传图片(支持多张)
     * @access public
     *
     * @param $request object 请求对象
     * @param $message string 需要获取的文件的 name
     *
     * @return ['路径1'，'路径2'，'路径3'，…………]
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-15 17:51
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public static function saveImags(Request $request,$name){
        $images=$request->file($name);
        $pathLIst=[];
        foreach($images as $image)
        {
            if(!is_null($image))
            {
                $fileObject=$image->openFile();
                $content=$fileObject->fread($fileObject->getSize());
                $fileName=date('YmdHis').rand(1,99999).'.'.$image->getClientOriginalExtension();
                $savePath='/'.date('Ym').'/'.date('d').'/'.$fileName;
                $dir=Storage::disk('images');
                $result=$dir->put($savePath,$content);
                if($result)
                {
                    $pathLIst[]='/images'.$savePath;
                }
                else
                {
                    $pathLIst[]='';
                }
            }
        }
        return $pathLIst;
    }
    /**
     * 上传图片(单张)
     * @access public
     *
     * @param $request object 请求对象
     * @param $message string 需要获取的文件的 name
     *
     * @return string  路径
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-15 17:51
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public static function saveImage(Request $request,$name){
        if($request->hasFile($name) && $request->file($name)->isValid()){
            $image=$request->file($name);
            $fileMime =  $image->getMimeType();
            $allowType = ['image/png','image/gif','image/jpeg'];
            if (in_array($fileMime,$allowType)) {
                $content=file_get_contents($image->getRealPath());
                $destinationPath='/'.date('YmdHis',time()).'_'.str_random(6).'.'.$image->getClientOriginalExtension();

                $result=Storage::disk('images')->put($destinationPath,$content);

                if($result)
                {
                    $path='/images'.$destinationPath;
                }
                else
                {
                    $path=false;
                }
                return $path;
            } else {
                throw new \Exception('上传文件类型不合法');
            }
        }
        else
        {
            throw new \Exception('没有上传文件');
        }
    }


    public static function getExclData($request,$name,$cnHeader=true){

        $excl=$request->file($name);
        if(empty($excl))
        {
            throw new \Exception('没有上传文件');
        }

        // 判断上传文件正确性-added by wangjiang on 2015-12-2 18:00
        if (true != $excl->isValid())
        {
            throw new \Exception('上传文件错误');
        }

        //英文表头直接读取
        if($cnHeader==='en')
        {
            $data=Excel::load($excl -> getRealPath(),function($reader){
            },'UTF-8')->get();
        }
        else
        {
            //中文表头 或者 不要表头
            $data=Excel::load($excl -> getRealPath(),function($reader){
                $reader->noHeading();
            },'UTF-8')->get();
        }
        $ExclData=[];
        foreach($data as $items)
        {
            $sheet=[];
            //判断是否为中文表头
            if($cnHeader)
            {
                $itemsInfo=$items->first();
                if(count($itemsInfo)<=0)
                {
                    throw new \Exception('没有找到首行');
                }
                $keyList=$itemsInfo->toArray();
                foreach($items as $rowNum=>$rows)
                {
                    //如果是表头
                    if($rowNum==0)
                    {
                        foreach($rows as $headName)
                        {
                            //检查表头单元格格式
                            if(!is_string($headName)&&strlen($headName))
                            {
                                throw new \Exception('请设置表头单元格格式为文本');
                            }
                        }
                        continue;
                    }
                    $rowData=[];
                    foreach($rows as $keyIndex=>$value)
                    {
                        if(strlen($keyList[$keyIndex])==0)
                        {
                            continue;
                        }
                        $rowData[$keyList[$keyIndex]]=$value;
                    }
                    $sheet[]=$rowData;
                }
            }
            else
            {
                foreach($items as $rows)
                {
                    $sheet[]=$rows->toArray();
                }
                //$ExclShell[$items->getTitle()]=$sheet;
            }
            $ExclShell[$items->getTitle()]=$sheet;
        }

        return $ExclShell;
    }
    public function getExclExport($data){
        Excel::create('Filename', function($excel) use ($data){

            // Set the title
            $excel->setTitle('Our new awesome title');

            // Chain the setters
            $excel->setCreator('Maatwebsite')
                ->setCompany('Maatwebsite');

            // Call them separately
            $excel->setDescription('A demonstration to change the file properties');

        });
    }

    /**
     * 发送微信通知
     */
    public static function sendWeiXin($openId,$message){
        $weixinservice= App::make('wechat.staff');
        try{
            return $weixinservice->send($message)->to($openId);
        }
        catch(\Exception $ex)
        {
            dd($ex);
            throw $ex;
        }
    }

    /**
     * 创建 微信消息
     * 格式 ：
     * $msg=Common::CreateWeiXinMessage([
     */
    public static function CreateWeiXinMessage($msgArray){
        $message = Message::make('news')->items(
            function() use ($msgArray){
                $msgData=[];
                foreach($msgArray as $key=>$item)
                {
                    $itemData=Message::make('news_item')->title($item['title']);
                    foreach($item as $feild=>$value)
                    {
                        if($feild==='desc')
                        {
                            $itemData=$itemData->description($value);
                        }
                        if($feild==='url')
                        {
                            $itemData=$itemData->url($value);
                        }
                        if($feild==='picUrl')
                        {
                            $itemData=$itemData->picUrl($value);
                        }
                    }
                    $msgData[]=$itemData;
                }
                return $msgData;
            }
        );
        return $message;
    }

    /**
     * 将Excl导入产生的数组(二维) ，其中 中文的字段换成对应的英文
     * @param $data
     * @param array $nameToEn
     * @return array
     * @throws \Exception
     */
    public static function arrayChTOEn($data,$nameToEn=[]){
        if(is_string($nameToEn))
        {
            $nameToEn=config($nameToEn);
        }

        if(empty($nameToEn))
        {
            throw new \Exception('中英文字段对照配置不存在');
        }
        $newData=[];
        foreach ($data as $key=>$item) {
            $row=[];
            foreach($item as $keyName=>$keyValue){
                $row[$nameToEn[$keyName]]=$keyValue;
            }
            $newData[]=$row;
        }
        return $newData;
    }

    /**
     * 微信的发送方法
     * @param $openid
     * @param $msg
     * @return bool
     * @throws \Exception
     * @throws \Overtrue\Wechat\Exception
     */
    public static function sendMsg($openid,$msg){
        if(empty($openid))
        {
            throw new \Exception('没有找到用户的微信OpenID');
        }
        $userService = new \Overtrue\Wechat\Staff(config('wechat.app_id'), config('wechat.secret'));
        return $userService->send($msg)->to($openid);
    }

    /*
    public static function putNamesToRows ($data, $config='')
    {
        if (empty($config))
        {
            throw new \Exception('config not found error');
        }

        if (!is_array($data))
        {
            throw new \Exception('data is not array');
        }

        global $map;
        if (is_string($config))
        {
            $map = config($config);
        }

        foreach ($data as $k => $v)
        {
            foreach ($v as $_k => $_v)
            {
                $data[$k][$map[$_k]] = $_v;
                unset($data[$k][$_k]);
            }
        }

        return $data;
    }
    */
    //微信群发


    /**
     * 微信群发
     * @access public
     *
     * @param object    $message        微信消息对象（可以使用Common::CreateWeiXinMessage 创建）
     * @param array $   OpendIdArray    接收的微信opendID列表 e.g:['oI7UquKmahFwGV0l2nyu_f51nDJ4','oI7UquPKycumti7NU4HQYjVnRjPo']
     * @return void
     *
     * <pre>
     * $Message  =   Common::CreateWeiXinMessage(
     *      [
     *          [
     *              'title' =>'邀请通知',
     *              'desc'  =>'osce考试第一期邀请',
     *              'url'=>'http://www.baidu.com'
     *          ],
     *          //['title'=>'osce考试第一期邀请','url'=>'http://www.baidu.com'],
     *      ]
     * );
     *  //Common::sendWeiXin('oI7UquKmahFwGV0l2nyu_f51nDJ4',$Message);//单发
     *  Common::sendWeixinToMany($Message,['oI7UquKmahFwGV0l2nyu_f51nDJ4','oI7UquPKycumti7NU4HQYjVnRjPo']);//群发
     * </pre>
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-07 21:04
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    static public function sendWeixinToMany(BaseMessage $message,array $OpendIdArray){
        if(count($OpendIdArray)==0)
        {
            throw new \Exception('你选择的接收用户数量为0');
        }
        $broadcast = new Broadcast(config('wechat.app_id'), config('wechat.secret'));
        $result =   $broadcast->send($message)->to($OpendIdArray);
    }
}