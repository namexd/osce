'use strict';

window.noempty = function (obj) {

	var empty = false;
	
	$(obj).find('label i').each(function () {
		var _p = $(this).parent().parent()
		var _obj = $(_p).find('.form-control');
		if ($.trim($(_obj).val())=='') {
			empty = true;
			uselayer(3,($(_obj).attr('emptymsg')?$(_obj).attr('emptymsg'):$(_obj).attr('placeholder')));
			$(_obj).focus();
			return false;
		}
	});
	if (empty) {
		return true;
	}
	return false;
};



/**
 * 公用ajax方法
 * 调用方法
 * Api.xxx({
 * 		isrepeat:$()  触发事件的元素     验证重复提交
 * 		istoken:1             是否需要验证token
 * 		islaravel:1        是否需要将参数都放在url后面传递
 * 		json:{}       参数
 * 		fn:function   成功之后的回调函数
 * })
 */

var Api = {
	
//	url:'http://guiyiguiyi.com/',
//	url:'http://localhost',
	url:'../..',

	perpage:10,
	R:function (options,url,type) {
		options.json=options.json||{};
		if (options.isrepeat) {
			var obj = $(options.isrepeat)[0];
			if (obj.bWait) {
				return false;
			}
			obj.bWait=true;
		}
		
		if (!options.istoken) {
			if (getCookie("token")) {
				options.json.token=getCookie("token");
			} else {
				removeCookie('token');
//				if (self==top) {
//					window.location.href="../pc/login.html";
//				} else {
//					window.parent.location.href = "../pc/login.html";
//				}
				return false;
			}
		} 
		Api.shade = layer.load(1, {
			shade: [0.3,'#000'] //0.1透明度的白色背景
		});
		if (options.islaravel) {
			url+='?token='+getCookie("token");
			for (var name in options.json) {
				url+='&'+name+'='+options.json[name];
			}
			options.json={};
		}

		$.ajax({
			type:type,
			url:Api.url+url,
			data:options.json,
			success:function (str) {
				layer.close(Api.shade);				
				if (options.isrepeat) {
					obj.bWait=false;
				}
				if (typeof(str)=='string'&&str!='') {console.log(str);
					var json = eval('('+str+')');
					if (json.status==99) {
						uselayer(1,json.msg,function () {
							removeCookie('token');
//							if (self==top) {
//								window.location.href="../pc/login.html";
//							} else {
//								window.parent.location.href = "../pc/login.html";
//							}							
						})
					} else {
						options.fn&&options.fn(eval('('+str+')'));
					}
				} else {
					options.fn&&options.fn(str);
				}
								
			},
			error:function (json) {
				layer.close(Api.shade);				
				if (options.isrepeat) {
					obj.bWait=false;
				}
				console.log(json);
				if (json.status==401) {
					alert("登录失效， 请重新登录。");
					removeCookie('_t');
					window.location.href = "index.html";
				} else if (json.status==500) {
					alert("服务器内部错误，请稍后再试。");		
				} else {
					var _txt = eval('('+json.responseText+')');
					alert(_txt.error);
				}				
			}
		});						
	},
	
	login:function (a) {Api.R(a,'/login','post');},  //登录的接口
	register:function (a) {Api.R(a,'/student/registe','post');},  //注册的接口
	fatherdepart:function (a) {Api.R(a,'/fatherdepart','get');},  //获取父科室
	childdepart:function (a) {Api.R(a,'/childdepart','get');},  //获取子科室
	messageHandle:function (a) {Api.R(a,'/messageHandle','post');},  //提交消息发布
	searchPInfo:function (a) {Api.R(a,'/student/search','get');},  //获取个人信息
	editpassword:function (a) {Api.R(a,'/student/editpassword','post');},//修改密码
	editPInfo:function (a) {Api.R(a,'/student/modify','post');},//修改个人信息
	AddDepart:function (a) {Api.R(a,'/depart/add','post');},//增加父科室
	messageCheck:function (a) {Api.R(a,'/messageCheck','get');},//消息查看
	messageContent:function (a) {Api.R(a,'/messageContent','get');},//消息内容获取
	
	
	searchteacher:function (a) {Api.R(a,'/teacher/search','get');},//查询所有老师
	
	//课程管理	
	electiveAddcourse:function (a) {Api.R(a,'/elective/addcourse','post');},//新增大纲外课程
	electiveModifycourse:function (a) {Api.R(a,'/elective/modifycourse','post');},//修改大纲外课程
	electiveDeletecourse:function (a) {Api.R(a,'/elective/deletecourse','get');},//删除大纲外课程
	electiveSearchall:function (a) {Api.R(a,'/elective/searchall','get');},//查询所有大纲外课程列表
	electiveCoursedata:function (a) {Api.R(a,'/elective/search-coursedata','get');},//查询课程的活动详细	
	electiveAddactivity:function (a) {Api.R(a,'/elective/addactivity','post');},//活动录入
	electiveConfirm:function (a) {Api.R(a,'/elective/confirm','get');},//教师评价确认上课
	electiveOnecourse:function (a) {Api.R(a,'/elective/search-onecourse','get');},//查询某一课程明细
	electiveOutactcourse:function (a) {Api.R(a,'/elective/search-outactcourse','get');},//查询一个学生没有填写活动的课程信息
	electiveSearchWpjact:function (a) {Api.R(a,'/elective/search-wpjact','get');},//查询一个老师没有评价活动的课程信息
	
	
	
	//排轮转表
	cycle:function (a) {Api.R(a,'/cycle/depshow','get');},//获取父科室和子科室
	makeCycle:function (a) {Api.R(a,'/cycle/makecycle','post');},//生成轮转表
	listmcycle:function (a) {Api.R(a,'/cycle/listmcycle','get');},//主轮转表列表
	delcycle:function (a) {Api.R(a,'/cycle/delcycle','get');},//删除主轮转表
	ftsdcycle:function (a) {Api.R(a,'/cycle/ftsdcycle','post');},//按时间查找轮转表
	fksdcycle:function (a) {Api.R(a,'/cycle/fksdcycle','post');},//按科室查找轮转表
	xcycle:function (a) {Api.R(a,'/cycle/xcycle','get');},//查询子科室下的列表
	showDengJi:function (a) {Api.R(a,'/cycle/showlist','post');},//显示登记列表
	confirmcycle:function (a) {Api.R(a,'/cycle/confirmcycle','post');},//确认出科

	//大纲内课程
	courseType:function (a) {Api.R(a,'/coursemain/add','get');},//获取大纲内课程类型
	coursemain:function (a) {Api.R(a,'/coursemain/insert','post');},//添加课程提交
	getCourseList:function (a) {Api.R(a,'/coursemain/index','get');},//获取课程列表
	deleteCourse:function (a) {Api.R(a,'/coursemain/delete','get');},//删除大纲内课程
	updateCourse:function (a) {Api.R(a,'/coursemain/update','post');},//更新课程
	editCourse:function (a) {Api.R(a,'/coursemain/edit','get');},//编辑课程
	showCourse:function (a) {Api.R(a,'/coursemain/show','get');},//显示单个课程信息
	
	copyCourse:function (a) {Api.R(a,'/coursemain/copycourse','post');},//复制方案
	
	
	//学生手册
	
	
	
	showManualType1:function (a) {Api.R(a,'/stucase/showtype','get');},//
	showManualType2:function (a) {Api.R(a,'/stuclinical/showtype','get');},//
	showManualType3:function (a) {Api.R(a,'/studisease/showtype','get');},//
	showManualType4:function (a) {Api.R(a,'/stuactivity/showtype','get');},//
	
	
	
	
	getManualType5:function (a) {Api.R(a,'/stusummary/add','get');},//添加学生出科小结
	
	getManualType:function (a) {Api.R(a,'/stucase/add','get');},//学生手册获取病种类型
	getManualType1:function (a) {Api.R(a,'/stucase/add','get');},//学生手册获取病种类型
	getManualType2:function (a) {Api.R(a,'/stuclinical/add','get');},//学生手册获取临床操作类型
	getManualType3:function (a) {Api.R(a,'/studisease/add','get');},//学生手册获取大病历书写类型
	getManualType4:function (a) {Api.R(a,'/stuactivity/add','get');},//学生手册获取活动记录类型

	addManual:function (a) {Api.R(a,'/stucase/insert','post');},//添加病种类型学生手册
	addManual2:function (a) {Api.R(a,'/stuclinical/insert','post');},//添加临床操作类型学生手册
	addManual3:function (a) {Api.R(a,'/studisease/insert','post');},//添加大病历书写类型学生手册
	addManual4:function (a) {Api.R(a,'/stuactivity/insert','post');},//添加活动记录类型学生手册
	addManual5:function (a) {Api.R(a,'/stusummary/insert','post');},//添加学生出科小结

	bingZhongList:function (a) {Api.R(a,'/stucase/showlist','get');},//生成病种列表
	lingChuangList:function (a) {Api.R(a,'/stuclinical/showlist','get');},//生成临床操作列表
	daBingLiList:function (a) {Api.R(a,'/studisease/showlist','get');},//生成大病历书写列表
	activityList:function (a) {Api.R(a,'/stuactivity/showlist','get');},//生成活动记录列表
	summaryList:function (a) {Api.R(a,'/stusummary/gettitle','post');},//生成出科小结列表

	deleteManual:function (a) {Api.R(a,'/stucase/delete','get');},//删除病种类型手册
	deleteManual2:function (a) {Api.R(a,'/stuclinical/delete','get');},//删除临床操作类型手册
	deleteManual3:function (a) {Api.R(a,'/studisease/delete','get');},//删除大病历书写类型手册
	deleteManual4:function (a) {Api.R(a,'/stuactivity/delete','get');},//删除活动记录类型手册
	deleteManual5:function (a) {Api.R(a,'/stusummary/delete','get');},//删除出科小结

	updateManual:function (a) {Api.R(a,'/stucase/update','post');},//更新病种类型手册
	updateManual2:function (a) {Api.R(a,'/stuclinical/update','post');},//更新临床操作类型手册
	updateManual3:function (a) {Api.R(a,'/studisease/update','post');},//更新大病历书写类型手册
	updateManual4:function (a) {Api.R(a,'/stuactivity/update','post');},//更新活动记录类型手册
	updateManual5:function (a) {Api.R(a,'/stusummary/update','post');},//更新出科小结

	showManual:function (a) {Api.R(a,'/stucase/edit','get');},//显示病种类型手册
	showManual2:function (a) {Api.R(a,'/stuclinical/edit','get');},//显示临床操作类型手册
	showManual3:function (a) {Api.R(a,'/studisease/edit','get');},//显示大病历类型手册
	showManual4:function (a) {Api.R(a,'/stuactivity/edit','get');},//显示活动记录类型手册
	showManual5:function (a) {Api.R(a,'/stusummary/edit','get');},//显示出科小结

	showStucase:function (a) {Api.R(a,'/stucase/show','get');},//显示病种类型手册
	showStuclinical:function (a) {Api.R(a,'/stuclinical/show','get');},//显示临床操作类型手册
	showStudisease:function (a) {Api.R(a,'/studisease/show','get');},//显示大病历类型手册
	showStuactivity:function (a) {Api.R(a,'/stuactivity/show','get');},//显示活动记录类型手册

	stulookcycle:function (a) {Api.R(a,'/cycle/stulookcycle','get');},//学生手册查看

	//科室管理
	addDepart:function (a) {Api.R(a,'/depart/add','post');},//新增科室
	addFather:function (a) {Api.R(a,'/depart/addfather','post');},//新增父科室
	
	managedepart:function (a) {Api.R(a,'/depart/managedepart','get');},//科室管理界面
	modifyDepart:function (a) {Api.R(a,'/depart/edit','post');},//修改科室
	deleteDepart:function (a) {Api.R(a,'/depart/delete','get');},//删除科室
	onedepart:function (a) {Api.R(a,'/depart/onedepart','get');},//查询一个科室的信息

	//教师管理
	addTeacher:function (a) {Api.R(a,'/teacher/add','post');},//新增科室
	teacherlist:function (a) {Api.R(a,'/teacher/teacherlist','get');},//科室管理界面
	modifyTeacher:function (a) {Api.R(a,'/teacher/edit','post');},//修改科室
	deleteTeacher:function (a) {Api.R(a,'/teacher/delete','get');},//删除科室
	searchTeacher:function (a) {Api.R(a,'/teacher/search','get');},//查询一个老师的信息

	//评价guanli
	getStudents:function (a) {Api.R(a,'/evaluate/getroles','get');},//得到一个科室学生的信息
	getEvaluateQuestions:function (a) {Api.R(a,'/evaluate/getinfo','get');},//得到评价的内容
	adEvaluateScores:function (a) {Api.R(a,'/evaluate/add','post');},//新增评价分数的接口
	getEvaluateList:function (a) {Api.R(a,'/evaluate/list','get');},//查询评价列表的接口
	addEvaluate:function (a) {Api.R(a,'/evaluate/addbase','post');},//新增评价接口
	getEvaluateDetail:function (a) {Api.R(a,'/evaluate/detail','get');},//查看评价详细信息接口
	getEvaluateStatistic:function (a) {Api.R(a,'/evaluate/statistics','get');},//获取图标信息接口

	//考勤
	addAttendance:function (a) {Api.R(a,'/attendance/add','post');},//新增考勤
	searchAttendance:function (a) {Api.R(a,'/attendance/search','get');},//查询考勤信息

	//考试监管
	examlist:function (a) {Api.R(a,'/exam/examlist','get');},//老师和学生查询考试列表信息
	studentlist:function (a) {Api.R(a,'/exam/studentlist','get');},//查询当前轮转科室里的学生信息
	surestudnet:function (a) {Api.R(a,'/exam/surestudent','get');},//确认学生能参加考试
	surecanshow:function (a) {Api.R(a,'/exam/surecanshow','get');},//确认学生能查看
	scorelist:function (a) {Api.R(a,'/exam/scorelist','get');},//查询学生成绩
	
	ImportTest:'/test/import',    //导入题库
	DelTest:function (a) {Api.R(a,'/test/del','get');},//删除试题
	ChooseTest:function (a) {Api.R(a,'/test/choose','get');},//选择试题
	ClassroomTest:function (a) {Api.R(a,'/test/classroom','get');},//选择教室
	AddExam:function (a) {Api.R(a,'/exam/addexam','post');},//新增考试
	Modelexamnews:function (a) {Api.R(a,'/exam/modelexamnews','get');},//查询最新的考试信息
	StartExam:function (a) {Api.R(a,'/exam/startexam','get');},//确认开始考试
	Addstudentresult:function (a) {Api.R(a,'/exam/addstudentresult','post');},//学生提交考试
	Dptexamlist:function (a) {Api.R(a,'/exam/dptexamlist','get');},//教师批卷列表
	AnswerList:function (a) {Api.R(a,'/exam/search-answerlist','get');},//查询试卷答题人的列表
	ExamDetail:function (a) {Api.R(a,'/exam/search-examdetail','get');},//查询某个学生试卷的具体信息
	ModifyResult:function (a) {Api.R(a,'/exam/modifyresult','post');},//批改试卷提交
	

	messageinform:function (a) {Api.R(a,'/messageinform','get');},//消息查看
	
	
	
	//在线资源
	
	favoriteGetList:function (a) {Api.R(a,'/favorite/getList','get');},//获取资源列表
	favoriteEdit:function (a) {Api.R(a,'/favorite/edit','post');},//新增资源列表
	favoriteDelete:function (a) {Api.R(a,'/favorite/delete','get');},//删除资源列表
	
	
	
	upload:'/upload',
	uploadimageurl:'/uploadimageurl',
	
	
	
	
	
	
	aaa:'123'
	
};



