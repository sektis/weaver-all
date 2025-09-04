<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

?>

<div id="<?php echo $skin_id?>" class="accordion accordion-flush h-100">
    <style>
        <?php echo $skin_selector?> {position:relative;padding: var(--wv-15) 0}

        <?php echo $skin_selector?> .collapse-wrap {position: absolute;right: 0;top:0;width: 100%;height:100%;display: flex;justify-content: end;z-index: 100;padding: var(--wv-10);padding-right: 0}
        <?php echo $skin_selector?> .collapse-icon {transform-origin: center;transition:transform 0.5s  cubic-bezier(0.77,0.2,0.05,1.0);}
        <?php echo $skin_selector?> .collapse-icon i{line-height: 0;color: #5F5F5F;font-size: var(--wv-8);}
        <?php echo $skin_selector?> .collapse-icon i:before{content: "▼";}
        <?php echo $skin_selector?> [aria-expanded="true"]>.collapse-icon  {transform: rotate(-180deg);}
        <?php echo $skin_selector?> [aria-expanded="true"]>.collapse-icon  :before{content: "▼"}


        <?php echo $skin_selector?> .depth-wrap-1{height: 100%}
        <?php echo $skin_selector?> .depth-ul-1{align-items: center}
        <?php echo $skin_selector?> .depth-li-1{position:relative;padding: 0 var(--wv-15)}
        <?php echo $skin_selector?> .depth-li-1:after{content:'';position: absolute;top:50%;transform: translateY(-50%);left:100%;width: 1px;height: 70%;background: #ddd}
        <?php echo $skin_selector?> .depth-link-1{font-size: var(--wv-md-18, var(--wv-14));}
        <?php echo $skin_selector?> .depth-link-1:has(+[data-bs-target]){margin-right: var(--wv-40); }
        <?php echo $skin_selector?> .depth-li-1.on .depth-link-1{}

        <?php echo $skin_selector?> .depth-wrap-2 [class*=depth-link-]{font-size: calc((10 - var(--wv-menu-depth)/2) *  0.1em);color:#5F5F5F}

        <?php echo $skin_selector?> .depth-wrap-2{position:absolute;top:100%;left:50%;transform:translateX(-50%);min-width:150%;width: max-content; margin-top: var(--wv-15);background: #fff;z-index: 100;border: 1px solid #d2d2d2;border-radius: var(--wv-8_6);}
        <?php echo $skin_selector?> .depth-ul-2  {flex-direction: column }
        <?php echo $skin_selector?> .depth-li-2  {}
        <?php echo $skin_selector?> .depth-link-2{padding: var(--wv-15) var(--wv-26);font-size: var(--wv-md-18, var(--wv-14));border-bottom: 2px solid #d2d2d2;color: #a5a5a5;}
        <?php echo $skin_selector?> .depth-link-2:hover{}


    </style>
    <?php ob_start(); ?>
        <svg class="w-[22px]"viewBox="0 0 22 21" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <mask id="mask0_1_366" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="22" height="21">
                <rect width="22" height="20" transform="translate(0 0.5)" fill="url(#pattern0_1_366)"/>
            </mask>
            <g mask="url(#mask0_1_366)">
                <rect y="0.5" width="22" height="20" fill="#111111"/>
            </g>
            <defs>
                <pattern id="pattern0_1_366" patternContentUnits="objectBoundingBox" width="1" height="1">
                    <use xlink:href="#image0_1_366" transform="scale(0.0454545 0.05)"/>
                </pattern>
                <image id="image0_1_366" width="22" height="20" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAUCAMAAAC+oj0CAAAA3lBMVEUAAACAgIBmQ2dmQ2ZnQ2dVVVVnQ2dlQ2hmQmdmQ2dmQ2dmQ2dmQ2hmQ2dmQ2dmTWZmQmhmQ2dmQ2dmQ2ZmQ2dkQ2RkQWmAQIBnQmdnQ2dhSWFtSW1mRGZnQ2dmQWZrQ2tlQmddRl1iRWxqRmpjR2NmQ2hoQmhmQ2dmRGZmQWZmQ2ZkQGRmRGZlQmVxOXFgQGBmRGZmQ2ZlQWVmR2ZmQ2ZmQ2dnRWdmRGZqQGpoQGhmRGZtSW1mQ2dlQ2VkRmRpQWlmRmZmRGZnRGdmQ2dmQ2dmQmhkQ2lpQ2lVVVVlQmiuwvl+AAAASnRSTlMAAv9QxwOPXGv+lM+75J4KYLfwp/cXMwRkgRUHD3gxE4gLGh0SmxtHSzduHKJJCQixQSsZ1utDHgwg2w7BNSEnKDx8+9+lPSIGUeKM88UAAADdSURBVHicZc7VmsIwEAXgIZu60BZbbJHF3d2d938hOg0NBXKRnPz5MjMA8zA8VyJXHJYElkPkh4Wdqsot4kTLlSBb8g2AHgdrbVx/sfznbnWSBwjbIudaH9/sBfbp/vtcSKLulYvfnXHjipHi7yCbmH5HnXeOqW5IGdiVGePJwc092VfBTHksZd0SEV4gT3SPLRHitMBZMHMet2mM4rAftZvKKh5QPqDxKhxg9axUv/guSiHd4Z6wGGsSQIYq5S1eZ1Jk43G0hkc66ZCTbmtGNs0+Lf1pS5VpMfO8PAA1XA88beP2/gAAAABJRU5ErkJggg=="/>
            </defs>
        </svg>
    <?php
    $first_link_name = ob_get_clean();
    $first_link_arr = array(
            'name'=>$first_link_name,
        'url'=>G5_URL,
        'target'=>'_self',
    );
    array_unshift($data, $first_link_arr);

    echo wv_collapse_menu(function ($depth,$content,$curr_id) {
        $replace_search = array("collapse show", 'aria-expanded="true"');
        $replace_target = array("collapse", 'aria-expanded="false"');

        return str_replace($replace_search,$replace_target,$content);
    },$skin_id,$data);


    ?>
    <script>
        $(document).ready(function () {

            var $skin = $('<?php echo $skin_selector?>');



            $(".collapse",$skin).on('hide.bs.collapse',function (e) {
                $('.collapse',$(e.target)).collapse('hide')
            })
            $(".collapse",$skin).on('show.bs.collapse',function (e) {
                $('.collapse[data-depth="'+$(e.target).data('depth')+'"]',$skin).collapse('hide')
            })

        })
    </script>
</div>




