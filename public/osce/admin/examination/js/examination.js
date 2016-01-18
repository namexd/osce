/**
 * Created by j5110 on 2016/1/15.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "test_station":test_station();break;
        case "examroom":examroom();break;
        case "clinicalcase":clinicalcase();break;
        case "categories":categories();break;
        case "invigilator":invigilator();break;
    }
});