var ng = {
	url:Api.url,
	
	fourm:{
		getType:function (a,b,c) {ng.http(a,b,c,'/fourm/getType','get');},  //获取主题
		editType:function (a,b,c) {ng.http(a,b,c,'/fourm/editType','get');},  //新增修改主题
		deleteType:function (a,b,c) {ng.http(a,b,c,'/fourm/deleteType','get');},  //删除主题
		getFourmList:function (a,b,c) {ng.http(a,b,c,'/fourm/getFourmList','get');},  //查询贴子
		editFourm:function (a,b,c) {ng.http(a,b,c,'/fourm/editFourm','post');},  //新增修改贴子
		deleteFourm:function (a,b,c) {ng.http(a,b,c,'/fourm/deleteFourm','get');},  //删除贴子
		getFourmDetail:function (a,b,c) {ng.http(a,b,c,'/fourm/getFourmDetail','get');},  //获取贴子详情
		replyFourm:function (a,b,c) {ng.http(a,b,c,'/fourm/replyFourm','get');},  //回复贴子
		replyFloor:function (a,b,c) {ng.http(a,b,c,'/fourm/replyFloor','get');},  //回复楼层
		getFloorList:function (a,b,c) {ng.http(a,b,c,'/fourm/getFloorList','get');},  //获取楼层列表
		deleteFloor:function (a,b,c) {ng.http(a,b,c,'/fourm/deleteFloor','get');},  //删除楼层
		deleteMessage:function (a,b,c) {ng.http(a,b,c,'/fourm/deleteMessage','get');},  //删除楼层回复消息
		
		
		
		aa:''
	},
	
	
	
	http:function ($http,$cookies,options,url,type) {
		options.json=options.json||{};
		
		if (!options.istoken) {
			if ($cookies.get("token")) {
				options.json.token=$cookies.get("token");
			} else {
				ng.notoken();
				return false;
			}
		} 
		ng.shade = layer.load(1, {
			shade: [0.3,'#000'] //0.1透明度的白色背景
		});
		var data = options.json;
		if (type=='get') {
			data = {params:options.json};
		}
		
		$http[type](ng.url+url,data).success(function (res) {
			layer.close(ng.shade);
			console.log(res);
			options.fn&&options.fn(res);
		}).error(function (res) {
			layer.close(ng.shade);
			console.log(res);
			console.log('失败了');
		});				
	
	},
	
	initedit:function ($scope,$http,$cookies) {
		
		
		
		
		
		window.UEDITOR_HOME_URL = Api.url+Api.upload;
		
		$scope.fourm = UE.getEditor('editor', {
			toolbars: [
				[
					"undo","|",
					"redo","|",
					"link","|",
					"unlink","|",
					"bold","|",
					"italic","|",
					"underline","|",
					"forecolor","|",
					"fontfamily","|",
					"fontsize","|",
					"emotion","|",
					"simpleupload"				
				]
			],
			enableContextMenu:false,//右键菜单
			autoHeightEnabled: false,//是否自动长高,默认true
			wordCount: true, //开启字数统计 
			elementPathEnabled : false,//是否启用元素路径，默认是显示
			maximumWords: 10000, //允许的最大字符数 
			//pasteplain: true, //是否默认为纯文本粘贴。false为不使用纯文本粘贴，true为使用纯文本粘贴 
			enableAutoSave: false, //自动保存
			emotionLocalization:false,//是否开启表情本地化
			zIndex : 1
			//initialContent:'欢迎使用ueditor!',初始化编辑器的内容
			//focus:false, //初始化时，是否让编辑器获得焦点true或false
		});			
		
		
		$scope.fourm.onfocus = function () {
			setTimeout(function () {
				$('.edui-box.edui-button:last .edui-state-disabled')[0].className = 'dui-default';
//				document.getElementById('edui56_state').className = 'dui-default';
				return false;
			},500);
		}
		
		//获取所有主题
		ng.fourm.getType($http,$cookies,{
			fn:function (res) {
				$scope.fourmItems = res.data;
				if (res.data.length!=0) {
					$scope.fourmTypeId=$scope.fourmItems[0].id;
				}
				
			}
		});			
		
		
		$scope.addfourm = function ($event) {
			if (!$scope.fourmTypeId) {
				uselayer(1,'你还没有选择主题');
				return false;				
			}
			if (!$scope.fourmTitle||$scope.fourmTitle=='') {
				uselayer(3,'标题不能为空');
				$event.target.parentElement.children[1].children[1].focus();
				return false;
			}
			if (!$scope.fourm.hasContents()) {
				uselayer(3,'内容不能为空');
				$scope.fourm.focus()//聚焦
				return false;
			}
			ng.fourm.editFourm($http,$cookies,{
				json:{
					id:$scope.fourmId,
					title:$scope.fourmTitle,
					typeid:$scope.fourmTypeId,
					content:$scope.fourm.getContent(),
					desc:$scope.fourm.getContentTxt()									
				},
				fn:function (res) {
					uselayer(1,res.message,function () {
						window.location.reload();			
					});

				}
				
			});				
		};		
		$scope.toaddfourm = function (isre) {
			if (!isre) {
				$scope.fourmId=undefined;
				$scope.fourmTitle ='';
			}
			$scope.showedit = layer.open({
				zIndex:1,
				type: 1,
				title: false,
				closeBtn: 0,
				area: '700px',
				skin: 'layui-layer-nobg', //没有背景色
				shadeClose: true,
				content: $('#my-edit')
			});		
			$('.edui-box.edui-button:last .edui-button-body')[0].innerHTML+='<input type="file" class="edit-upload" accept="image/png,image/jpg,image/jpeg,image/gif" />';
		
			toupload($('.edit-upload')[0],Api.upload,{file:'Filedata'},function (res) {
				console.log(res);
				if (res.code==1) {
					$scope.fourm.setContent('<img src="'+Api.url+'/'+res.imageurl+'" /> &nbsp;',true);
				} else {
					uselayer(3,res.msg);
				}
			});			
		};
		$scope.tocancelfourm = function () {
			layer.close($scope.showedit);
		};
	},
	
	
	
	notoken:function () {
		alert('token失效');
		
	}
	
	
};








