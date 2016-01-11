<?php

namespace App\Repositories\Message\Contracts;

interface Message {

    /**
     * 发送消息
     *
     * @param $accept 消息接收人,一个接收人或一个接收人数组
     * @param $content 消息内容
     * @param null $title 消息标题
     * @param null $module 模块名称
     * @param int $sender 发送人
     * @param int $pid 消息父id
     * @return mixed
     *
     */
    public function send($accept,$content,$title=null,$module=null,$sender=0,$pid=0);

    /**
     * 获取消息
     * @param $id 消息编号
     * @return mixed
     */
    public function get($id);

    /**
     * 获取消息列表
     * @param $accept  接收人
     * @param null $sender 发送人
     * @param null $module 模块
     * @param int $status 状态
     * @param int $pageSize 分页条数
     * @param int $pageIndex 页码
     * @return mixed
     */
    public function messages($accept,$sender=null,$module=null,$status=1,$pageSize=10,$pageIndex=0);

    public function delete($id);

}