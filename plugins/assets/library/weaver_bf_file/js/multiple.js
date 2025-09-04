$.fn.wv_multiple_file = function(){
    var $form = $(this).closest('form');
    if(  $form[0].wv_multiple_error==1){
        return false;
    }
    var chk=true;
    $("[name^='bf_file[']:not([multiple])",$form).each(function () {
        var $this = $(this);

        var attr_name = $this.attr('name');
        var match = attr_name.match(/.*\[(.*)\]$/);


        if(!match[1]){
            alert('멀티플 인풋이 있을경우 일반 name에는 반드시 index가 포함되어야합니다.');
            chk = false;
            $form[0].wv_multiple_error=1;
            return false;
        }
    });
    if(!chk)return false;
    $form[0].wv_multiple_error=0;
    wv_multiple_file_do($(this));


};

function wv_multiple_file_do($this){

    $this.addClass('wv-file-preview');

    var index_range = $this.data('index-range');
    if(!index_range){
        alert('멀티플 파일 인풋의 index를 설정하세요.\nex) data-index-range="0,20"');
        $this.attr('disabled',true);
        return false;
    }

    var arr = index_range.split(',');
    var min = parseInt(arr[0]);
    var max = parseInt(arr[1]);
    if( !Number.isInteger(min) || !Number.isInteger(max)){
        alert('멀티플 파일 인풋의 index를 설정하세요.\nex) data-max="20"');
        $this.attr('disabled',true);
        return false;
    }


    if(min>max){
        alert('멀티플 인풋의 min index가 max index보다 큽니다');
        $this.attr('disabled',true);
        return false;
    }


    if(!$this.parent().is('.wv-multiple-wrap')){
        $this.parent().find('>*').wrapAll('<div class="wv-multiple-wrap"></div>')
    }



    var org_attr_name = $this.attr('name');
    var org_attr_id = $this.attr('id');
    var org_attr_class = $this.attr('class');


    $this.attr('name',org_attr_name.replace(/(\[)(\d*)\](?!.*\[)/, '[]'))
    $this.attr('id',org_attr_id.replace(/([_-])(\d*)$/, '$1'));




    var $multiple = $this.parent().find('.wv-multiple');
    if($multiple.length==0){
        $multiple = $('<div class="wv-multiple"></div>').insertAfter($this)
    }

    var $wrap = $this.closest('.wv-multiple-wrap');
    $(">*",$wrap).not($this).not('.wv-multiple').remove();

    for(var i=min;i<=max;i++){
        var new_attr_name = org_attr_name.replace(/(\[)(\d*)\](?!.*\[)/, '['+i+']');
        var new_attr_id = org_attr_id.replace(/([_-])(\d*)$/, '$1'+i);

        // if($("input[id='"+new_attr_id+"']").length ){
        //     alert("input[id='"+new_attr_id+"'] 객체가 이미 있습니다.");
        //     $this.attr('disabled',true);
        //     return false;
        // }
        if($("input[name='"+new_attr_name+"']").length ){
            alert("input[name='"+new_attr_name+"'] 객체가 이미 있습니다.");
            $this.attr('disabled',true);
            return false;
        }


        $multiple.append('<input type="file" name="'+new_attr_name+'" id="'+new_attr_id+'" class="d-none '+org_attr_class+'" >');






    }

    $this.on('change',function () {
        var $multiple_input = $(this);

        for(var i=0;i<$multiple_input[0].files.length;i++){
            var $match_input = $multiple_input.siblings('.wv-multiple').first().find(".wv-preview-wrap:not(:has(.file-reset)) input[name*='bf_file[']").first();
            if($match_input.length){

                // $match_input[0].files.push($multiple_input[0].files[i])
                var dataTransfer = new DataTransfer();
                dataTransfer.items.add($multiple_input[0].files[i]);
                $match_input[0].files = dataTransfer.files
                $match_input.trigger('change');
            }else{
                alert('최대 '+max+'개까지 첨부가능합니다.');
                break;
            }
        }
        $this.val(null);
    })
}





$(document).ready(function () {
    $("form[name='fwrite']").loaded("input[type='file'][multiple]",function () {
        $(this).wv_multiple_file()
    })
})