//设置cookie
function setCookie(name,value,iDay) {
	//$.cookie(name, value, {expires: iDay});
	var oDate = new Date();
	if (iDay>10) {
		oDate.setMinutes(oDate.getMinutes()+iDay);
	} else {
		oDate.setDate(oDate.getDate()+iDay);
	}
	document.cookie=name+"="+value+";expires="+oDate+";Path=/";
};
//获取cookie
function getCookie(name) { 
	var arr = document.cookie.split("; ");
	for (var i = 0 ; i<arr.length; i++) { 
		var _arr = arr[i].split("=");
		if (_arr[0]==name) { 
			return _arr[1];
		}
	}
	return "";
};
//删除cookie
function removeCookie(name) { 
	setCookie(name,"",-1);
};

//字符转编码
function strtocode(str) {
	var _arr = str.split("");
	var _arr2 = [];
	for (var i = 0; i<_arr.length; i++) {
		_arr2.push(_arr[i].charCodeAt());
	}
	var _str2 = _arr2.join("&");
	return _str2;
};

//编码转字符
function codetostr(str) {
	var arr = str.split("&");
	var _arr = [];
	for (var i = 0 ; i <arr.length; i++) {
		_arr.push(String.fromCharCode(arr[i]));
	}
	var _str = _arr.join("");
	return _str;
};

