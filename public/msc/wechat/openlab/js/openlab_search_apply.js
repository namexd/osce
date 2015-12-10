/**
 * Created by DELL on 2015/11/30.
 */
function get_list(){
    $("#leibie_list").animate({right:"0"});//将左边弹出
    $(".leibie_left").click(function(){
        $("#leibie_list").animate({right:"-100%"});
    });
    $("#comfirm_student").click(function(){
        $("#leibie_list").animate({right:"-100%"});
    });
}
$(document).ready(function(){
    $("#class_list").change(function(){//班级插入选择

        var selected= $(this).find("option:selected").text();
        var selected_id= $(this).find("option:selected").val();
        if(add_class_list.length==""){
            push()
        }else{
            var count="0";
            $.each(add_class_list, function(){
                if(this==selected){
                    return false;
                }else{
                    count++;
                }
            });
            if(count==add_class_list.length){
                push();
            }}
        function  push(){
            add_class_list.push(selected);
            $("#class_selected ul").append(
                ' <li class="more_li" data="'+selected_id+'"><span>'
                + selected
                +'</span><i class="fa  fa-times  font18"></i></li>');
            var ss = $('.ss').val();
            $('#frmTeacher').find('.ss').remove();
            var id_all = '';
            if(ss){
                id_all = ss+','+selected_id;
            }else{
                id_all = selected_id;
            }
            var str = "<input type='hidden' class='ss' name='class_id' value='"+id_all+"'>";
            $('#frmTeacher').append(str);
        }
        $(".fa-times").unbind().click(function(){
            $(this).parent().remove();
            add_class_list.splice($.inArray($(this).siblings("span").text(),add_class_list),1);

            var id_str = $('.ss').val();
            var arr = id_str.split(',');
            var data = $(this).parent().attr('data');
            arr.splice(jQuery.inArray(data,arr),1);
            id_all = arr.join(',');
            $('.ss').val(id_all);
        })

    });

    $("#group_list").change(function(){//学生组插入选择
        var selected= $(this).find("option:selected").text();
        var selected_id= $(this).find("option:selected").val();
        if(add_group_list.length==""){
            push2()
        }else{
            var count="0";
            $.each(add_group_list, function(){
                if(this==selected){
                    return false;
                }else{
                    count++;
                }
            });
            if(count==add_group_list.length){
                push2();
            }}
        function  push2(){
            add_group_list.push(selected);
            $("#group_selected ul").append(
                ' <li class="more_li"><span>'
                + selected
                +'</span><i class="fa  fa-times  font18"></i></li>');
            var gg = $('.gg').val();
            $('#frmTeacher').find('.gg').remove();
            var id_all = '';
            if(gg){
                id_all = gg+','+selected_id;
            }else{
                id_all = selected_id;
            }
            var str = "<input type='hidden' class='gg' name='group_id' value='"+id_all+"'>";
            $('#frmTeacher').append(str);
        }
        $(".fa-times").unbind().click(function(){
            $(this).parent().remove();
            add_group_list.splice($.inArray($(this).siblings("span").text(),add_group_list),1);
            var id_str = $('.gg').val();
            var arr = id_str.split(',');
            var data = $(this).parent().attr('data');
            arr.splice(jQuery.inArray(data,arr),1);
            id_all = arr.join(',');
            $('.gg').val(id_all);
        })
    });

})