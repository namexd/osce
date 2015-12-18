<?php

use Illuminate\Database\Seeder;

class ResourcesLabSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ResourcesClassroom =   new  \Modules\Msc\Entities\ResourcesClassroom();
        foreach($this->defaultData() as $input)
        {
            $input['manager_id']    ='';
            $ResourcesClassroom ->addLabResource($input);
        }
    }
    public function defaultData(){
        return array (
            0 =>
                array (
                    'name' => '临床技能室',
                    'code' => '3--13',
                    'location' => '新八教3楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            1 =>
                array (
                    'name' => '临床技能室',
                    'code' => '3--15',
                    'location' => '新八教3楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            2 =>
                array (
                    'name' => '临床技能室',
                    'code' => '3--17',
                    'location' => '新八教3楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            3 =>
                array (
                    'name' => '临床技能室',
                    'code' => '3--19',
                    'location' => '新八教3楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            4 =>
                array (
                    'name' => '临床技能室',
                    'code' => '3--22',
                    'location' => '新八教3楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            5 =>
                array (
                    'name' => '临床技能室',
                    'code' => '3--24',
                    'location' => '新八教3楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            6 =>
                array (
                    'name' => '临床技能室',
                    'code' => '3--26',
                    'location' => '新八教3楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            7 =>
                array (
                    'name' => '临床技能室',
                    'code' => '3--28',
                    'location' => '新八教3楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            8 =>
                array (
                    'name' => '模拟手术室',
                    'code' => '4001',
                    'location' => '新八教4楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '赵蓉',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            9 =>
                array (
                    'name' => '模拟手术室',
                    'code' => '4002',
                    'location' => '新八教4楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '赵蓉',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            10 =>
                array (
                    'name' => '基本生命支持训练室',
                    'code' => '4003',
                    'location' => '新八教4楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '赵蓉',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            11 =>
                array (
                    'name' => '监控室',
                    'code' => '4004',
                    'location' => '新八教4楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '赵蓉',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            12 =>
                array (
                    'name' => '气道控制训练室',
                    'code' => '4005',
                    'location' => '新八教4楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '赵蓉',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            13 =>
                array (
                    'name' => '创伤急救训练室',
                    'code' => '4006',
                    'location' => '新八教4楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '赵蓉',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            14 =>
                array (
                    'name' => '数字化综合训练室',
                    'code' => '4017',
                    'location' => '新八教4楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '赵蓉',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            15 =>
                array (
                    'name' => '全科医学综合训练室',
                    'code' => '4018',
                    'location' => '新八教4楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '马俊荣',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            16 =>
                array (
                    'name' => '内镜诊疗技术训练室',
                    'code' => '4019',
                    'location' => '新八教4楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '张超',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            17 =>
                array (
                    'name' => '眼科手术技能训练室',
                    'code' => '4022',
                    'location' => '新八教4楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '张超',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            18 =>
                array (
                    'name' => '基本技能训练室3',
                    'code' => '4036',
                    'location' => '新八教4楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '马俊荣',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            19 =>
                array (
                    'name' => '基本技能训练室2',
                    'code' => '4038',
                    'location' => '新八教4楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '马俊荣',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            20 =>
                array (
                    'name' => '心肺腹听诊触诊实验室',
                    'code' => '4040',
                    'location' => '新八教4楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            21 =>
                array (
                    'name' => '心肺听诊实验室',
                    'code' => '4041',
                    'location' => '新八教4楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            22 =>
                array (
                    'name' => '模拟病房1',
                    'code' => '5001',
                    'location' => '新八教5楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '张超',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            23 =>
                array (
                    'name' => '无菌技术训练室',
                    'code' => '5002',
                    'location' => '新八教5楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '张超',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            24 =>
                array (
                    'name' => '模拟病房2',
                    'code' => '5004',
                    'location' => '新八教5楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '张超',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            25 =>
                array (
                    'name' => '准备间',
                    'code' => '5013',
                    'location' => '新八教5楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '张超',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            26 =>
                array (
                    'name' => '示教室1',
                    'code' => '5014',
                    'location' => '新八教5楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '张超',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            27 =>
                array (
                    'name' => '示教室2',
                    'code' => '5015',
                    'location' => '新八教5楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '张超',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            28 =>
                array (
                    'name' => '生物力学研究室',
                    'code' => '5016',
                    'location' => '新八教5楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '骨科',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            29 =>
                array (
                    'name' => '伤口技能实验室',
                    'code' => '5017',
                    'location' => '新八教5楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '张超',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            30 =>
                array (
                    'name' => '护理学实验室',
                    'code' => '5018',
                    'location' => '新八教5楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '张超',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            31 =>
                array (
                    'name' => '儿科技能训练室',
                    'code' => '5032',
                    'location' => '新八教5楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '张超',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            32 =>
                array (
                    'name' => '老年护理实验室',
                    'code' => '5033',
                    'location' => '新八教5楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '张超',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            33 =>
                array (
                    'name' => '形体训练室',
                    'code' => '5034',
                    'location' => '新八教5楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '张超',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            34 =>
                array (
                    'name' => '妇产科技能训练室',
                    'code' => '5035',
                    'location' => '新八教5楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '张超',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            35 =>
                array (
                    'name' => '妇儿综合训练室',
                    'code' => '5036',
                    'location' => '新八教5楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '张超',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            36 =>
                array (
                    'name' => 'OT 康复实验室',
                    'code' => '6001',
                    'location' => '新八教6楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            37 =>
                array (
                    'name' => 'OT康复实验室',
                    'code' => '6002',
                    'location' => '新八教6楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            38 =>
                array (
                    'name' => 'PT康复实验室',
                    'code' => '6003',
                    'location' => '新八教6楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            39 =>
                array (
                    'name' => '眼视光学实验室',
                    'code' => '6015',
                    'location' => '新八教6楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            40 =>
                array (
                    'name' => '眼视光学实验室',
                    'code' => '6017',
                    'location' => '新八教6楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            41 =>
                array (
                    'name' => '眼视光学实验室',
                    'code' => '6019',
                    'location' => '新八教6楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            42 =>
                array (
                    'name' => '眼视光学实验室',
                    'code' => '6022',
                    'location' => '新八教6楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            43 =>
                array (
                    'name' => '营养膳食实验室',
                    'code' => '6024',
                    'location' => '新八教6楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            44 =>
                array (
                    'name' => '自主开放实验室2',
                    'code' => '6037',
                    'location' => '新八教6楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            45 =>
                array (
                    'name' => '自主开放实验室1',
                    'code' => '6038',
                    'location' => '新八教6楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            46 =>
                array (
                    'name' => '呼吸治疗实验室',
                    'code' => '6039',
                    'location' => '新八教6楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            47 =>
                array (
                    'name' => '医技影像超声检查学',
                    'code' => '6040',
                    'location' => '新八教6楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '何霄',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            48 =>
                array (
                    'name' => '教学准备室',
                    'code' => '7024',
                    'location' => '新八教7楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '赵清江',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            49 =>
                array (
                    'name' => '功能实验室',
                    'code' => '7007',
                    'location' => '新八教7楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '赵清江',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            50 =>
                array (
                    'name' => '功能实验室',
                    'code' => '7008',
                    'location' => '新八教7楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '赵清江',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            51 =>
                array (
                    'name' => '综合实验室',
                    'code' => '7006',
                    'location' => '新八教7楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '赵清江',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            52 =>
                array (
                    'name' => '仪器室1',
                    'code' => '7023',
                    'location' => '新八教7楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '赵清江',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            53 =>
                array (
                    'name' => '仪器室2',
                    'code' => '7021',
                    'location' => '新八教7楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '赵清江',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            54 =>
                array (
                    'name' => '仪器室3',
                    'code' => '7020',
                    'location' => '新八教7楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '赵清江',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            55 =>
                array (
                    'name' => '第一形态学实验室',
                    'code' => '8022',
                    'location' => '新八教8楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '熊茂琦',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            56 =>
                array (
                    'name' => '第二形态学实验室',
                    'code' => '8021',
                    'location' => '新八教8楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '熊茂琦',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            57 =>
                array (
                    'name' => '第三形态学实验室',
                    'code' => '8020',
                    'location' => '新八教8楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '熊茂琦',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            58 =>
                array (
                    'name' => '第四形态学实验室',
                    'code' => '8018',
                    'location' => '新八教8楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '熊茂琦',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            59 =>
                array (
                    'name' => '标本陈列馆',
                    'code' => '8017',
                    'location' => '新八教8楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '熊茂琦',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            60 =>
                array (
                    'name' => '标本示教室',
                    'code' => '8007',
                    'location' => '新八教8楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '熊茂琦',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            61 =>
                array (
                    'name' => '标本制作室',
                    'code' => '8008',
                    'location' => '新八教8楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '熊茂琦',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            62 =>
                array (
                    'name' => '教学准备室',
                    'code' => '8006',
                    'location' => '新八教8楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '熊茂琦',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            63 =>
                array (
                    'name' => '多人共览显微镜室',
                    'code' => '8019',
                    'location' => '新八教8楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '熊茂琦',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            64 =>
                array (
                    'name' => '虚拟仿真实验室',
                    'code' => '10003',
                    'location' => '新八教10楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '熊茂琦',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            65 =>
                array (
                    'name' => '示教室',
                    'code' => '10002',
                    'location' => '新八教10楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '熊茂琦',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            66 =>
                array (
                    'name' => '手术准备实验室',
                    'code' => 'b01027',
                    'location' => '新八教负一楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '岳中伟',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            67 =>
                array (
                    'name' => '手术实验室1',
                    'code' => 'b01025',
                    'location' => '新八教负一楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '岳中伟',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            68 =>
                array (
                    'name' => '手术实验室2',
                    'code' => 'b01023',
                    'location' => '新八教负一楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '岳中伟',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            69 =>
                array (
                    'name' => '手术实验室3',
                    'code' => 'b01021',
                    'location' => '新八教负一楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '岳中伟',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            70 =>
                array (
                    'name' => '手术实验室4',
                    'code' => 'b01019',
                    'location' => '新八教负一楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '岳中伟',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            71 =>
                array (
                    'name' => '手术实验室5',
                    'code' => 'b01017',
                    'location' => '新八教负一楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '岳中伟',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            72 =>
                array (
                    'name' => '动物准备间',
                    'code' => 'b01016',
                    'location' => '新八教负一楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '岳中伟',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            73 =>
                array (
                    'name' => '强生微创实验室1',
                    'code' => 'b01018',
                    'location' => '新八教负一楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '岳中伟',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            74 =>
                array (
                    'name' => '',
                    'code' => 'b01020',
                    'location' => '新八教负一楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' =>  '岳中伟',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            75 =>
                array (
                    'name' => '奥林巴斯微创、内镜实验室',
                    'code' => 'b01022',
                    'location' => '新八教负一楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '岳中伟',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            76 =>
                array (
                    'name' => '',
                    'code' => 'b01024',
                    'location' => '新八教负一楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' =>  '岳中伟',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            77 =>
                array (
                    'name' => '奥林巴斯模拟实验室',
                    'code' => 'b01026',
                    'location' => '新八教负一楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '岳中伟',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            78 =>
                array (
                    'name' => '男更衣间',
                    'code' => 'b01040',
                    'location' => '新八教负一楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '岳中伟',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            79 =>
                array (
                    'name' => '女更衣间',
                    'code' => 'b01041',
                    'location' => '新八教负一楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '岳中伟',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            80 =>
                array (
                    'name' => '办公室',
                    'code' => 'b01038',
                    'location' => '新八教负一楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '岳中伟',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
            81 =>
                array (
                    'name' => '视教室',
                    'code' => 'b01039',
                    'location' => '新八教负一楼',
                    'begintime' => '08:00:00',
                    'endtime' => '22:00:00',
                    'opened' => 0,
                    'manager_name' => '岳中伟',
                    'manager_mobile' => '',
                    'detail' => '',
                ),
        );
    }
}
