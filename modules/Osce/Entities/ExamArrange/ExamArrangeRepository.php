<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/14 0014
 * Time: 16:21
 */

namespace Modules\Osce\Entities\ExamArrange;


class ExamArrangeRepository extends AbstractExamArrange
{
    /**
     * 返回实体类的类名，带命名空间
     * @method GET
     * @url /msc/admin/resources-manager/路径名/getModel
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return ${response}
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date ${DATE} ${TIME}
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getModel()
    {
        return 'Modules\Osce\Entities\ExamArrange\ExamArrange';
    }







}