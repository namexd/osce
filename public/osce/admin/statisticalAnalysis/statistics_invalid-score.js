/**
 * Created by Administrator on 2016/6/23 GaoDapeng
 * 无效成绩原因显示
 */
$(function () {
    invalid_ajax();
});
function invalid_ajax() {
    var url = $('#url').val();
    $('.invalid_score').click(function (){
        var result_id = $(this).siblings('input').val();
        $.ajax({
            url:url,
            data: {result_id:result_id},
            type: "get",
            dataType: "json",
            success: function (data) {
                if(1 == data.code){
                    if('undefined' != typeof(data.data[0].reason)){
                        switch (data.data[0].description)
                        {
                            case 1 :
                                layer.alert('该考生放弃考试，理由是：'+data.data[0].reason);
                                break;
                            case 2 :
                                layer.alert('该考生考试作弊，理由是：'+data.data[0].reason);
                                break;
                            case 3 :
                                layer.alert('该考生考试替考，理由是：'+data.data[0].reason);
                                break;
                            case 4 :
                                layer.alert('该考生考试异常，理由是：'+data.data[0].reason);
                                break;
                        }
                    }else {
                        layer.alert('未找到无效原因');
                    }
                }else {
                    layer.alert('未找到该成绩信息！');
                }
            }
        })
    });
}

