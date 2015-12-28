@extends('msc::admin.layouts.admin')
@section('only_css')

@stop

@section('only_js')

@stop

@section('content')
	<input type="hidden" id="parameter" value="" />
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row table-head-style1">
			<div class="col-xs-4 col-md-4">
				<div class="input-group">
					<label>专业名称</label>
					<input type="text" placeholder="请输入专业名称" class="input-sm form-control">
				</div>
			</div>
			<div class="col-xs-4 col-md-3">
				<div class="input-group">
					<label>状态</label>
					<select name="" id="">
						<option value="全部"></option>
						<option value="正常"></option>
						<option value="停用"></option>
					</select>
				</div>
			</div>
			<div class="col-xs-4 col-md-2">
				<div class="input-group">
					<input type="button" value="查询" class="right btn btn-blue">
				</div>
			</div>
		</div>
	</div>
@stop

@section('layer_content')
<!--新增-->
<form class="form-horizontal" id="Form1" novalidate="novalidate" action="/msc/admin/user/student-add" method="post" style="display: none;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">新增学生</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-2 control-label">姓名</label>
            <div class="col-sm-10">
                <input type="text" class="form-control name add-name" name="name" value="" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">学号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control code add-code" name="code" />
            </div>
        </div>
        <div class="form-group">
        	<div class="col-sm-offset-2" style="padding-left: 15px;">
        		<input type="radio" class="check_icon" name="gender"  value="1"/> <span style="padding-right: 40px;">男</span>
            	<input type="radio" class="check_icon" name="gender" value="2" /> <span>女</span>
        	</div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">年级</label>
            <div class="col-sm-10">
                <select class="form-control grade" id="" name="grade">
                    <option value="2015">2015</option>
                    <option value="2014">2014</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">类别</label>
            <div class="col-sm-10">
                <select class="form-control student_type" id="" name="student_type">
                    <option value="1">本科</option>
                    <option value="2">专科</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">专业</label>
            <div class="col-sm-10">
                <input type="text" class="form-control profession_name add-profession_name" name="profession_name" />
                <!--<select class="form-control profession_name" name="profession_name">
                	<option value="儿科">儿科</option>
                	<option value="设计">设计</option>
                </select>-->
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">手机号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control mobile add-mobile" name="mobile" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">证件</label>
            <div class="col-sm-4" style="padding-right: 0;">
                <select class="form-control idcard" id="" name="idcard_type">
                    <option value="0">证件类型</option>
                    <option value="1" selected="selected">身份证</option>
                    <option value="2">护照</option>
                </select>
            </div>
            <div class="col-sm-6" style="padding-left: 0;">
            	<input type="text" class="form-control card add-card" name="idcard" />
            </div>
        </div>
        <div class="form-group">
        	<div class="col-sm-offset-2">
        		<button type="button" class="btn btn-primary btn-new-add" data-dismiss="modal" aria-hidden="true">确定</button>
        	</div>
        </div>
    </div>
</form>
<!--查看-->
<form class="form-horizontal" id="Form2" novalidate="novalidate" action="" method="post" style="display: none;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">查看</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-2 control-label">姓名</label>
            <div class="col-sm-10">
                <input type="text" class="form-control look-name" value="" disabled="disabled" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">学号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control look-code" disabled="disabled" />
            </div>
        </div>
        <div class="form-group">
        	<div class="col-sm-offset-2" style="padding-left: 15px;">
        		<input type="radio" class="check_icon look-man" name="student_type"  value="1" disabled="disabled"/> <span style="padding-right: 40px;">男</span>
            	<input type="radio" class="check_icon look-woman" name="student_type" value="2" disabled="disabled" /> <span>女</span>
        	</div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">年级</label>
            <div class="col-sm-10">
                <select class="form-control look-grade" id="" disabled="disabled">
                    <option value="2015">2015</option>
                    <option value="2014">2014</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">类别</label>
            <div class="col-sm-10">
                <select class="form-control look-student_type" id="" disabled="disabled">
                    <option value="1">本科</option>
                    <option value="2">专科</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 look-control-label">专业</label>
            <div class="col-sm-10">
                <input type="text"class="form-control look-profession_name" name="profession_name" disabled="disabled" />
                <!--<select class="form-control look-profession_name" name="professional" disabled="disabled">
                	<option value="1">儿科</option>
                	<option value="2">设计</option>
                </select>-->
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">手机号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control look-mobile" disabled="disabled" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">证件</label>
            <div class="col-sm-4" style="padding-right: 0;">
                <select class="form-control look-idcard" id="" disabled="disabled">
                    <option value="0">证件类型</option>
                    <option value="1" selected="selected">身份证</option>
                    <option value="2">护照</option>
                </select>
            </div>
            <div class="col-sm-6" style="padding-left: 0;">
            	<input type="text" class="form-control look-card" disabled="disabled" />
            </div>
        </div>
        <div class="form-group">
        	<div class="col-sm-offset-2">
        		<button type="submit" class="btn btn-primary btn-new-add" data-dismiss="modal" aria-hidden="true">确定</button>
        	</div>
        </div>
    </div>
