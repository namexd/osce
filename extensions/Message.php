<?php
/**
 * Created by PhpStorm.
 * User: limingyao
 * Date: 2015/5/27
 * Time: 11:59
 */

namespace Extensions;


class Message {

    /**
     * 消息内容
     * @var string
     */
    public $message;

    /**
     * 消息代码
     * @var int
     */
    public $code;

    /**
     * 消息附带对象
     * @var object
     */
    public $data;

    public function __construct($code,$data=null,$message='')
    {
        $this->code=$code;
        $this->message=$message;
        $this->data=$data;
    }

    public function toArray()
    {
        return [
            'code'=>$this->code,
            'message'=>$this->message,
            'data'=>$this->data
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public function fail($data=null,$code=-1,$message='fail')
    {
        self::setValue($data,$code,$message);
        return $this;
    }

    public function success($data=null,$code=1,$message='success')
    {
        self::setValue($data,$code,$message);
        return $this;
    }

    protected function setValue($data,$code,$message)
    {
        $this->code=$code;
        $this->message=$message;
        $this->data=$data;
    }

    public function __toString()
    {
        return $this->toJson();
    }
}