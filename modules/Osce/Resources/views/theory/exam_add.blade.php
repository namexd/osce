@extends('osce::theory.base')

@section('title')
	新增试卷
@stop
@section('head_css')
	<style>
		.word-ellipsis {
		    overflow: hidden;
		    text-overflow: ellipsis;
		    white-space: nowrap;
		}		
		textarea.form-control { min-height: 200px; resize: none;}
		.form-horizontal label i,.form-horizontal label em {color: red; }
		.form-control.error {border: 1px solid #cc5965;}	
		
		#sj-form ul { padding: 0; color: #337ab7;line-height: 34px; text-decoration: underline;  }
		#sj-form ul li { position: relative; cursor: pointer;padding-right: 20px; overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
		#sj-form ul li em,#add-dx ul li em {color: #ed5565; position: absolute; right: 0; top: 8px;}
		
		
		#add-dx { display: none; overflow: hidden; padding: 20px 0;}
		#add-dx ul { padding: 0;}
		#add-dx ul li { position: relative;}
		#add-dx ul li div { padding: 0 20px 0 50px;}
		#add-dx ul li i { position: absolute; left: 0; top: 6px; width: 16px; height: 16px;}
		#add-dx ul li span { position: absolute; left: 30px; top: 0; line-height: 34px; }
		
		.tp-list div { float: left; margin:0 20px 20px 0; position: relative; border: 1px solid #ccc; width: 200px; height: 200px; line-height: 200px; text-align: center; vertical-align: middle;}
		.tp-list div img { max-width: 100%; max-height: 100%;}
		.tp-list div i { font-size: 50px;}
		.tp-list div input { position: absolute; left: 0; top: 0; width: 100%; height: 100%; cursor: pointer; opacity: 0;}
		.tp-list div em { position: absolute; right: -10px; top: -10px; cursor: pointer; z-index: 2; font-size: 26px; color: #ed5565; background: #fff;}
		
	</style>

@stop	
@section('head_js')
	<script>
		var aZimu = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N'];
		var openindex;
		var listobj;		
		$(function () {
			
			$('.dx-btn').click(function () {
				
				openindex = layer.open({
					type: 1,
					title: '新增单选题',
					closeBtn: 1,
					shadeClose:true,
					area: ['800px',$(window).height()*0.8+'px'],
					content: $('#add-dx'), //iframe的url，no代表不显示滚动条
					success:function () {
						
					}
				});				
				
				
			});
			
			$('.uploadimg').change(function () {
				var _this = $(this);
				uploadFile({
					url:"{{route('osce.theory.toUpload')}}",
					json:{
						exam_images:$(_this)[0].files[0]
					},
					fn:function (res) {
						console.log(res)
					}
					
				});
				
				
			});
			
			
		});
	</script>

@stop


@section('body')
<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6">
                <h5 class="title-label">新增试卷</h5>
            </div>
        </div>	
        <div class="ibox-content">
        	<div class="row">
		        <div id="sj-form" class="form-horizontal">
	                
	                <div class="form-group">
	                    <label class="col-sm-2 control-label">试卷名称：<i>*</i></label>
						<div class="col-sm-6">
							<input type="text" id="ja-name" placeholder="请填写试卷名称" class="form-control"  />
						</div>
	                </div>
	               
	                <div class="hr-line-dashed"></div>   
	                
	                <div class="form-group">
	                    <label class="col-sm-2 control-label">单选题：</label>
						<div class="col-sm-6">
							<ul class="dx-list">
								<li>
									<i>1、</i><span>点击下方按钮新增题目点击下方按钮新增题目点击下方按钮新增题目点击下方按钮新增题目</span>
									<em class="state2 fa fa-trash-o fa-2x"></em>
								</li>
								<li>
									<i>2、</i><span>点击下方按钮新增题目点击下方按钮新增题目点击下方按钮新增题目点击下方按钮新增题目</span>
									<em class="state2 fa fa-trash-o fa-2x"></em>
								</li>
								
							</ul>
							<input type="button" class="btn btn-sm btn-primary dx-btn" value="新增单选题" />
						</div>
	                </div>	 
	                
	                <div class="hr-line-dashed"></div>     
	              
			        <div class="form-group">
	                    <div class="col-sm-4 col-sm-offset-2">
            				<button class="btn btn-success ja-save">提交</button>
	                        <a class="btn btn-white" href="javascript:history.go(-1);">取消</a>
	                    </div>
	                </div>
				</div>
			</div>
		</div>
		
		
			
</div>		

<div class="form-horizontal" id="add-dx">
    <div class="form-group">
        <label class="col-sm-3 control-label">题型：</label>
		<div class="col-sm-7">
			<span class="form-control">单选题</span>
		</div>
    </div>    
    <div class="hr-line-dashed"></div>   
    <div class="form-group">
        <label class="col-sm-3 control-label"><i>*</i> 题干：</label>
		<div class="col-sm-7">
			<textarea placeholder="请填写题干" class="form-control" ></textarea>
		</div>
    </div>
    <div class="hr-line-dashed"></div>   
    <div class="form-group">
        <label class="col-sm-3 control-label">图片：</label>
		<div class="col-sm-7 tp-list">
			<!--<div>
				<img src="https://www.baidu.com/img/540258baibian_d5f2e53b313d9f2355f795c8a854d0d7.png" />
				<em class="fa fa-times-circle"></em>
			</div>-->
			<div>
				<i class="fa fa-plus"></i>
				<input class="uploadimg" type="file" />
			</div>
		</div>
    </div>
    <div class="hr-line-dashed"></div>     
    <div class="form-group">
        <label class="col-sm-3 control-label"><i>*</i> 选项：</label>
		<div class="col-sm-7">
			<ul>
				<li>
					<i class="radio_icon"></i>
					<span>A.</span>
					<div><input type="text" class="form-control" /></div>
					<em class="state2 fa fa-trash-o fa-2x"></em>
				</li>
			</ul>
			<input type="button" class="btn btn-sm btn-primary" value="新增选项" />
		</div>
    </div>
    <div class="hr-line-dashed"></div>     
   
  
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-4">
            <button class="btn btn-success">保存</button>
            <a class="btn btn-white addqj-close">取消</a>
        </div>
    </div>
</div>
		
@stop

