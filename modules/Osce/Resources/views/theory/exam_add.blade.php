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
		
		#sj-form ul { padding: 0; margin: 0; color: #337ab7; }
		#sj-form ul li { border-bottom: 1px solid #337ab7; margin: 10px 0; position: relative; cursor: pointer;padding-right: 20px; overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
		#sj-form ul li i { font-style: normal;}
		#sj-form ul li em,#add-dx ul li em {color: #ed5565; position: absolute; right: 0; top: 2px;}
		
		
		#add-dx { display: none; overflow: hidden; padding: 20px 0;}
		#add-dx ul { padding: 0;}
		#add-dx ul li { position: relative; margin-bottom: 5px;}
		#add-dx ul li div { padding: 0 20px 0 50px;}
		#add-dx ul li i { position: absolute; left: 0; top: 6px; width: 16px; height: 16px;}
		#add-dx ul li span { position: absolute; text-align: center; left: 30px; top: 0; line-height: 34px; }
		
		.tp-list div { float: left; margin:0 20px 20px 0; position: relative; border: 1px solid #ccc; width: 200px; height: 200px; line-height: 200px; text-align: center; vertical-align: middle;}
		.tp-list div img { max-width: 98%; max-height: 98%;}
		.tp-list div i { font-size: 50px;}
		.tp-list div input { position: absolute; left: 0; top: 0; width: 100%; height: 100%; cursor: pointer; opacity: 0;}
		.tp-list div em { position: absolute; right: -10px; top: -10px; cursor: pointer; z-index: 2; font-size: 26px; color: #ed5565; background: #fff;}
		
		.addtm-xz,.addtm-pd ,.addtm-wd  { display: none;}
		
		
		
	</style>

@stop	
@section('head_js')
	<script src="{{ asset('osce/theory/js/layer-v3.1/layer.js') }}"></script>
	<script src="{{ asset('osce/theory/js/exam_add.js') }}"></script>

	<script>

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
		        <div id="sj-form" posturl="{{route('osce.theory.addQuestionList')}}" class="form-horizontal">
	                
	                <div class="form-group">
	                    <label class="col-sm-2 control-label">试卷名称：<i>*</i></label>
						<div class="col-sm-6">
							<input type="text" id="sj-name" placeholder="请填写试卷名称" class="form-control"  />
						</div>
	                </div>
	               
	                <div class="hr-line-dashed"></div>   
	                
	                <div class="form-group">
	                    <label class="col-sm-2 control-label">单选题：</label>
						<div class="col-sm-6">
							<ul class="dx-list"></ul>
							<input type="button" class="btn btn-sm btn-primary dx-btn" value="新增单选题" />
						</div>
	                </div>	 
	                <div class="hr-line-dashed"></div> 
	                
	                <div class="form-group">
	                    <label class="col-sm-2 control-label">多选题：</label>
						<div class="col-sm-6">
							<ul class="dx-list"></ul>
							<input type="button" class="btn btn-sm btn-primary dx-btn" value="新增多选题" />
						</div>
	                </div>	 
	                <div class="hr-line-dashed"></div>  
	                <div class="form-group">
	                    <label class="col-sm-2 control-label">判断题：</label>
						<div class="col-sm-6">
							<ul class="dx-list"></ul>
							<input type="button" class="btn btn-sm btn-primary dx-btn" value="新增判断题" />
						</div>
	                </div>	 
	                <div class="hr-line-dashed"></div>  
	                <div class="form-group">
	                    <label class="col-sm-2 control-label">填空题：</label>
						<div class="col-sm-6">
							<ul class="dx-list"></ul>
							<input type="button" class="btn btn-sm btn-primary dx-btn" value="新增填空题" />
						</div>
	                </div>	 
	                <div class="hr-line-dashed"></div>  
	                <div class="form-group">
	                    <label class="col-sm-2 control-label">名词解释题：</label>
						<div class="col-sm-6">
							<ul class="dx-list"></ul>
							<input type="button" class="btn btn-sm btn-primary dx-btn" value="新增名词解释题" />
						</div>
	                </div>	 
	                <div class="hr-line-dashed"></div>  
	                <div class="form-group">
	                    <label class="col-sm-2 control-label">论述题：</label>
						<div class="col-sm-6">
							<ul class="dx-list"></ul>
							<input type="button" class="btn btn-sm btn-primary dx-btn" value="新增论述题" />
						</div>
	                </div>	 
	                <div class="hr-line-dashed"></div>  
	                <div class="form-group">
	                    <label class="col-sm-2 control-label">简答题：</label>
						<div class="col-sm-6">
							<ul class="dx-list"></ul>
							<input type="button" class="btn btn-sm btn-primary dx-btn" value="新增简答题" />
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

<div class="form-horizontal addtm-con" id="add-dx">
    <div class="form-group">
        <label class="col-sm-3 control-label">题型：</label>
		<div class="col-sm-7">
			<input class="form-control type-name" value="" type="text" readonly="readonly" />
			<input class="type-value" value="" type="hidden"/>
		</div>
    </div>    
    <div class="hr-line-dashed"></div>   
    <div class="form-group">
        <label class="col-sm-3 control-label"><i>*</i> 分值：</label>
		<div class="col-sm-7">
			<input class="form-control addtm-points" type="text" placeholder="请填写分值" />
		</div>
    </div>
    <div class="hr-line-dashed"></div>   
    <div class="form-group">
        <label class="col-sm-3 control-label"><i>*</i> 题干：</label>
		<div class="col-sm-7">
			<textarea class="form-control addtm-question" placeholder="请填写题干" ></textarea>
		</div>
    </div>
    <div class="hr-line-dashed"></div>   
    <div class="form-group">
        <label class="col-sm-3 control-label">图片：</label>
		<div class="col-sm-7 tp-list" uploadurl="{{route('osce.theory.toUpload')}}" deleteurl="{{route('osce.theory.toDeleteUpload')}}">
			<div>
				<i class="fa fa-plus"></i>
				<input class="uploadimg" type="file" />
			</div>
		</div>
    </div>
    <div class="hr-line-dashed"></div>     
    <div class="form-group addtm-xz">
        <label class="col-sm-3 control-label"><i>*</i> 选项：</label>
		<div class="col-sm-7">
			<ul>
				<li>
					<i class="radio_icon"></i>
					<span class="xuhao-xx">A.</span>
					<div><input type="text" class="form-control" /></div>
					<em class="state2 fa fa-trash-o fa-2x remove-xx"></em>
				</li>
			</ul>
			<input type="button" class="btn btn-sm btn-primary add-xx" value="新增选项" />
		</div>
    </div>
    <div class="form-group addtm-pd">
        <label class="col-sm-3 control-label"><i>*</i> 选项：</label>
		<div class="col-sm-7">
			<ul>
				<li>
					<i class="radio_icon"></i>
					<div><input type="text" value="正确" readonly="readonly" class="form-control" /></div>
				</li>
				<li>
					<i class="radio_icon"></i>
					<div><input type="text" value="错误" readonly="readonly" class="form-control" /></div>
				</li>
			</ul>
		</div>
    </div>
   	
    <div class="form-group addtm-wd">
        <label class="col-sm-3 control-label">参考评分点：</label>
		<div class="col-sm-7">
			<textarea class="form-control addtm-answer" placeholder="请填写参考评分点，多个参考评分点请换行" ></textarea>
		</div>
    </div>
    <div class="hr-line-dashed"></div>    			

  
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-4">
            <button class="btn btn-success addtm-save">保存</button>
            <a class="btn btn-white addtm-close">取消</a>
        </div>
    </div>
</div>
		
@stop

