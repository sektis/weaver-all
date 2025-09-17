$(document).ready(function () {
    $("#chkall").click(function () {
        var $this = $(this);
        var $form = $this.closest('form');
        var all_checked = $this.is(":checked");

        $("[name='chk[]']",$form).each(function (i,e) {
            console.log(e)
            $(e).attr('checked',all_checked)
        })
    })
    $("[name='chk[]']").click(function () {
        var $this = $(this);
        var $form = $this.closest('form');
        var $all_check = $("#chkall",$form);

        var all_check_change = $("[name='chk[]']:checked",$form).length?true:false;
        $all_check.attr('checked',all_check_change);
    })
})