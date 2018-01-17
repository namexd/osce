/*公用方法 */
(function (w) {
	'use strict';
	//设置cookie
	w.setCookie = function(name,value,iDay) {
		var oDate = new Date();
		if (iDay>10) {
			oDate.setMinutes(oDate.getMinutes()+iDay);
		} else {
			oDate.setDate(oDate.getDate()+iDay);
		}
		document.cookie=name+"="+value+";expires="+oDate+";Path=/";
	};
	//获取cookie
	w.getCookie = function (name) { 
	   var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)","i");
	   var r = document.cookie.substr(1).match(reg);
	   if (r!=null) return (r[2]); return null;
	};
	//删除cookie
	w.removeCookie = function (name) { 
		setCookie(name,"",-1);
	};	
	//字符转编码
	w.strtocode = function (str) {
		var _arr = str.split("");
		var _arr2 = [];
		for (var i = 0; i<_arr.length; i++) {
			_arr2.push(_arr[i].charCodeAt());
		}
		var _str2 = _arr2.join("&");
		return _str2;
	};
	//编码转字符
	w.codetostr = function (str) {
		var arr = str.split("&");
		var _arr = [];
		for (var i = 0 ; i <arr.length; i++) {
			_arr.push(String.fromCharCode(arr[i]));
		}
		var _str = _arr.join("");
		return _str;
	};	
	//获取url参数
	w.findurl = function (name) {//获取url参数值
	   var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)","i");
	   var r = window.location.search.substr(1).match(reg);
	   if (r!=null) return (r[2]); return null;
	};
	//改变url参数
	w.changeurl = function (name,value,type) {
		var _href = window.location.href;
		var _name = type=='#'?findurltype(name):findurl(name);
		if (!_name) {
			if (window.location.search) {
				window.location.href=_href+('&'+name+'='+value);
			} else {
				window.location.href=_href+((type=='#'?'#':'?')+name+'='+value);
			}
		} else {
			window.location.href = _href.replace(name+'='+_name, name+'='+value);
		}
	};
	//获取#值
	w.findurltype = function (name) {
	   var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)","i");
	   var r = window.location.hash.substr(1).match(reg);
	   if (r!=null) return (r[2]); return null;	
	};
	//给小于10的加0
	w.addzero = function (n) {
		return n<10?"0"+n:n;
	};
	//时间戳转时间格式2016-05-01 12:01:12
	w.timetodate = function (num,n,t) {
		n=n||1;
		//判断如果是秒转换成毫秒
		if (String(num).length<=10) {
			num = num*1000;
		}
		var oDate = new Date();
		oDate.setTime(num);
		var y = t?oDate.getFullYear()+'年':oDate.getFullYear();
		var m = t?addzero(oDate.getMonth()+1)+'月':addzero(oDate.getMonth()+1);
		var d = t?addzero(oDate.getDate())+'日':addzero(oDate.getDate());
		var h = t?addzero(oDate.getHours())+'时':addzero(oDate.getHours());
		var mm= t?addzero(oDate.getMinutes())+'分':addzero(oDate.getMinutes());
		var s = t?addzero(oDate.getSeconds())+'秒':addzero(oDate.getSeconds());
		var str = '';
		switch (n){
			case 1: case 'h:mm':
				str = h+':'+mm;
				break;
			case 2: case 'h:mm:s':
				str = h+':'+mm+':'+s;
				break;
			case 3: case 'y-m':
				str = y+'-'+m;
				break;
			case 4: case 'm-d':
				str = m+'-'+d;
				break;
			case 5: case 'y-m-d':
				str = y+'-'+m+'-'+d;
				break;
			case 6: case 'm-d h:mm':
				str = m+'-'+d+' '+h+':'+mm;
				break;
			case 7: case 'm-d h:mm:s':
				str = m+'-'+d+' '+h+':'+mm+':'+s;
				break;
			case 8: case 'y-m-d h:mm:s':
				str = y+'-'+m+'-'+d+' '+h+':'+mm+':'+s;
				break;
			default:str = y+'-'+m+'-'+d;
				break;
		}
		return str;
	};
	//时间格式转时间戳
	w.datetotime = function (dateStr){
	    var newstr = dateStr.replace(/-/g,'/'); 
	    var date =  new Date(newstr); 
	    var time_str = date.getTime().toString();
	    return time_str.substr(0,10);
	};
	//公用弹窗方法
	w.uselayer = function (type,str,fn,fn2) {
		var layerindex = null;
		switch (type){
			case 1:
				layerindex = layer.alert(str,layerfn);			
				break;
			case 2:
				layerindex = layer.confirm(str,layerfn);			
				break;
			case 3:
				layerindex = layer.msg(str,layerfn,{time:1000,skin:'msg-error center',icon:1});			
				break;
			case 31:
				layerindex = layer.msg(str,layerfn,{time:1000,skin:'msg-success center',icon:1});			
				break;
			default:
				break;
		}
		function layerfn() {
			layer.close(layerindex);
			fn&&fn();
		};
		return layerindex;
	};
	//layer弹出层	
	w.uselayer2 = function (type,str,fn,title,obj) {
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
		
	
	//刷新页面
	w.toReload = function () {
		window.location.reload();
	};
	//判断字符串是否为空
	w.isnull = function (str) {
		var str2='';
		if (arguments.length==2) {
			str2=arguments[1];
		}
		return str?str:str2;
	};
	//判断是否IE低版本，默认是10
	w.isIELower = function (n) {
		var str = window.navigator.userAgent.toLowerCase();
		var ie = false;
		if (str.indexOf("msie 9") !=-1) { 
			ie = 9;
		}
		if (str.indexOf("msie 8") !=-1) { 
			ie = 8;
		}
		if (str.indexOf("msie 7") !=-1) { 
			ie = 7;
		}
		if (str.indexOf("msie 6") !=-1) { 
			ie = 6;
		}	
		n = n?n:10;
		if (ie&&ie<=n) {
			return true;
		}
		return false;
	};
	/*html5 文件上传
	 * url
	 * json:{file:$(this)[0].files[0]}
	 * fn: 成功回调
	 * progress:进度条回调
	 * error:失败回调
	 */
	w.uploadFile = function (options) {
		options = options||{};
		options.json = options.json||{};
		var shade = layer.load(0, {shade: [0.3,'#fff']});
	    var fd = new FormData();
	    for (var name in options.json) {
	    	 fd.append(name,options.json[name]);
	    }
	    var xhr = new XMLHttpRequest();
	    xhr.upload.addEventListener("progress", uploadProgress, false);
	    xhr.addEventListener("load", uploadComplete, false);
	    xhr.addEventListener("error", uploadFailed, false);
	    xhr.addEventListener("abort", uploadCanceled, false);
		xhr.open("POST",options.url);
	    xhr.send(fd);
		function uploadProgress(evt) {
		    if (evt.lengthComputable) {
		    	var percentComplete = Math.round(evt.loaded * 100 / evt.total);
				options.progress&&options.progress(percentComplete.toString());
		    }
		}
		function uploadComplete(evt) {
		    /*上传完成后执行函数*/
			layer.close(shade);
		    options.fn&&options.fn(evt.target.responseText);
		};
		function uploadFailed(evt) {
			layer.close(shade);
			uselayer(1,"文件上传出错，请刷新重试");
			options.error&&options.error(evt);
		};
		function uploadCanceled(evt) {
			layer.close(shade);
			uselayer(1,"上传超时，请刷新重试。");
			options.error&&options.error(evt);
	  	};		
	};
	/*下拉单选公用方法
	 * all
	 * oDiv:
	 * arr:
	 * options:{
	 * 	name:
	 * 	value:
	 * 	change: true, 设置下拉触发事件
	 * 	fn:
	 * 	go:true, 是否默认触发
	 * }
	 * 
	 */
	w.writesel = function (all,oDiv,arr,options) {
		options = options||{};
		if (all!='') {
			$(oDiv).html('<option value="0">'+all+'</option>');
		} else {
			$(oDiv).html('');
		}
		for (var i = 0 ; i < arr.length; i++) {
			$(oDiv).append('<option value="'+(options.value?arr[i][options.value]:arr[i].id)+'">'+(options.name?arr[i][options.name]:arr[i].name)+'</option>');
		}
		$(oDiv).val($(oDiv).find('option').first().val());
		$(oDiv).off('change');
		if (options.change) {
			$(oDiv).on('change',function () {
				options.fn($(oDiv).val());
			});
		}
		if (options.go) {
			options.fn&&options.fn($(oDiv).val());
		}
	};
	/*分页方法
	 * total  总量
	 * fn:   回调
	 * obj:  
	 * perpage:  每页
	 * 
	 */
	w.flipover = function (total,fn,obj,perpage) {
		perpage=perpage||Api.perpage;
		var nowpage = obj.page;
		
		if (findurltype('flippage')) {
			nowpage = findurltype('flippage');
			obj.page = nowpage;
		}
		
		if (!nowpage) {
			nowpage=1;
			obj.showpage=false;
			fn&&fn();
		}
		//防止重复执行代码
		if (obj.showpage) {
			return false;
		}	
		var oPage = document.getElementById("flippage");
		if (!oPage) {
			oPage = document.createElement('div');
			oPage.id = 'flippage';
			obj.parentNode.appendChild(oPage);
		}
		if (total<=perpage) {
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
			oPage.className = 'clearfix flippage';
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
		var nPage = Math.ceil(total/perpage);
		for (var i = 0 ; i < nPage; i++) {
			var oLi = document.createElement("li");
			oLi.innerHTML = i+1;
			oUl.appendChild(oLi);
		}
		if (nowpage>nPage) {
			nowpage = nPage;
			obj.showpage = false;
			fn&&fn();
			return false;
		}
		
		oUl.children[nowpage-1].className = "active";
		var iNow = nowpage-1;
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
			!first&&fn&&fn(obj);
			aLi[iNow].className = 'active';
			$(oUl).stop().animate({"left":iLeft},{duration: 200});
			obj.page = aLi[iNow].innerHTML;
			changeurl('flippage',obj.page,'#');
			return true;
		};
		tab(true);
		oPage.style.display = 'block';
		return true;
	};
})(window);



(function (w) {
	'use strict';	
	w.noempty = function (obj) {
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


	w.Api = {
		_api:{},
		ajax:function (options,url,type,e) {
			options.json=options.json||{};
			if (Api._api[url]) {
				return false;
			}
			Api._api[url] = true;
			layer.close(Api.shade);		
			Api.shade = layer.load(0, {
				shade: [0.3,'#fff'] //0.1透明度的白色背景
			});
			$.ajax({
				type:options.type?options.type:'get',
				url:options.url,
				data:options.json,
				success:function (res) {
					layer.close(Api.shade);		
					Api._api[url] = false;
					console.log(res);
					if (res.code!=1) {
						uselayer(1,res.message);
					} else {
						options.fn&&options.fn(res.data);
					}
				},
				error:function (json) {
					layer.close(Api.shade);			
					Api._api[url] = false;		
					console.log(json);
					if (json.status==401) {
						alert("登录失效， 请重新登录。");
					} else if (json.status==500) {
						alert("服务器内部错误，请稍后再试。");		
					} else {
						var _txt = eval('('+json.responseText+')');
						alert(_txt.error);
					}				
				}
			});						
		},
			
		aaa:'123'
		
	};


	
})(window);

