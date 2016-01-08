@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        button.btn.btn-white.dropdown-toggle {
            border: none;
            font-weight: bolder;
        }
        .blank-panel .panel-heading {margin-left: -20px;}
        #start,#end{width: 160px;}
        .exam-name{
            line-height: 34px;
            margin-right: 20px;
        }
        .exam-list{
            width: 70%;
        }
    </style>
@stop

@section('only_js')

@stop


@section('content')
    <div class="ibox-title route-nav">
        <ol class="breadcrumb">
            <li><a href="#">考试管理</a></li>
            <li class="route-active">考生查询</li>
        </ol>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考生查询</h5>
            </div>
        </div>
        <form class="container-fluid ibox-content" id="list_form">
            <div class="panel blank-panel">


                <div  class="row" style="margin:20px 0;">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <label class="pull-left exam-name">考试名称:</label>
                        <div class="pull-left exam-list">
                            <select id="select_Category" required  class="form-control m-b" name="account">
                                <option>考试1</option>
                                <option>考试2</option>
                            </select>
                        </div>
                    </div>
                    <div class="input-group col-md-6 col-sm-6 col-xs-12">
                        <input type="text" placeholder="请输入姓名" class="input-md form-control">
                         <span class="input-group-btn">
                            <button type="button" class="btn btn-md btn-primary" id="search">搜索</button>
                        </span>
                    </div>
                </div>
                <table class="table table-striped" id="table-striped">
                    <thead>
                    <tr>
                        <th>考试名称</th>
                        <th>考生姓名</th>
                        <th>性别</th>
                        <th>学号</th>
                        <th>身份证号</th>
                        <th>联系电话</th>
                    </tr>
                    </thead>
                    <tbody>
                  @foreach($data as $item)
                     <tr>
                         <td>{{ $item->exam_name  }}</td>
                         <td>{{ $item-> student_name }}</td>
                         @if( $item-> gender==0)
                         <td>女</td>
                         @else
                         <td>男</td>
                         @endif
                         <td>{{ $item-> code }}</td>
                         <td>{{ $item-> idCard }}</td>
                         <td>{{ $item-> mobile }}</td>
                     </tr>
                   @endforeach
                    </tbody>
                </table>

                <div class="btn-group pull-right">

                </div>
            </div>
        </form>
    </div>
@stop{{-- 内容主体区域 --}}