</form>
<!--编辑-->
<form class="form-horizontal" id="Form3" novalidate="novalidate" action="/msc/admin/user/student-save" method="post" style="display: none;">
	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">编辑</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-2 control-label">姓名</label>
            <div class="col-sm-10">
                <input type="text" class="form-control edit-name" value="张三" name="name" />
                <input type="hidden" class="edit-hidden-name" value="" name="id"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">学号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control edit-code" name="code"/>
            </div>
        </div>
        <div class="form-group">
        	<div class="col-sm-offset-2" style="padding-left: 15px;">
        		<input type="radio" class="check_icon edit-man" name="gender" value="1" /> <span style="padding-right: 40px;">男</span>
            	<input type="radio" class="check_icon edit-woman" name="gender" value="2" /> <span>女</span>
        	</div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">年级</label>
            <div class="col-sm-10">
                <select class="form-control edit-grade" id="" name="grade">
                    <option value="2015">2015</option>
                    <option value="2014">2014</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">类别</label>
            <div class="col-sm-10">
                <select class="form-control edit-student_type" id="" name="student_type">
                    <option value="1">本科</option>
                    <option value="2">专科</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">专业</label>
            <div class="col-sm-10">
                <input type="text" class="form-control edit-professional_name" name="professional_name" />
                <!--<select class="form-control edit-professional_name" name="professional_name">
                	<option value="1">儿科</option>
                	<option value="2">设计</option>
                </select>-->
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">手机号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control edit-mobile" name="mobile" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">证件</label>
            <div class="col-sm-4" style="padding-right: 0;">
                <select class="form-control edit-idcard_type" id="" name="idcard_type">
                    <option value="0">证件类型</option>
                    <option value="1" selected="selected">身份证</option>
                    <option value="2">护照</option>
                </select>
            </div>
            <div class="col-sm-6" style="padding-left: 0;">
            	<input type="text" class="form-control edit-idcard" name="idcard" />
            </div>
        </div>
        <div class="form-group">
        	<div class="col-sm-offset-2">
        		<button type="button" class="btn btn-primary btn-edit" data-dismiss="modal" aria-hidden="true">确定</button>
        	</div>
        </div>
    </div>
</form>
<!--禁用-->
<form class="form-horizontal" id="Form4" novalidate="novalidate" action="" method="post" style="display: none;">
	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">禁用</h4>
    </div>
    <div class="modal-body">
                    确定禁用xxx用户?
    </div>
    <div class="form-group" style="text-align: center;">
    	<button type="button" class="btn btn-primary btn-forbidden" data-dismiss="modal" aria-hidden="true">确定</button>
    </div>
</form>
<!--删除-->
<form class="form-horizontal" id="Form5" novalidate="novalidate" action="" method="post" style="display: none;">
	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">删除</h4>
    </div>
    <div class="modal-body">
                    确定删除xxx用户?
    </div>
    <div class="form-group" style="text-align: center;">
    	<button type="button" class="btn btn-primary btn-del" data-dismiss="modal" aria-hidden="true">确定</button>
    </div>
</form>
@stop