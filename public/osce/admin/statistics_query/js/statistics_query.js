/**
 * Created by Administrator on 2016/1/28 0028.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "exam_vcr":exam_vcr();break;
    }
});

/*
科目考试视频
author:lizhiyuan
date:2016/1/28
*/
function exam_vcr(){
    
}