@extends('osce::admin.layouts.admin_index')
@section('only_css')
    <style>
        .active{
            font-weight: 700;
        }
        .route-nav{
            margin-bottom: 30px;
        }
        .header{
            padding: 5px 15px;
        }
    </style>
@stop
@section('only_js')
@stop
@section('content')
    <div class="ibox-title route-nav">
        <ol class="breadcrumb">
            <li><a href="#">资源管理</a></li>
            <li class="route-active">人员管理</li>
        </ol>
    </div>
    <div class="ibox-title header">
        <div class="pull-left">
            <h3>人员管理</h3>
        </div>
       <div class="pull-right">
           <button type="button" class="btn btn-default">新增</button>
       </div>
    </div>
    <div class="container-fluid ibox-content">
        <ul class="nav nav-tabs">
            <li role="presentation"><a href="{{route('osce.admin.invigilator.getInvigilatorList')}}">监巡考老师</a></li>
            <li role="presentation" class="active"><a href="{{route('osce.admin.invigilator.getSpInvigilatorList')}}">SP老师</a></li>
        </ul>
        <table class="table table-striped" id="table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>账号</th>
                    <th>
                        姓名
                    </th>
                    <th>联系电话</th>
                    <th>
                        最近登录
                    </th>
                    <th>
                        操作
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>aaaaa</td>
                    <td>高老叟</td>
                    <td>1111111111</td>
                    <td>2015/12/11</td>
                    <td>
                        <a href=""><span class="read  state1 detail"><i class="fa fa-pencil-square-o"></i></span></a>
                        <a href=""><span class="read  state2"><i class="fa fa-trash-o"></i></span></a>
                    </td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>aaaaa</td>
                    <td>高老叟</td>
                    <td>1111111111</td>
                    <td>2015/12/11</td>
                    <td>
                        <a href=""><span class="read  state1 detail"><i class="fa fa-pencil-square-o"></i></span></a>
                        <a href=""><span class="read  state2"><i class="fa fa-trash-o"></i></span></a>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="row">
            <div class="pull-left">
                共2条
            </div>
            <div class="pull-right">
                <nav>
                    <ul class="pagination">
                        <li>
                            <a href="#" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <li><a href="#">1</a></li>
                        <li><a href="#">2</a></li>
                        <li><a href="#">3</a></li>
                        <li><a href="#">4</a></li>
                        <li><a href="#">5</a></li>
                        <li>
                            <a href="#" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

    </div>
@stop