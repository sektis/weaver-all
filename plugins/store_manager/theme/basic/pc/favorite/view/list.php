<?php
global $g5;




$result = wv()->store_manager->made('contract_item')->get_list(array(
    'where_contractitem' => array(
        array('is_free' => '=0')
    ),
    'order_by'=>'wr_id asc'
));
$cont_items = $result['list'];

?>
<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin position-relative h-100 flex-nowrap bg-white" style="<?php echo isset($data['margin_top']) ? "margin-top::{$data['margin_top']};" : ''; ?>">
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> [data-bs-toggle]{position: relative}
        <?php echo $skin_selector?> [data-bs-toggle]:not(.active){filter: grayscale(1);opacity: .6}
        @media (min-width: 992px) {
        }

        @media (max-width: 991.98px) {
        }
    </style>

    <div class="position-relative col col-lg-auto  md:w-full h-100 " style="">
        <div class="container h-100">

                <div class="vstack h-100 pt-[10px]" style="">
                    <div class="wv-offcanvas-header col-auto">
                        <div class=" ">
                            <div class="row align-items-center g-0">
                                <div class="col">
                                    <div data-bs-dismiss="offcanvas" class="cursor-pointer"><img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_left.png" class="w-[28px]" alt=""></div>
                                </div>
                                <div class="col-auto text-center">
                                    <p class="fs-[14/20/-0.56/600/#0D171B]">찜한 가게</p>
                                </div>
                                <div class="col"></div>
                            </div>
                        </div>
                    </div>

                    <div class="wv-mx-fit" style="height: 2px;background-color: #efefef"></div>

                    <div class="wv-offcanvas-body col" style="padding: 0">
                        <div class="hstack justify-content-end h-[48px]" style="">

                            <div class="wv-dropdown-select">
                                <button type="button" class=" btn" data-bs-toggle="dropdown"  >
                  <span class="hstack" style="gap:var(--wv-6)">
                       <span class="dropdown-label fs-[14/100%/-0.56/500/#0D171B]"></span>
                    <img src="<?php echo $this->manager->plugin_url; ?>/img/arrow_down.png" class="w-[13px]" alt="">
                  </span>
                                </button>
                                <ul class="dropdown-menu " style="width: max-content;">
                                    <div class="vstack align-items-start" style="padding: var(--wv-15) var(--wv-15);row-gap: var(--wv-10)">
                                        <a class="  fs-[14/100%/-0.56/500/#0D171B] px-0 text-center" data-order-value="near" <?php echo $order=='near'?'selected':''; ?> href="#"> <img src="<?php echo $this->manager->plugin_url; ?>/img/near.png" class="w-[13px]" alt=""> 가까운 순</a>
                                    </div>
                                </ul>
                            </div>
                        </div>
                        <div class="wv-mx-fit">
                            <div class="hstack menu-tab-top" role="tablist">
                                <?php $i=0; foreach ($cont_items as $cont_item){
                                    ?>
                                    <a href="#"   class="<?php echo $i==0?'active':''; ?> fs-[14/20/-0.56/600/] col transition h-[42px] d-flex-center" data-bs-toggle="tab" data-bs-target="#cont-<?php echo $cont_item['wr_id']; ?>" style="<?php echo $cont_item['contractitem']['color_type_text'];?>">
                                        <?php echo $cont_item['contractitem']['item_name_montserrat']; ?>
                                        <span class="cont-under-line position-absolute   start-0 w-100 h-[2px]" style="bottom:-1px;background-color: <?php echo $cont_item['contractitem']['color_type']['text']; ?>" ></span>
                                    </a>
                                    <?php $i++;}?>
                            </div>
                            <div class="wv-mx-fit" style="height: 1px;background-color: #efefef"></div>
                            <div class="tab-content menu-tab-content " id="myTabContent">
                                <?php $i=0; foreach ($cont_items as $cont_item){
                                    $opts = array(
                                        'where_contract' => array(
                                            'and'=>array(
                                                array('contractitem_wr_id' => "={$cont_item['wr_id']}"),
                                                array('status' => "=1"),
                                            )
                                        ),
                                        'select_store'=>array('list_each'=>array('contractitem_wr_id'=>$cont_item['wr_id']),'service'),
                                        'join'  =>  array(
                                            array(
                                                'table'  => wv()->store_manager->made('favorite_store')->get_list_table_name('favorite'),
                                                'on_from'     => 'wr_id',
                                                'on_to'     => 'store_wr_id',
                                                'select' => '*',
                                                'type'   => 'right',
                                                'where'=>array(
                                                        array('store_wr_id'=>' is not null')

                                                )
                                            )
                                        ),
                                    );
                                    $center = wv()->location->get('current');
                                    $opts = wv_make_distance_options($center['lat'],$center['lng'],$opts);
                                    if($order and $order!='near'){
                                        $opts['order_by'] = $order;

                                    }
                                    $result = wv()->store_manager->made('sub01_01')->get_list($opts);
                                    $store_list = $result['list'];

                                    ?>
                                    <div class="tab-pane fade <?php echo $i==0?'show active':''; ?> " id="cont-<?php echo $cont_item['wr_id']; ?>" >
                                        <div class="tab-pane-inner  ">
                                            <?php foreach ($store_list as $each){?>

                                                <div style="padding: var(--wv-12) var(--wv-10) var(--wv-20)">
                                                    <?php echo $each['store']['list_each'];; ?>

                                                </div>
                                                <div class="wv-mx-fit" style="border-top: 6px solid #efefef"></div>
                                            <?php }?>
                                        </div>
                                    </div>
                                    <?php $i++;}?>

                            </div>

                        </div>

                    </div>

                </div>

    </div>

    <script>
        $(document).ready(function () {
            var $skin = $("<?php echo $skin_selector?>");
            $("form", $skin).ajaxForm({
                // reload: false,
                // reload_ajax:true,
                // success: function () {
                //     var $offcanvas = $skin.closest('.wv-offcanvas');
                //     $offcanvas.offcanvas('hide');
                // }
            })

            var searchData = <?php echo json_encode($vars)?>;
            $(".wv-dropdown-select",$skin).on('wv.dropdown.change',function (e) {
                var order_value = $("a.selected",$(this)).data('order-value');

                var listData = $.extend({}, searchData, {order: order_value});
                wv_ajax('<?php echo wv()->store_manager->ajax_url?>','offcanvas,end,backdrop,class: w-[360px],replace_with:<?php echo $skin_selector?>',listData)
            })
        })
    </script>
</div>