function uselayer(type,str,fn,fn2) {
	if (type=='1') {
		var ilayer = layer.alert(
			str, 
			{btn:['确定']},
			function () {
				layer.close(ilayer);
				fn&&fn();							
			});		
	} else if (type=='2') {
		var ilayer = layer.confirm(
			str,
			{btn:['确定','取消']},
			function () {
				layer.close(ilayer);
				fn&&fn();
			},
			function () {
				layer.close(ilayer);
				fn2&&fn2();
			}
		);
	} else if (type=='3') {
		var ilayer = layer.msg(str,{
		  time: 1000 //1秒关闭（如果不配置，默认是3秒）
		}, function(){
		  fn&&fn();
		});  		
		
	} else if (type=='4') {
		var ilayer = layer.alert(
			str,
			{btn:['确定']},
			function () {
				//layer.close(ilayer);
				fn&&fn();
			});
	}
};


//获取url参数
function GetQueryString(name) {//获取url参数值
   var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)","i");
   var r = window.location.search.substr(1).match(reg);
   if (r!=null) return (r[2]); return null;
}

function findurl(name) {	
	var str = window.location.href;
	var i = str.indexOf("?");
	var _i = str.indexOf("#");
	if (_i<i) {
		_i = str.length;
	}
	if (i!=-1) {
		str = str.substring(i+1,_i);
		var arr = str.split("&");
		for (var i = 0 ; i <arr.length; i++) {
			var _arr = arr[i].split("=");
			if (_arr[0]==name) {
				return  _arr[1];
			}
		}		
	}
	return "";
};

