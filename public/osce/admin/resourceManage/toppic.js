/**
 * Created by 梧桐雨间的枫叶 on 2016/1/24.
 */
var toppic  =   new Array;

//初始化 考核点考核项表单
function initToppic(){

}

//新增考核点
function addPoint(event,porintIndex,content,total){
    var tbody   =   $('tbody');
    var porint  =   $('<tr>');

    if(!arguments[1]) porintIndex = 0;
    if(!arguments[2]) content = '';
    if(!arguments[3]) total = 0;


    if(porintIndex==0)
    {
        if(tbody.children().length>0)
        {
            tbody.children().after(porint);
            porintIndex  =   toppic.length;
        }
        else
        {
            porintIndex =   1;
        }
    }
    else
    {
        var update  =   true;
    }

    var index       =   $('<td>').text(porintIndex);
    var mame        =   $('<td>');
    var score       =   $('<td>');
    var ctrl        =   $('<td>');

    //增加 第二列
    var nameBox     =   $('<div>').addClass('form-group');
    var nameLabel   =   $('<label>').addClass('col-sm-2').addClass('control-label').text('考核点:');
    var inputBox    =   $('<div>').addClass('col-sm-10');
    var nameInput   =   $('<input>').attr(
        {
            name:'content['+porintIndex+'][title]',
        }
    ).addClass('form-control').addClass('select_Category').val(content);


    mame.append(nameBox);
    nameBox.append(nameLabel);
    nameBox.append(inputBox);
    inputBox.append(nameInput);

    //增加第三列
    var scoreInput  =   $('<input>').attr(
        {
            name:'score['+porintIndex+'][total]',
        }
    ).css('display','none').val(total);
    var scoreValue  =   $('<span>').text(total);
    score.append(scoreInput);
    score.append(scoreValue);

    //增加第四列
    var delBox  =   $('<a>').attr('href','javascript:void(0)');
    var upbox   =   $('<a>').attr('href','javascript:void(0)');
    var downbox =   $('<a>').attr('href','javascript:void(0)');
    var addBox  =   $('<a>').attr('href','javascript:void(0)');

    var delSpan     =   $('<span>').addClass('read').addClass('state2').addClass('detail');
    var delIco      =   $('<i>').addClass('fa').addClass('fa-trash-o').addClass('fa-2x');
    var upSpan      =   $('<span>').addClass('read').addClass('state1').addClass('detail');
    var upIco       =   $('<i>').addClass('fa').addClass('fa-arrow-up').addClass('child-up').addClass('fa-2x');
    var downlSpan   =   $('<span>').addClass('read').addClass('state1').addClass('detail');
    var downIco     =   $('<i>').addClass('fa').addClass('fa-arrow-down').addClass('child-down').addClass('fa-2x');
    var addSpan     =   $('<span>').addClass('read').addClass('state1').addClass('detail');
    var addIco      =   $('<i>').addClass('fa').addClass('fa-plus').addClass('fa-2x');
    delSpan.append(delIco);
    upSpan.append(upIco);
    downlSpan.append(downIco);
    addSpan.append(addIco);
    delBox  .append(delSpan);
    upbox   .append(upSpan);
    downbox .append(downlSpan);
    addBox  .append(addSpan);

    ctrl.append(delBox).append(upbox).append(downbox).append(addBox);

    porint.append(index);
    porint.append(mame);
    porint.append(score);
    porint.append(ctrl);



    porint.addClass('porint');
    porint.addClass('porint'+porintIndex);
    tbody.append(porint);
    porint.attr({
        'data-porintindex':porintIndex
    });
    if(update!=true)
    {
        toppic[porintIndex]  =   {
            index :porintIndex,
            content :content,
            score   :total,
            'child'   :new Array,
        };
    }
    addBox.bind('click',addOption)
}

