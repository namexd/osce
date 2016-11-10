<link rel="stylesheet" href="{{asset('modules/boss/plugin/msgbox/msgbox.css')}}">
<script type="text/javascript" src="{{asset('modules/boss/plugin/artDialog/lib/sea.js')}}"></script>
<script type="text/javascript" src="{{asset('modules/boss/plugin/msgbox/msgbox.js')}}"></script>
<!-- Fonts -->
<script type="text/javascript">
    //加载artDialog
    window.dialog = null;
    window.d = null;
    seajs.config({
        alias: {
            "jquery": "jquery-1.10.2.js"
        }
    });

    //定义全局dialog对象
    seajs.use(['{{asset('modules/boss/plugin/artDialog/src/dialog-plus')}}'], function (dialog) {
        window.dialog = dialog;
    });

    //弹出框
    function floatBox(boxtitle,url,boxwidth,boxheight,closefunc,nofade)
    {
        boxwidth = boxwidth!='' ? boxwidth : 0;
        boxheight = boxheight!='' ? boxheight : 0;
        var func = $.isFunction(closefunc) ?  closefunc : function(){};
        window.d = window.dialog({
            url: url,
            title: boxtitle,
            width:boxwidth,
            height:boxheight,
            onclose:function(){
                func();
            }

        })

        if(boxwidth != 0)
        {
            d.width(boxwidth);
        }
        if(boxheight != 0)
        {
            d.height(boxheight);
        }
        if(nofade){
            d.show()
        }else
        {
            d.showModal();
        }
    }
</script>