function findurltype(name) {
   var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)","i");
   var r = window.location.hash.substr(1).match(reg);
   if (r!=null) return (r[2]); return null;	
};


//改变url后面#的值
function changeurltype(name,val) {
	var _href = window.location.hash;
	if (_href=='') {
		window.location.href += '#'+name+'='+val;
	} else {
		if (_href.search(name)==-1) {
			window.location.href+='&'+name+'='+val;
		} else {
			var _h = window.location.href;
			window.location.href = _h.replace(name+'='+findurltype(name),name+'='+val);
		}
	}																			
};
//改变url后面#的值
function changepage(str) {
	var _href = window.location.href;
	if (_href.indexOf('#')==-1) {
		window.location.href = _href+'#'+str;
	} else {
		window.location.href = _href.substring(0,_href.indexOf('#'))+'#'+str;
	}																			
};


function isIELower() {
	var str = window.navigator.userAgent.toLowerCase();
	if (str.indexOf("msie 9") !=-1||str.indexOf("msie 8") !=-1||str.indexOf("msie 7") !=-1||str.indexOf("msie 6") !=-1) { 
		return true;
	} else { 
		return false;
	}
};

//给小于10的加0
function addzero(n) {
	return n<10?"0"+n:n;
};


function getstrongid(id) {
	var oDiv = id;
	if (typeof id == "string") {
		oDiv = document.getElementById(id);
	}	
	var oStrong = oDiv.getElementsByTagName("strong")[0];
	return oStrong.indexid;
};

