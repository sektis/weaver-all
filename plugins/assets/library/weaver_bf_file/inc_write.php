<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
$files = get_file($board['bo_table'],$wr_id);

?>
<script>
    $(document).ready(function () {
        var files = <?php echo count($files)?json_encode($files):json_encode(array())?>;
        var attr_name = 'data-bf-file';
        $("form[name='fwrite']").loaded("[name^='bf_file[']",function () {
            var $this = $(this);
            var file_i ='';
            var match = $this.attr('name').match(/.*\[(.*)\]$/);

            if(!match[1])return false;
            var i = parseInt(match[1]);
            var f = files[i];

            if(f && f.file){
                $this[0].setAttribute(attr_name, f.path+'/'+f.file);
            }

        })

    })
</script>


