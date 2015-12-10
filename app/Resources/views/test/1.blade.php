<!DOCTYPE html>
<html>

<head>

    <link href="{{asset('plugins/css/bootstrap.min.css?v=3.4.0')}}" rel="stylesheet">
    <link href="{{asset('plugins/css/font-awesome.min.css?v=4.3.0')}}" rel="stylesheet">
    <link href="{{asset('plugins/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/css/style.min.css?v=3.0.0')}}" rel="stylesheet">

</head>
<style type="text/css">

    .table-head-style1{ background: #434a54;margin: 0; border-radius: 6px 6px 0 0; padding: 6px 0; }
    .selected-all{ color: #fff; font-size: 14px;;padding: 0 20px;line-height: 30px }
    .input-group-btn{ height: 30px;}
    .test1{ background: #fff;color: #1b7a8b;}
    .table thead th{ font-size: 14px;}
    .btn_pl{height:30px;min-width:80px;line-height: 16px;}
    .opera  span{ padding: 0 5px; cursor: pointer;}

    .table .state1{ color:#408aff;}
    .table .state2{ color:#ed5565;}
    .table .state3{ color:#21b9bb;}

    .modal .modal-dialog{ margin-top:10%;}
    .modal-body textarea{ margin-top: 10px; height: 200px; }

    #time_set input{ float: left; width:238px;}
    #time_set  .time_set{ display: table;  }
    #time_set  .time_set span{padding:0  10px 0 0; }
    #time_set {line-height: 36px; padding-left: 13px; margin-bottom: 0;}

    #time_set p{ margin-bottom: 0;}

    .hr-line-dashed{margin: 0 0 20px 0;}
</style>

<body>


<div class="wrapper wrapper-content animated fadeInRight">

        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-4 selected-all">

                <button type="button" class="btn btn_pl btn-link" ng-click="examine_del()">审核通过</button>
                <button type="button" class="btn btn_pl btn-link" ng-click="examine_through()">审核不通过</button>
                <!--<button type="button" class="btn btn_pl btn-link" ng-click="examine_reject()">批量未通过</button>-->
            </div>
            <div class="col-xs-6 col-md-4">

                <div class="input-group">
                    <input type="text" placeholder="请输入关键字" class="input-sm form-control">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
                    </span>
                </div>

            </div>

        </div>
        <form class="container-fluid ibox-content" id="list_form">
            <table class="table table-striped" id="table-striped">
                <thead>
                <tr>
                    <th width="100">
                        <label class="check_label all_checked">
                            <div class="check_icon"></div>
                            <input  type="checkbox"  value="">
                        </label>
                    </th>
                    <th>#</th>
                    <th>设备名称</th>
                    <th>借用时间</th>
                    <th>设备编号</th>
                    <th>借用人</th>
                    <th>借用理由</th>
                    <th>
                        <div class="btn-group Examine">
                            <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">是否续借 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#">是</a>
                                </li>
                                <li>
                                    <a href="#">否</a>
                                </li>
                            </ul>
                        </div>

                    </th>
                    <th>
                        <div class="btn-group Examine">
                            <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">设备状态 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#">已审核</a>
                                </li>
                                <li>
                                    <a href="#">未审核</a>
                                </li>
                            </ul>
                        </div>

                    </th>
                    <th>
                        操作
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <div class="">
                            <input type="checkbox" value="">选项1
                        </div>
                    </td>
                    <td>1</td>
                    <td>开放实验室a</td>
                    <td>2015.12.12-2015.12.30</td>

                    <td>51333338</td>
                    <td>审核名字</td>
                    <td>因为我摔得不得了</td>
                    <td><span class="state3">是</span></td>
                    <td>空闲</td>
                    <td>
                        <div class="opera">
                            <span class="read  state1" data-toggle="modal" data-target="#myModal">审核通过</span>

                            <span class="Scrap state2">审核不通过</span>

                        </div>

                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="">
                            <input type="checkbox" value="">选项1
                        </div>
                    </td>
                    <td>1</td>
                    <td>开放实验室a</td>
                    <td>2015.12.12-2015.12.30</td>

                    <td>51333338</td>
                    <td>审核名字</td>
                    <td>因为我摔得不得了</td>
                    <td>否</td>
                    <td><span class="state3">空闲</span></td>
                    <td>
                        <div class="opera">
                            <span class="read  state1">审核通过</span>

                            <span class="Scrap state2">审核不通过</span>

                        </div>

                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="">
                            <input type="checkbox" value="">选项1
                        </div>
                    </td>
                    <td>1</td>
                    <td>开放实验室a</td>
                    <td>2015.12.12-2015.12.30</td>

                    <td>51333338</td>
                    <td>审核名字</td>
                    <td>因为我摔得不得了</td>
                    <td>否</td>
                    <td>报废</td>
                    <td>
                        <div class="opera">
                            <span class="read  state1">审核通过</span>

                            <span class="Scrap state2">审核不通过</span>

                        </div>

                    </td>
                </tr>


                </tbody>
            </table>
            <div class="pull-left">
                已选择 <span class="length">0</span> 条
            </div>

            <div class="btn-group pull-right">
                <button type="button" class="btn btn-white"><i class="fa fa-chevron-left"></i>
                </button>
                <button class="btn btn-white">1</button>
                <button class="btn btn-white  active">2</button>
                <button class="btn btn-white">3</button>
                <button class="btn btn-white">4</button>
                <button type="button" class="btn btn-white"><i class="fa fa-chevron-right"></i>
                </button>
            </div>
        </form>
        <div ng-include="'configs.html'"></div>

</div>

</body>
</html>