/**
 * 成绩查询
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        //科目成绩
        case "exam_assignment":exam_assignment();break;
        //考生成绩
    }
});