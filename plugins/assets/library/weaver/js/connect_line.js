$.fn.connect_line = function(line_style){
    var elems = [];
    var $this = $(this);
    $this.each(function (i,e) {
        var $target = $(e);
        elems.push($target);
    })
    if(!line_style){
        line_style = 'border-bottom:1px solid #000';
    }

    for(var i=0;i<elems.length;i++){

        var $from = elems[i];
        var $to = elems[i+1];

        if(!$to)break;

        if(!$('.wv-connect-line',$from).length){
            $from.append('<div class="wv-connect-line" style="'+line_style+'"></div>')
        }
        wv_drawLine($from,$to,$(".wv-connect-line",$from));
    }

    $(window).on('resize',wv_debounce( function () {

        $this.connect_line(line_style);
    },50))
};
function wv_line_distance(x, y, x0, y0){
    return Math.sqrt((x -= x0) * x + (y -= y0) * y);
};
function wv_drawLine(a, b, line) {
    var pointA = $(a).offset();
    var pointB = $(b).offset();
    var pointAcenterX = $(a).width() / 2;
    var pointAcenterY = $(a).height() / 2;
    var pointBcenterX = $(b).width() / 2;
    var pointBcenterY = $(b).height() / 2;
    var angle = Math.atan2(pointB.top - pointA.top, pointB.left - pointA.left) * 180 / Math.PI;
    var distance = wv_line_distance(pointA.left, pointA.top, pointB.left, pointB.top);


    // Set Angle
    $(line).css('transform', 'rotate(' + angle + 'deg)');

    // Set Width
    $(line).css('width', distance + 'px');

    // Set Position
    $(line).css('position', 'absolute');
    if(pointB.left < pointA.left) {
        $(line).offset({top: pointA.top + pointAcenterY, left: pointB.left + pointBcenterX});
    } else {
        $(line).offset({top: pointA.top + pointAcenterY, left: pointA.left + pointAcenterX});
    }
}