@extends('msc::admin.layouts.admin')
@section('only_css')
  <style rel="stylesheet" >
      /*layer*/

      .modal-header{ background-color: #f6f8fa; color: #95b8fd;}
      .modal .modal-dialog{ margin-top:5%;}
      .modal-body .clink_more{
          background-color: #408aff; color: #fff; position: absolute; right: -16px; bottom: 0;
          display: inline-table;padding: 5px 15px; border-radius: 24px 0 0 24px;
          cursor: pointer;
      }
      .modal-body textarea{ margin-top: 10px; height: 200px; }

  </style>
@stop
@section('only_js')

    <script>

        $(document).ready(function () {
            $(".clink_more").click(function(){
                $(this).prev(".eqdetail").slideToggle();
                $(this).children("span").toggle();
            })
        });
    </script>
@stop
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row table-head-style1 ">
            <div class="col-xs-2 col-md-1 head-opera">
                <button type="button" class="btn btn_pl btn-link">批量报废</button>
            </div>
            <div class="col-xs-6 col-md-2">
                <div class="input-group">
                    <input type="text" placeholder="请输入项目名称" class="input-sm form-control">
                <span class="input-group-btn">
                    <button type="button" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
                </span>
                </div>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th>#</th>
                    <th>名称</th>
                    <th>
                        <div class="btn-group Examine">
                            <button data-toggle="dropdown" class="btn-white border-white dropdown-toggle" type="button">类别 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#">开放实验室</a>
                                </li>
                                <li>
                                    <a href="#">教师</a>
                                </li>
                                <li>
                                    <a href="#">开放设备</a>
                                </li>
                                <li>
                                    <a href="#">模型设备</a>
                                </li>
                            </ul>
                        </div>
                    </th>

                    <th>编号</th>
                    <th>负责人</th>
                    <th>
                        负责人电话

                    </th>
                    <th>地址</th>
                    <th>
                        操作
                    </th>
                </tr>
                </thead>
                <tbody>


                @foreach($list as $k => $v)
                <tr>
                    <td>
                        <div class="">
                            <input type="checkbox" value="">选项{{ ($k+1) }}
                        </div>
                    </td>
                    <td>{{ $v['id'] }}</td>
                    <td>{{ $v['name'] }}</td>
                    <td>{{ $v['category'] }}</td>
                    <td>{{ $v['code'] }}</td>
                    <td>{{ $v['manager_name'] }}</td>
                    <td>{{ $v['manager_mobile'] }}</td>
                    <td>{{ $v['location'] }}</td>
                    <td>
                        <div class="opera">
                            <span class="read  span_primary">查看</span>
                            <span class="edit span_primary">编辑</span>
                            <span class="Scrap span_danger" data-toggle="modal" data-target="#myModal">报废</span>
                            <span class="Print span_primary">二维码打印</span>
                        </div>

                    </td>
                </tr>
                @endforeach



                </tbody>
            </table>
            <div class="pull-left">
                已选择2条
            </div>

            <div class="btn-group pull-right">
                <?php echo $pagination->render();?>
            </div>
        </div>

    </div>
@stop{{-- 内容主体区域 --}}



@section('layer_content')
    <form class="form-horizontal" id="Form" novalidate="novalidate">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">报废</h4>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label class="col-sm-3 control-label">设备名称：</label>
                <div class="col-sm-9">
                    <p class="form-control-static sname">设备名称：</p>
                    <div class="eqdetail" style="display:none;">
                        <p class="form-control-static">报废设备名称我我我我我我我我我我我我25个字以内</p>
                        <p class="form-control-static">报废设备名称我我我我我我我我我我我我25个字以内</p>
                        <p class="form-control-static">报废设备名称我我我我我我我我我我我我25个字以内</p>
                    </div>
                    <div class="clink_more"><span>更多</span><span style="display: none">收起</span></div>
                </div>

            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <label class="col-sm-3 control-label">报废理由：</label>
                <div class="col-sm-9">
                    <select class="form-control" name="">
                        <option>理由1</option>
                        <option>理由1</option>
                        <option>理由1</option>
                        <option>自定义理由</option>
                    </select>
                    <textarea id="ccomment" name="comment" class="form-control" required="" aria-required="true"></textarea>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success" >提交并报废</button>
        </div>
    </form>

@stop{{-- 内容主体区域 --}}