function getstronghtml(id) {
	var oDiv = id;
	if (typeof id == "string") {
		oDiv = document.getElementById(id);
	}	
	var oStrong = oDiv.getElementsByTagName("strong")[0];
	return oStrong.innerHTML;
};

//时间戳转时间格式2016-05-01 12:01:12
function timetodate(num,n) {
	var oDate = new Date();
	oDate.setTime(num*1000);
	if (n=='1') {
		var str = oDate.getFullYear()+'-'+addzero(oDate.getMonth()+1)+'-'+addzero(oDate.getDate());
	} else {
		var str = oDate.getFullYear()+'-'+addzero(oDate.getMonth()+1)+'-'+addzero(oDate.getDate())+' '+addzero(oDate.getHours())+':'+addzero(oDate.getMinutes());
	}

	return str;
};

//时间格式转时间戳
function datetotime(dateStr){
    var newstr = dateStr.replace(/-/g,'/'); 
    var date =  new Date(newstr); 
    var time_str = date.getTime().toString();
    return time_str.substr(0,10);
};


/**
 * 公用分页方法
 * 调用方法
 * flipover(
 * 		total,        总共多少条数据
 * 		prepage,      每页多少条数据
 * 		fn,     	     回调函数， 重新取数据      
 * 		obj		   	     挂载一些参数，obj可以是容器，调用前需要初始化
*						 obj.page:请求第几页数据，
* 						 obj.showpage:false 是否显示分页
 * );
 **/