//新增考核项
function addOption(event,porintIndex,optionIndex,content,description,score){
    if(!arguments[1]) porintIndex   = 0;
    if(!arguments[2]) optionIndex   = 0;
    if(!arguments[3]) content       = '';
    if(!arguments[4]) description   = '';
    if(!arguments[5]) score         = 1;

    var tbody   =   $('tbody');
    var option      =   $('<tr>').addClass('option');

    if(porintIndex==0)
    {
        porintIndex =   $(this).parents('.porint').data('porintindex');
    }
    else
    {
        var update      =   true;
        var porintData  =   toppic[porintIndex];
        var havenChilds =   porintData.child;
    }
    var porintData;

    for (var pIndex in toppic)
    {
        if(pIndex==porintIndex)
        {
            var porintData  =   toppic[pIndex];
            break;
        }
    }
    if(porintData==null){
        return false;
    }
    if(optionIndex==0)
    {
        var havenChilds =   porintData.child;
        //optionIndex     =   havenChilds.length+1;
        for(optionIndex in havenChilds)
        {
            //alert(havenChilds[optionIndex]);
        }
        if(optionIndex==undefined)
        {
            optionIndex=1;
        }
        optionIndex =   parseInt(optionIndex)+1
    }

    if(optionIndex==0)
    {
        optionIndex =1;
        havenChilds = new Array;
    }

    var optionIndexName =   porintIndex+'-'+ optionIndex;

    var index       =   $('<td>').text(optionIndexName);
    var mame        =   $('<td>');
    var scoreDom     =   $('<td>');
    var ctrl        =   $('<td>');


    //增加 第二列
    var contentBox     =   $('<div>').addClass('form-group');
    var contentLabel   =   $('<label>').addClass('col-sm-2').addClass('control-label').text('考核项:');
    var contentInputBox    =   $('<div>').addClass('col-sm-10');
    var contentInput   =   $('<input>').attr(
        {
            name:'content['+porintIndex+']['+optionIndex+']',
        }
    ).addClass('form-control').addClass('select_Category').val(content);

    var descriptionBox  =   $('<div>').addClass('form-group');
    var descriptionLabel   =   $('<label>').addClass('col-sm-2').addClass('control-label').text('评分标准:');
    var descriptionInputBox    =   $('<div>').addClass('col-sm-10');
    var descriptionInput   =   $('<input>').attr(
        {
            name:'description['+porintIndex+']['+optionIndex+']',
        }
    ).addClass('form-control').addClass('select_Category').val(content);


    mame.append(contentBox);
    contentBox.append(contentLabel);
    contentBox.append(contentInputBox);
    contentInputBox.append(contentInput);
    mame.append(descriptionBox);
    descriptionBox.append(descriptionLabel);
    descriptionBox.append(descriptionInputBox);
    descriptionInputBox.append(descriptionInput);

    option.append(index);
    option.append(mame);
    option.append(scoreDom);
    option.append(ctrl);

    //增加第三列
    var scoreSelect =   $('<select>').addClass('scoreSelect');

    for(var num=1;num<=10;num++)
    {
        var scoreOption =   $('<option>').attr({
            value:num
        }).text(num);
        if(score==num)
        {
            scoreOption.attr('selected','selected');
        }
        scoreSelect.append(scoreOption);
    }
    scoreSelect.attr({
        'name':'score['+porintIndex+']['+optionIndex+']',
    });
    scoreDom.append(scoreSelect);

    //增加第四列
    var delBox  =   $('<a>').attr('href','javascript:void(0)');
    var upbox   =   $('<a>').attr('href','javascript:void(0)');
    var downbox =   $('<a>').attr('href','javascript:void(0)');
    var addBox  =   $('<a>').attr('href','javascript:void(0)');

    var delSpan     =   $('<span>').addClass('read').addClass('state2').addClass('detail');
    var delIco      =   $('<i>').addClass('fa').addClass('fa-trash-o').addClass('fa-2x');
    var upSpan      =   $('<span>').addClass('read').addClass('state1').addClass('detail');
    var upIco       =   $('<i>').addClass('fa').addClass('fa-arrow-up').addClass('child-up').addClass('fa-2x');
    var downlSpan   =   $('<span>').addClass('read').addClass('state1').addClass('detail');
    var downIco     =   $('<i>').addClass('fa').addClass('fa-arrow-down').addClass('child-down').addClass('fa-2x');
    var addSpan     =   $('<span>').addClass('read').addClass('state1').addClass('detail');
    var addIco      =   $('<i>').addClass('fa').addClass('fa-plus').addClass('fa-2x');
    delSpan.append(delIco);
    upSpan.append(upIco);
    downlSpan.append(downIco);
    addSpan.append(addIco);
    delBox  .append(delSpan);
    upbox   .append(upSpan);
    downbox .append(downlSpan);
    addBox  .append(addSpan);
    delBox.bind('click',delOption);
    ctrl.append(delBox).append(upbox).append(downbox);

    var optionData      =   {
        index       :optionIndex,
        content     :content,
        score       :score,
        description :description
    };
    if(havenChilds.length>0)
    {
        var perOption   =   havenChilds[optionIndex-1];
        if(perOption==null)
        {
            alert(0);
            return ;
        }
        $('.option'+porintIndex+'-'+perOption.index).after(option);
        havenChilds[parseInt(optionIndex)]    =   optionData;
    }
    else
    {
        tbody.append(option);
        havenChilds[parseInt(optionIndex)]      =   optionData;
    }
    option.addClass('option'+porintIndex+'-'+optionIndex);
    option.attr('data-option',optionIndex).attr('data-porintindex',porintIndex).addClass('porint_preant_'+porintIndex);
    if(update!=true)
    {
        toppic[pIndex].child=havenChilds;
    }
}

function delOption(){
    var row =   $(this).parents('.option');
    var porintindex =   row.data('porintindex');
    var optionIndex =   row.data('option');

    var socreData   =   row.find('.scoreSelect').find("option:selected").val();

    var thisPorint  =   toppic[porintindex];
    var brother     =   thisPorint.child;
    var newBroter   =   new Array;
    newBroter[0]    =   null;
    for (var i in brother)
    {
        if(brother[i]==null)
        {
            continue;
        }

        if(brother[i].index!=optionIndex)
        {
            newBroter.push(brother[i]);
        }
    }
    thisPorint.child    =   newBroter;
    thisPorint.score    -=  socreData;
    toppic[porintindex] =   thisPorint;
    updateDom()
}


function updateDom(){
    $('.table').find('tbody').children().remove();
    var toppicData  =   toppic;
    //toppic  =   new Array;
    for (var porintIndex in toppicData)
    {
        addPoint('',porintIndex,toppicData[porintIndex].content,toppicData[porintIndex].score);
        var childrens   =   toppicData[porintIndex].child;

        for (var optionIndex in childrens)
        {
            if(childrens[optionIndex]==null)
            {
                continue;
            }
            alert(123);
            addOption('',toppicData[porintIndex].index,childrens[optionIndex].index,childrens[optionIndex].content,childrens[optionIndex].description,childrens[optionIndex].score);
        }
    }
}
$(function(){
    $('#add-new').click(addPoint)
});