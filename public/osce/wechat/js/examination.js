var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "examination_list":examination_list();break;
    }
});



function examination_list(){
	
}
