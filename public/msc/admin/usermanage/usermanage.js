var pars;
$(function() {
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "student_manage":student_manage();break;//用户管理
    }
});


 /**
	*用户管理
	*zouzhiqiang
	*QQ:812408748
	*2015-12-15
	*update:zouzhiqiang (2015-12-15 15:00)
**/
function student_manage(){

}
