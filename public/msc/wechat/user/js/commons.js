function duMiao(obj){
    obj.addClass('ipt_disabled');
    obj.attr('disabled',true);
    obj.val('60s再次获取');
    var dsq = setInterval(function(){
        obj.val((parseInt(obj.val())
            -1)+'s再次获取');
        if(parseInt(obj.val()) <= 1){
            obj.val('获取验证码');
            obj.attr('disabled',false);
            obj.removeClass('ipt_disabled');
            clearInterval(dsq);
        }
    },1000);

}
