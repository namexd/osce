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

//layer弹出层	
function uselayer (type,str,fn,title,obj) {
	var data = {title:[(title?title:'信息'),'background-color:#16abff;color:#fff']};
	if (type=='1') {
		var ilayer = layer.alert(str,data,function () {
				layer.close(ilayer);
				fn&&fn();							
		});		
	} else if (type=='2') {
		var ilayer = layer.confirm(str,data,function () {
				layer.close(ilayer);
				fn&&fn();
			}
		);
	} else if (type=='3') {
		
		var ilayer = layer.msg(str,{skin:'msg-error center',icon:1,time:1500},function(){
		  		fn&&fn();
		});  		
	} else if (type=='31') {
		
		var ilayer = layer.msg(str,{skin:'msg-success center',icon:1,time:1500},function(){
		  		fn&&fn();
		});  		
	} else if (type=='4') {
		data = {
			type: 1,
			title: data.title,
			area: $(obj).width(),
			content: $(obj)
		};
		var ilayer = layer.open(data);	
	}
	return ilayer;
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



































