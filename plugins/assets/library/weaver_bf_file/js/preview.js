$.fn.wv_preview_file = function(){

    wv_preview_file_do($(this));

};

function wv_preview_file_do($this){

    var input = $this[0]
    var $form = $this.closest('form');
    var $preview_area = $("[for="+$this.attr('id')+"]",$form);

    if($this.is("[multiple]")){
       return false;
    }
    $this.addClass('d-none');

    if(window.getComputedStyle($this.parent()[0]).position !== 'relative'){
        $this.wrap('<div class="wv-preview-wrap"></div>')
    }

    var $preview_wrap = $this.closest('.wv-preview-wrap');




    if($this.closest('.wv-multiple').length){
        var attr_name = $this.attr('name');
        var match = attr_name.match(/.*\[(.*)\]$/);
        $preview_wrap.append('<input type="hidden" name="wv_multiple_order['+match[1]+']" value="'+match[1]+'" >');
    }



    if($preview_area.length==0){
        $preview_area = $('<label class="ratio ratio-1x1 w-100  overflow-hidden" style="border-radius:1em;background-color: rgba(204,204,204,1);background-image:url(https://s2.svgbox.net/octicons.svg?ic=image); background-repeat: no-repeat;background-position: center;;background-size:20%;" for="'+$this.attr('id')+'"></label>').insertAfter($this);
    }


    var bf_file_check_time = 0;
    var bf_file_check_tik = 50;
    var bf_file_check = setInterval(function () {
        var data_bf_file = $this.data('bf-file')
        if(bf_file_check_time>1000){
            clearInterval(bf_file_check)
        }
        if(data_bf_file){

            wv_insert_preview($this,$preview_area,wv_get_file_type(data_bf_file),data_bf_file);
            clearInterval(bf_file_check)
        }
        bf_file_check_time+=bf_file_check_tik;

    }, bf_file_check_tik);






    $this.on('change',function () {
        if (input.files && input.files[0]) {

            var file_type = input.files[0].type.split("/")[0];




            var preview_url = URL.createObjectURL(input.files[0]);
            $preview_area.addClass('position-relative')
            if($preview_area.css('background-image')){
                $preview_area[0].setAttribute('data-background-image', $preview_area.css('background-image'));
            }


            wv_insert_preview($this,$preview_area,file_type,preview_url,input.files[0].name);


        }
    })
}

function wv_get_file_type(fileName) {
    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    const videoExtensions = ['mp4', 'mov', 'avi', 'mkv', 'flv', 'wmv'];

    // 파일 확장자 추출
    const extension = fileName.split('.').pop().toLowerCase();

    if (imageExtensions.includes(extension)) {
        return 'image';
    } else if (videoExtensions.includes(extension)) {
        return 'video';
    } else {
        return 'other';
    }
}



function wv_insert_preview($file_input,$preview_area,file_type,preview_url,source_name){

    if(file_type=='video'){
        var video = $preview_area.append('<video muted style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);width:auto;height:auto;max-width: 100%;max-height: 100%;z-index: 1;object-fit: contain;"></video>').find('video')[0];


        video.src = preview_url;
        const timer = setInterval(() => {
            if (video.readyState == 4) {
                video.currentTime = 10;
                clearInterval(timer);
            }
        }, 500);


    }else if(file_type=='image'){
        var img = $preview_area.append('<img style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);width:auto;height:auto;max-width: 100%;max-height: 100%;z-index: 1;object-fit: contain;"></img>').find('img')[0];


        img.src = preview_url;
    }else{
        var txt = $preview_area.append('<p style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);width: 100%;padding:2em;height: 100%;z-index: 1;display: flex;align-items: center;justify-content: center;background-color: #fff"></p>').find('p');
        txt.html(source_name);
    }

    // $preview_area.append('<a class="file-reset" style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);padding-top: 1em;padding-right: 1em;width: 100%;height: 100%;display:flex;align-items:start;justify-content: end;background-color: rgba(0,0,0,0);color:#fff;z-index: 3"><i class="fa-solid fa-x" style="font-size:2em;cursor: pointer"></i></a>')
    $preview_area.append('<a class="file-reset" style="position: absolute;right: 0;top: 0;padding: 1em;width: auto;height: auto;display: inline-block;align-items: start;justify-content: end;background-color: rgba(0, 0, 0, 0.4);color: #fff;z-index: 3;left: unset;"><i class="fa-solid fa-x" style="font-size:2em;cursor: pointer"></i></a>')

    $(".file-reset",$preview_area).click(function (e) {
        e.preventDefault()
        var $reset_btn = $(this);
        $('video',$preview_area).remove();
        $('img',$preview_area).remove();
        $file_input.val(null);
        $reset_btn.remove();
        var match = $file_input.attr('name').match(/.*\[(.*)\]$/);
        var $del_input = $("[name='bf_file_del["+match[1]+"]']");
        if($del_input.length){
            $del_input.attr('checked',true);
        }else{
            $('<input type="hidden" name="bf_file_del['+match[1]+']"   value="1">').insertAfter($preview_area)
        }
        var $multiple = $preview_area.closest('.wv-multiple');
        if($multiple.length){
            var order = $("[name^='wv_multiple_order[']",$multiple).first().val();
            $multiple.append($preview_area.closest('.wv-preview-wrap').detach())


            $("[name^='wv_multiple_order[']",$multiple).each(function (i,e) {

                $(e).val(order)
                order++;
            })



        }
    })

}

$(document).ready(function () {
    $("form[name='fwrite']").loaded(".wv-file-preview",function () {

        $(this).wv_preview_file()
    })

})