function flipover(total,prepage,fn,obj) {
	changeurltype('page',obj.page);
	if (obj.page==1) {
		obj.showpage=false;
	}
	var oPage = document.getElementById("page");
	if (total==0) {
		obj.innerHTML = '<p class="no-data">该条件下暂无数据<p>';
		oPage.style.display = "none";
		return false;
	} else {
		obj.innerHTML = '';
	}	
	if (obj.showpage) {
		return false;
	}
	if (total<=prepage) {
		oPage.style.display = "none";
		return false;
	}	
	
	obj.showpage = true;

	if (oPage.innerHTML=='') {
		oPage.innerHTML = 
			'<input type="button" value="首页" class="page_first" />'
			+'<input type="button" value="上一页" class="page_prev" />'
			+'<input type="button" value="..." class="page_jump_prev" />'
			+'<div><ul class="clearfix page_list"></ul></div>'
			+'<input type="button" value="..." class="page_jump_next" />'
			+'<input type="button" value="下一页" class="page_next" />'
			+'<input type="button" value="末页" class="page_last" />';		
		oPage.className = 'clearfix page';
	}
	var oUl = oPage.getElementsByTagName("ul")[0];
	var oFirst = $(oPage).find('.page_first')[0];
	var oLast = $(oPage).find('.page_last')[0];
	var oPrev = $(oPage).find('.page_prev')[0];
	var oNext = $(oPage).find('.page_next')[0];
	var oJumpPrev = $(oPage).find('.page_jump_prev')[0];
	var oJumpNext = $(oPage).find('.page_jump_next')[0];
	
	var iW = 42;
	
	var iLeft = 0;
	
	oUl.innerHTML = '';
	oUl.style.left = 0;	
//	oJumpPrev.style.visibility = "hidden";
//	oJumpNext.style.visibility = "hidden";
	oUl.style.width = total*iW+"px";	
	var nPage = Math.ceil(total/prepage);
	for (var i = 0 ; i < nPage; i++) {
		var oLi = document.createElement("li");
		oLi.innerHTML = i+1;
		oUl.appendChild(oLi);
	}
	if (obj.page>nPage) {
		obj.page = nPage;
		obj.showpage = false;
		fn&&fn();
		return false;
	}
	oUl.children[obj.page-1].className = "active";
	var iNow = obj.page-1;
	var aLi = oUl.getElementsByTagName("li");
	aLi[aLi.length-1].style.borderRight = '1px solid #ddd';
	if (nPage>5) {
		oFirst.style.visibility = "visible";
		oLast.style.visibility = "visible";
		oPrev.style.visibility = "visible";
		oNext.style.visibility = "visible";
		oJumpPrev.style.visibility = "visible";
		oJumpNext.style.visibility = "visible";
		
		
		oJumpNext.onclick = function () {
			iNow+=5;
			if (iNow > aLi.length-1) {
				iNow = aLi.length-1;
			}
			tab();
		};
	} else {
		oFirst.style.visibility = "hidden";
		oLast.style.visibility = "hidden";
		oPrev.style.visibility = "hidden";
		oNext.style.visibility = "hidden";
		oJumpPrev.style.visibility = "hidden";
		oJumpNext.style.visibility = "hidden";		
	}
	oJumpPrev.onclick = function () {
		iNow-=5;
		if (iNow <0) {
			iNow = 0;
		}
		tab();
	};	
	oFirst.onclick = function () {
		iNow = 0;
		tab();
	};
	oLast.onclick = function () {
		iNow = aLi.length-1;
		tab();
	};
	for (var i = 0 ; i < aLi.length; i++) {
		aLi[i].index = i;
		aLi[i].onclick = function () {
			iNow = this.index;
			tab();
		}
	}
	oNext.onclick = function () {
		iNow++;
		if(iNow == aLi.length) {
			iNow = aLi.length-1;						
		}											
		tab();
	};
	oPrev.onclick = function () {
		iNow--;
		if(iNow <0) {
			iNow=0;
		}
		tab();
	};
	function tab(first) {
		if (iNow<3) {
			oJumpPrev.disabled="disabled";
		} else {
			if (nPage>5) {
				oJumpPrev.removeAttribute('disabled');
			}			
		}
		if (iNow>aLi.length-4) {
			oJumpNext.disabled="disabled";
		} else {
			if (nPage>5) {
				oJumpNext.removeAttribute('disabled');
			}
		}
		if(iNow == 0) {
			oPrev.disabled="disabled";	
			oFirst.disabled="disabled";					
		} else {
			oPrev.removeAttribute('disabled');
			oFirst.removeAttribute('disabled');	
		}
		if(iNow == aLi.length-1) {
			oNext.disabled="disabled";	
			oLast.disabled="disabled";					
		} else {
			oNext.removeAttribute('disabled');
			oLast.removeAttribute('disabled');	
		}
		iLeft = (-iNow+2)*iW;
		if (iLeft<-(aLi.length-5)*iW) {
			iLeft=-(aLi.length-5)*iW;
		}
		if (iLeft>0) {
			iLeft=0;
		}
		for ( var i=0;i<aLi.length;i++) { 
			aLi[i].className = '';			
		}
		obj.page = aLi[iNow].innerHTML;
		changeurltype('page',obj.page);
		!first&&fn&&fn();
		aLi[iNow].className = 'active';
		$(oUl).stop().animate({"left":iLeft},{duration: 200});	
		return true;
	};
	tab(true);
	oPage.style.display = 'block';
	return true;
};

//html5 文件上传
function uploadFile(options) {
	var index = layer.load(2, {
	  shade: [0.5,'#000'] //0.1透明度的白色背景
	});		
    var fd = new FormData();
    for (var name in options.json) {
    	 fd.append(name,options.json[name]);
    }
    var xhr = new XMLHttpRequest();
    xhr.upload.addEventListener("progress", uploadProgress, false);
    xhr.addEventListener("load", uploadComplete, false);
    xhr.addEventListener("error", uploadFailed, false);
    xhr.addEventListener("abort", uploadCanceled, false);
		xhr.open("POST", options.url);
    xhr.send(fd);
		
	function uploadProgress(evt) {
	  	console.log(evt);
	    if (evt.lengthComputable) {
	      var percentComplete = Math.round(evt.loaded * 100 / evt.total);
	//    $(".progress span").html(percentComplete.toString() + '%');
	    }
	    else {
	      
	    }
	}
	  function uploadComplete(evt) {
	    /*上传完成后执行函数*/
		layer.close(index);
	    options.success&&options.success(evt.target.responseText);
	    
	};
	function uploadFailed(evt) {
		layer.close(index);
	    alert("文件上传出错，请刷新重试");
	};
	function uploadCanceled(evt) {
		layer.close(index);
	    alert("上传超时，请刷新重试。");
  	};		
};

