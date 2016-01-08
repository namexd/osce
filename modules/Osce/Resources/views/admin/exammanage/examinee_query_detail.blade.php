@extends('osce::admin.layouts.admin_index')
@section('only_css')
    <style>
        table tr td .form-group {
            margin-bottom: 0;
        }
        .ibox-title h5{
            margin-right: 100px;
        }
        .exam-content{
            width: 80%;
        }
        .total-score{
            text-align: center;
            font-size: 18px;
        }
        .scores{
            font-size: 22px;
            margin-left: 10px;
            margin-right: 10px;
        }
    </style>
@stop

@section('only_js')

@stop

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>考站名称: <span>肠胃炎考站</span></h5>
                <h5>考生: <span>张三</span></h5>
                <h5><span class="glyphicon glyphicon-bell"></span><span>12:00</span></h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-12 ">
                            <div class="row">

                                <div class="col-sm-12">
                                    <div class="ibox float-e-margins">


                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>序号</th>
                                                    <th class="exam-content">考核内容</th>
                                                    <th>满分</th>
                                                    <th>得分</th>
                                                    <th>操作</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td class="exam-content">血压测量</td>
                                                        <td>10</td>
                                                        <td>9</td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="total-score">
                                                考试总成绩:<span class="scores">80</span>分
                                            </div>
                                    </div>
                                </div>

                            </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script>

    </script>
@stop{{-- 内容主体区域 --}}