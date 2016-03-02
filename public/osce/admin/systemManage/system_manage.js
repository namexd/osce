var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "authorization_add":authorization_add();break;
    }
});

/*
 * 新增角色权限
 * @author lizhiyuan
 * @version 1.0
 * @date    2016-01-15
 */
function authorization_add(){

}