function toupload(obj,url,json,fn) {
	json = json||{};
	obj.onchange = function () {
		layer.msg('正在导入，请耐心等待导入完成');
		json.token = getCookie("token");
		if (json.file) {
			json[json.file] = $(this)[0].files[0];
		} else {
			json.file = $(this)[0].files[0];
		}
	 	uploadFile({
	 		url:Api.url+url+'?token='+getCookie("token"),
	 		json:json,
	 		success:function (str) {
	 			var json = eval('('+str+')');
	 			if (fn) {
	 				fn(json);
	 			} else {
					if (json.status==1) {
						uselayer(1,json.msg,function () {
							window.location.reload();
						});
					} 	 				
	 			}

//				var $obj = obj;
//				$(obj).remove();
//				$($obj).parent().append($obj);
				
	 		}
	 	});						
	};					
};

function togetteacher(id,did,fn) {
	Api.searchteacher({
		json:{
			id:id,
			department_id:did
		},
		fn:function (arr) {
			fn&fn(arr);
		}
		
	});
};

function togetfatherdepart(fn) {
	Api.fatherdepart({
		json:{},
		fn:function (arr) {
			fn&fn(arr);
		}
	});
};
function togetchilddepart(id,fn) {						
	Api.childdepart({
		json:{
			pid:id
		},
		fn:function (arr) {
			fn&fn(arr);
		}
	});					
};

function writesel(all,oDiv,arr,change,fn,name) {
	if (all!='') {
		$(oDiv).html('<option value="0">'+all+'</option>');
	} else {
		$(oDiv).html('');
	}
	for (var i = 0 ; i < arr.length; i++) {
		if (name) {
			$(oDiv).append('<option value="'+arr[i].name+'">'+arr[i].name+'</option>');
		} else {
			$(oDiv).append('<option value="'+arr[i].id+'">'+arr[i].name+'</option>');
		}					
	}
	$(oDiv).val($(oDiv).find('option').first().val());
	$(oDiv).off('change');
	if (change=="1") {
		$(oDiv).on('change',function () {
			fn($(oDiv).val());
		});
	}
	fn&&fn($(oDiv).val());
};	



function uselayer(type,str,fn,fn2) {
	if (type=='1') {
		var ilayer = layer.alert(
			str, 
			{btn:['确定']},
			function () {
				layer.close(ilayer);
				fn&&fn();							
			});		
	} else if (type=='2') {
		var ilayer = layer.confirm(
			str,
			{btn:['确定','取消']},
			function () {
				layer.close(ilayer);
				fn&&fn();
			},
			function () {
				layer.close(ilayer);
				fn2&&fn2();
			}
		);
	} else if (type=='3') {
		var ilayer = layer.msg(str,{
		  time: 2000 //2秒关闭（如果不配置，默认是3秒）
		}, function(){
		  fn&&fn();
		});  		
		
	}
};

/**
 * 柱状图
 * @author sunjie
 * @date    2016-06-15
 * @version [1.0]
 * @param   {string}   title 图表标题
 * @param   {string}   yname y轴标题
 * @param   {string}   xname x轴标题
 * @param   {string}   hovername 鼠标悬浮标题
 * @param   {array}   arr1  x轴数据
 * @param   {array}   arr2  y轴数据
 */
function writediagram(title,yname,xname,hovername,arr1,arr2) {
    $('.statistics').highcharts({
        chart: {
            type: 'column'
        },
        title: {
        	style:{
        		color:'#000'
        	},
            text: title
        },
        subtitle: {
        	useHTML:true,
            text: '&nbsp;'
        }, 		
        series: [{
            name: hovername,
            data: arr2,
            color:'#4f81bd',
            dataLabels: {
                enabled: false,
                rotation: 0,
                color: '#FFFFFF',
                align: 'center',
                x: 0,
                y: 0
            }            
        }],
        xAxis: {
            title: {
                text: xname,
	            style: {
	                color: '#999',
	                fontSize:'16px'
	            }                  
            },        	
            categories:arr1,
            labels:{
            	align:'center',
 	            style: {
	                color: '#999',
	                fontSize:'16'
	            }             	
            }            
        },
        yAxis: {
            title: {
                text: yname,
	            style: {
	                color: '#999',
	                fontSize:'16px'
	            }                  
            },
            allowDecimals:false,  //取整
            gridLineColor:'#c0c0c0',
            lineColor:'red',
            min:0,
            labels:{
 	            style: {
	                color: '#999',
	                fontSize:'20'
	            }             	
            }
        },
        legend: {
        	enabled:false	//图例
        },
		credits: {
			enabled: false  //右下角不显示LOGO 
		},
        exporting: { 
            enabled: false  //设置导出按钮不可用 
        }
    });
};

//公用方法
function common() {
	if (isIELower()) {
		alert('您的浏览器版本过低，请升级浏览器');
	}
	
	if (!getCookie("token")) {
//		window.location.href = "login.html";
	}
	if ($('.childTitle').hasClass('hascancle')) {
		$('.childTitle').append('<div class="childTitle-right cancle"><span class="glyphicon glyphicon-share-alt"></span></div>');
	}

	
	
	$('.cancle').click(function () {
		if (findurltype('page')==1) {
			history.go(-2);
		} else {
			history.go(-1);
		}
			
	});	
	
};






























