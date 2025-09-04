<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
$sub_contents = '';
?>
<div id="<?php echo $skin_id?>" class="h-100">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> a{white-space: nowrap}

        <?php echo $skin_selector?> .depth-ul-1{align-items: start;gap: var(--wv-60)}
        <?php echo $skin_selector?> .depth-li-1{transition: all .1s }
        <?php echo $skin_selector?> .depth-link-1{transition: all .1s;font-size: var(--wv-18);text-align: center;padding: var(--wv-8) var(--wv-21);background: #96766D;border-radius: var(--wv-17_5);font-weight: 700;color:#fff}


        <?php echo $skin_selector?> .depth-wrap-2{margin-top: var(--wv-28)}
        <?php echo $skin_selector?> .depth-ul-2{gap: var(--wv-28);flex-direction: column;}
        <?php echo $skin_selector?> .depth-li-2{}
        <?php echo $skin_selector?> .depth-link-2{text-align: center;font-size: var(--wv-16)}
        <?php echo $skin_selector?> .depth-link-2:hover{color:#95756D}


        <?php echo $skin_selector?> .menu-background{border-top:2px solid #96766D;transition: all .54s ease ;
                                        border-bottom-left-radius: var(--wv-20);border-bottom-right-radius: var(--wv-20);
                                        background: rgba(255, 255, 255, 0.92);overflow: hidden;position:absolute;top:calc(100% - 1px);width: calc(100% + 2px);left:50%;transform: translateX(-50%);z-index: 100;;clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);display: flex;justify-content: center;}
        <?php echo $skin_selector?> .menu-background .menu-background-inner{padding: var(--wv-50)}
        <?php echo $skin_selector?> .menu-background.show{clip-path: polygon(0 0, 100% 0, 100% 100%, 0% 100%);}


    </style>
    <div class="  menu-btn " style="cursor: pointer"  >
        <svg class="w-[28px]" viewBox="0 0 28 27" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.43438 1.65479C5.77898 1.65479 6.86888 2.74489 6.86888 4.08939C6.86888 5.43379 5.77898 6.52389 4.43438 6.52389C3.08988 6.52389 1.99988 5.43379 1.99988 4.08939C1.99988 2.74489 3.08988 1.65479 4.43438 1.65479ZM14.1723 1.65479C15.5169 1.65479 16.6068 2.74489 16.6068 4.08939C16.6068 5.43379 15.5169 6.52389 14.1723 6.52389C12.8279 6.52389 11.738 5.43379 11.738 4.08939C11.738 2.74489 12.8279 1.65479 14.1723 1.65479ZM26.3448 4.08939C26.3448 2.74489 25.2549 1.65479 23.9103 1.65479C22.5658 1.65479 21.4758 2.74489 21.4758 4.08939C21.4758 5.43379 22.5658 6.52389 23.9103 6.52389C25.2549 6.52389 26.3448 5.43379 26.3448 4.08939ZM4.43438 11.3927C5.77898 11.3927 6.86888 12.4828 6.86888 13.8272C6.86888 15.1717 5.77898 16.2618 4.43438 16.2618C3.08988 16.2618 1.99988 15.1717 1.99988 13.8272C1.99988 12.4828 3.08988 11.3927 4.43438 11.3927ZM16.6068 13.8272C16.6068 12.4828 15.5169 11.3927 14.1723 11.3927C12.8279 11.3927 11.738 12.4828 11.738 13.8272C11.738 15.1717 12.8279 16.2618 14.1723 16.2618C15.5169 16.2618 16.6068 15.1717 16.6068 13.8272ZM23.9103 11.3927C25.2549 11.3927 26.3448 12.4828 26.3448 13.8272C26.3448 15.1717 25.2549 16.2618 23.9103 16.2618C22.5658 16.2618 21.4758 15.1717 21.4758 13.8272C21.4758 12.4828 22.5658 11.3927 23.9103 11.3927ZM6.86888 23.5654C6.86888 22.2206 5.77898 21.1308 4.43438 21.1308C3.08988 21.1308 1.99988 22.2206 1.99988 23.5654C1.99988 24.9098 3.08988 25.9996 4.43438 25.9996C5.77898 25.9996 6.86888 24.9098 6.86888 23.5654ZM14.1723 21.1308C15.5169 21.1308 16.6068 22.2206 16.6068 23.5654C16.6068 24.9098 15.5169 25.9996 14.1723 25.9996C12.8279 25.9996 11.738 24.9098 11.738 23.5654C11.738 22.2206 12.8279 21.1308 14.1723 21.1308ZM26.3448 23.5654C26.3448 22.2206 25.2549 21.1308 23.9103 21.1308C22.5658 21.1308 21.4758 22.2206 21.4758 23.5654C21.4758 24.9098 22.5658 25.9996 23.9103 25.9996C25.2549 25.9996 26.3448 24.9098 26.3448 23.5654Z" fill="#96766D"/>
        </svg>

    </div>
    <div class="menu-background" >
        <div class="menu-background-inner">
            <?php
            $sub_contents='';
            echo wv_depth_menu('',$skin_id,$data,2);
            ?>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var $skin = $('<?php echo $skin_selector?>');
            var $menu_btn = $(".menu-btn",$skin);

            var $menu_background = $('.menu-background',$skin);

            $menu_btn.click(function () {

                if($menu_background.hasClass('show')){
                    $menu_background.removeClass('show');
                }else{
                    $menu_background.addClass('show');
                }


            })
        })
    </script>
</div>


