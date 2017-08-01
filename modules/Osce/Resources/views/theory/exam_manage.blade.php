<!DOCTYPE html>
<html>
	<head>
		<script type="text/javascript" src="../js/layout.js" ></script>
		<meta charset="UTF-8">
		<title>高州市人民医院 - 住院医师轮转系统 - 考试管理</title>
		<link rel="stylesheet" type="text/css" href="../css/exam-manage.css"/>
		<script src="../js/exam-manage.js" type="text/javascript" charset="utf-8"></script>
		<link rel="stylesheet" type="text/css" href="../css/bootstrap-datetimepicker.min.css"/>
		<script type="text/javascript" src="../js/bootstrap-datetimepicker.min.js" ></script>	
	</head>
	<body>
		<div class="childTitle">考试管理</div>
		
		<div class="exam">
			<div class="exam-column clearfix choose-type">
				<span class="exam-left">类型选择：</span>
				<div class="exam-right">
					<input type="radio" name="type" id="chuke" value="1" />
					<label for="chuke">出科考试</label>
					<input type="radio" name="type" id="gonggong" value="2" />
					<label for="gonggong">公共考试</label>
					<input type="radio" name="type" id="biye" value="3" />
					<label for="biye">毕业考试</label>
				</div>
			</div>
			
			<div class="exam-column clearfix choose-depart">
				<span class="exam-left">科室选择：</span>
				<div class="exam-right">
					
				</div>
			</div>
			
			<div class="set-exam">
				<div class="set-exam-list">
					<div class="exam-column clearfix choose-question">
						<span class="exam-left">考题选择：</span>
						<div class="exam-right clearfix">
							<div>
								<select name="" class="form-control sel-question">
									<option value="">请选择考题</option>
								</select>								
							</div>
							<!--<a href="javascript:;">考题预览</a>-->
						</div>
					</div>	
					<div class="exam-column clearfix choose-time">
						<span class="exam-left">考试时间：</span>
						<div class="exam-right clearfix">
							<div>
								<input type="text" placeholder="请选择开始时间" class="form-control sel-time starttime" />
							</div>
							<span>至</span>
							<div>
								<input type="text" placeholder="请选择结束时间" class="form-control sel-time endtime" />
							</div>
						</div>
					</div>	
					<div class="exam-column clearfix choose-teacher choose-set">
						<span class="exam-left">监考设置：</span>
						<div class="exam-right clearfix">
							<ul class="choose-set-left">
							</ul>
							<i>-><br/><-</i>
							<ul class="choose-set-right">
							</ul>
						</div>
					</div>	
					<div class="exam-column clearfix choose-room choose-set">
						<span class="exam-left">考场设置：</span>
						<div class="exam-right clearfix">
							<ul class="choose-set-left">
							</ul>
							<i>-><br/><-</i>
							<ul class="choose-set-right">
							</ul>
						</div>
					</div>					
				</div>								
			</div>
			
			
			
			
			<input type="button" value="提交" class="exam-addbtn" />
			
			
		</div>
		
		
		
		
			
		
		
	</body>
</html>
