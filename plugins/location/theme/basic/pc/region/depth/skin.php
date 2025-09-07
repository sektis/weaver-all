<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

?>
<div id="<?php echo $skin_id?>" class="<?php echo $skin_class; ?> wv-skin-widget position-relative d-flex-center flex-nowrap"  style="<?php echo isset($data['margin_top'])?"margin-top::{$data['margin_top']};":''; ?>" >
    <style>
        <?php echo $skin_selector?> {}
        <?php echo $skin_selector?> .text1{
            color: #97989c;
            background-color: #f9f9f9;
            text-align: center;
            font-size: var(--wv-14);
            font-weight: 600;
            /* 142.857% */;
            letter-spacing: calc(var(--wv-0_56) * -1);
            height: var(--wv-42);
            display: flex;
            justify-content: center;
            align-items: center;
            padding:0 var(--wv-12);
        }
        <?php echo $skin_selector?> .text1.active{color:#0d171b;background-color: #fff;}
        <?php echo $skin_selector?> .depth2 .text1{color:#0d171b;background-color: #fff;}
        <?php echo $skin_selector?> .depth3 .text1{color:#0d171b;background-color: #fff;}
        <?php echo $skin_selector?>  .location-search-input {  letter-spacing: calc(var(--wv-0_56) * -1);}
        <?php echo $skin_selector?>  .location-search-input::placeholder {color: #cfcfcf;font-size: var(--wv-14);font-style: normal;font-weight: 500;line-height: var(-calc(var(--wv-14) * -1),var(--wv-20));letter-spacing: calc(var(--wv-0_56) * -1);}
        <?php echo $skin_selector?>  .skin-test >div{width: 100px;height: 100px;display: inline-block}

        <?php echo $skin_selector?>  .depth-scroll { overflow-y: auto;overflow-x: hidden;}
        <?php echo $skin_selector?>  .depth-scroll .text1{ cursor: pointer}
        <?php echo $skin_selector?>  .depth2 .text1{justify-content: start}
        <?php echo $skin_selector?>  .depth3 .text1{justify-content: space-between}
        <?php echo $skin_selector?>  .depth2 .text1.added{background-color: #fff6f6;color:#FF5F5A}
        <?php echo $skin_selector?>  .depth3 .text1.added{background-color: #fff6f6;color:#FF5F5A}
        <?php echo $skin_selector?>  .depth3 .text1.added .depth3-check{filter: invert(46%) sepia(61%) saturate(696%) hue-rotate(314deg) brightness(104%) contrast(111%);}
        <?php echo $skin_selector?>  .depth3 .depth3-check{width: var(--wv-20)}
        <?php echo $skin_selector?>  .depth-scroll::-webkit-scrollbar { width: 2px;}
        <?php echo $skin_selector?>  .depth-scroll::-webkit-scrollbar-thumb {background: #97989c;border-radius: 10px;}
        <?php echo $skin_selector?>  .depth-scroll::-webkit-scrollbar-track {background: #ededee}
        <?php echo $skin_selector?>  .favorite-towns-wrap {max-height: 0;transition: all .1s;overflow: hidden}
        <?php echo $skin_selector?>  .favorite-towns-wrap.active {max-height: 1000px;transition: all .9s}
        <?php echo $skin_selector?>  .favorite-save {background: #cfcfcf}
        <?php echo $skin_selector?>  .favorite-save.active {background: #0d171b}
        <?php echo $skin_selector?>  .favorite-towns {gap: var(--wv-8);row-gap: var(--wv-8);flex-wrap: wrap}
        <?php echo $skin_selector?>  .added-favorite-town {display: inline-flex;padding: var(--wv-6) var(--wv-8);align-items:center;justify-content:space-between;gap: var(--wv-10);border-radius: var(--wv-4);background: #0d171b;gap: var(--wv-13);
                                         font-size: var(--wv-14);cursor: pointer;color:#fff;font-weight: 500;letter-spacing: calc(var(--wv-0_56) * -1);}
        <?php echo $skin_selector?>  .location-search-result-wrap {    position: absolute;top: calc(100% + 3px);left: 0;width: 100%;max-height: 0;transition: all .9s;overflow: hidden; font-size: var(--wv-12);font-weight: 500}
        <?php echo $skin_selector?>  .location-search-result-wrap.active {max-height: 1000px; }
        <?php echo $skin_selector?>  .location-search-result-inner {    border: 1px solid #EFEFEF;border-radius: var(--wv-4);background: #fff}
        <?php echo $skin_selector?>  .location-search-result {padding: var(--wv-6);max-height: 35dvh;background: #fff;overflow-y: auto}
        <?php echo $skin_selector?>  .location-search-foot {padding: var(--wv-6);display: flex;border-top: 1px solid #efefef}
        @media (min-width: 992px) {

        }

        @media (max-width: 991.98px) {

        }
    </style>

    <div class="position-relative col col-lg-auto w-full md:w-full " style="background:#fff;border-radius: var(--wv-4) var(--wv-4) 0 0;;;">

        <div  class="vstack  " style="height: 90dvh; ">

            <div class="col-auto" >
                <div class=" hstack justify-content-between" style="padding: var(--wv-16) var(--wv-17)">
                    <p class="fs-[16//-0.64/700/#0D171B]">동네를 설정해주세요.</p>
                    <a href="" data-bs-dismiss="offcanvas"><img src="<?php echo $wv_skin_url; ?>/close.png" class="w-[28px]" alt=""></a>
                </div>
            </div>

            <div class="col-auto" style="padding: 0 var(--wv-16)">
                <div class="position-relative" style="z-index: 3">
                    <form action="" class="location-search-form">
                        <input type="text" class="form-control  location-search-input h-[43px] rounded-[4px] border-[1px] border-solid border-[#EFEFEF] outline-none p-[12px] bg-[#f9f9f9]"   placeholder="지역을 검색해보세요 (서울 강남구, 강남구 논현동 등)" />
                        <button class="btn p-0 outline-none position-absolute top-50 translate-middle-y end-[12px]"><img src="<?php echo $wv_skin_url; ?>/search.png" class="w-[20px]" alt=""></button>
                    </form>
                    <div class="location-search-result-wrap  ">
                        <div class="location-search-result-inner">
                            <div class="location-search-result" style="p">

                            </div>
                            <div class="location-search-foot" style="p">
                                <span href="" class="ms-auto fs-09em location-search-result-close cursor-pointer">닫기</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-[6px] hstack justify-content-end">
                    <a href="" class="hstack" style="padding: var(--wv-8) 0 var(--wv-8) var(--wv-14);align-items: center;gap: var(--wv-4);">
                        <img src="<?php echo $wv_skin_url; ?>/target.png" class="w-[12px]" alt="">
                        <span class="fs-[12/100%/-0.24/500/#989898] favorite-set-curr-location">현 위치로 설정하기</span>
                    </a>
                </div>
            </div>

            <div class="col mt-[12px] overflow-hidden"  >
                <div class="row g-0 h-100" style="overflow: hidden;padding:1px;"    >
                    <div class="col-auto h-100" style="width: var(--wv-80)">
                        <div class="vstack h-100">
                            <p class="text1 active wv-border-line col-auto" style="z-index: 1">시/도</p>
                            <div class="depth-scroll depth1 col">

                            </div>
                        </div>

                    </div>
                    <div class="col-auto h-100" style="width: var(--wv-140)">
                        <div class="vstack h-100">
                            <p class="text1 active wv-border-line col-auto" style="z-index: 1">시/구/군</p>
                            <div class="depth-scroll depth2 col">

                            </div>
                        </div>
                    </div>
                    <div class="col h-100">
                        <div class="vstack h-100">
                            <p class="text1 active wv-border-line col-auto" style="z-index: 1">동/읍/면</p>
                            <div class="depth-scroll depth3 col">

                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-auto">
                <div class="favorite-towns-wrap">
                    <div style="padding: var(--wv-11_5) var(--wv-16) var(--wv-16)">
                        <div class="hstack" style="gap:var(--wv-4);">
                            <p class="fs-[12//-0.48/600/#0D171B]">선택된 동네</p>
                            <div class="hstack fs-[12//-0.48/600/#97989C]">
                                <span class="favorite-towns-cnt-current text-[#FF5F5A]">0</span>
                                <span>/</span>
                                <span class="favorite-towns-cnt-max"></span>
                            </div>
                        </div>
                        <div class="hstack favorite-towns mt-[8.5px]">

                        </div>
                    </div>
                </div>
            </div>


            <div class="col-auto">
                <div class="hstack  " style="padding: var(--wv-12) var(--wv-16);gap:var(--wv-8)">
                    <a href="" class="wv-flex-box  w-[63px] h-[54px] col-auto favorite-reset" style="row-gap: var(--wv-2);border-radius: var(--wv-4);border: 1px solid #efefef;">
                        <div>
                            <img src="<?php echo $wv_skin_url; ?>/refresh.png" class="w-[22px]" alt="">
                            <p class="fs-[12//-0.48/600/#97989C]">초기화</p>
                        </div>
                    </a>
                    <a href="" class="wv-flex-box col h-[54px] favorite-save   fs-[16//-0.64/700/#FFF]" style="border-radius: var(--wv-4);gap:0">
                        <span class="favorite-towns-cnt-current"></span>개 동네 설정하기
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            var $root = $("<?php echo $skin_selector?>");
            var favorite_max_count = '<?php echo wv()->location->get_favorite_max_count()?>';

            // --------------------------
            // 카운트 갱신 & 최대치 표시
            // --------------------------
            function updateFavoriteCount() {
                var count = $root.find('.added-favorite-town').length;
                $root.find('.favorite-towns-cnt-current').text(count);
                var hasAny = count > 0;
                $root.find('.favorite-towns-wrap').toggleClass('active', hasAny);
                $root.find('.favorite-save').toggleClass('active', hasAny);
            }
            $root.find('.favorite-towns-cnt-max').text(favorite_max_count);
            updateFavoriteCount();

            // --------------------------
            // API 헬퍼
            // --------------------------
            function api(params, cb) {
                $.post(
                    '<?php echo wv()->location->ajax_url() . '?wv_location_action=api'; ?>',
                    params,
                    function (res) { if (res && res.ok) cb(res); },
                    'json'
                );
            }

            // --------------------------
            // 서버에서 받은 즐겨찾기 목록을 배지로 주입
            // --------------------------
            function hydrateFavorites(list) {
                if (!Array.isArray(list)) return;
                var $favoriteWrap = $root.find('.favorite-towns');

                list.forEach(function (row) {
                    var addr = row && row.address ? row.address : {};
                    var d1 = (addr.region_1depth_name || '').trim();
                    var d2 = normalizeD2Name(addr.region_2depth_name);
                    var d3 = (addr.region_3depth_name || '').trim();
                    if (!d1 || !d3) return;

                    // 중복 방지
                    var dup = $favoriteWrap.find('.added-favorite-town').filter(function () {
                        var $ft = $(this);
                        return $ft.data('depth1-name') === d1 &&
                            $ft.data('depth2-name') === d2 &&
                            $ft.data('depth3-name') === d3;
                    }).length > 0;
                    if (dup) return;

                    // 배지 생성
                    var $fav = $('<div class="added-favorite-town"></div>')
                        .attr({
                            'data-depth1-name': d1,
                            'data-depth2-name': d2,
                            'data-depth3-name': d3
                        })
                        .append('<span>' + d3 + '</span>')
                        .append('<img src="<?php echo $wv_skin_url; ?>/delete.png" class="w-[16px]" alt="">');

                    $favoriteWrap.append($fav);
                });

                updateFavoriteCount();
            }

            // --------------------------
            // 리스트 렌더링 (렌더 완료 이벤트 포함)
            // --------------------------
            function renderItems($box, items, opt) {
                $box.empty();
                if (!items || !items.length) {
                    $box.append('<p class="text1">데이터 없음</p>');
                    setTimeout(function () { $box.trigger('wv:rendered'); }, 0);
                    return;
                }

                var isD1 = $box.hasClass('depth1');
                var isD2 = $box.hasClass('depth2');
                var isD3 = $box.hasClass('depth3');

                items.forEach(function (it) {
                    var text = (typeof it === 'string') ? it : (it.name || '');
                    var code = (typeof it === 'string') ? '' : (it.code || '');
                    // depth3용: API의 depth2_name 우선 사용 (없으면 d2_name → sel.d2)
                    var d2NameFromApi = (typeof it === 'object' && it) ? (it.depth2_name || it.d2_name || '') : '';

                    var $p = $('<p class="text1"></p>')
                        .text(text)
                        .attr('data-name', text);

                    if (code) $p.attr('data-code', code);
                    if (isD3) $p.append('<img src="<?php echo $wv_skin_url?>/check.png" class="depth3-check">');

                    if (isD1) {
                        $p.attr({'data-depth1-name': text});
                    } else if (isD2) {
                        $p.attr({
                            'data-depth1-name': (typeof sel !== 'undefined' && sel.d1) ? sel.d1 : '',
                            'data-depth2-name': text
                        });
                    } else if (isD3) {
                        $p.attr({
                            'data-depth1-name': (typeof sel !== 'undefined' && sel.d1) ? sel.d1 : '',
                            'data-depth2-name': normalizeD2Name(d2NameFromApi || ((typeof sel !== 'undefined' && sel.d2) ? sel.d2 : '')),
                            'data-depth3-name': text
                        });
                    }

                    if (opt && opt.click) $p.on('click', opt.click);
                    $box.append($p);
                });

                setTimeout(function () { $box.trigger('wv:rendered'); }, 0);
            }

            // --------------------------
            // 선택 상태 표시
            // --------------------------
            function setActive($scope, nameOrCode) {
                $scope.find('.text1').removeClass('active');
                $scope.find('.text1').each(function () {
                    var $t = $(this);
                    if ($t.data('name') === nameOrCode || $t.data('code') === nameOrCode) {
                        $t.addClass('active');
                    }
                });
            }

            // --------------------------
            // 요소/컨테이너 참조
            // --------------------------
            var $d1 = $root.find('.depth1');
            var $d2 = $root.find('.depth2');
            var $d3 = $root.find('.depth3');
            var sel = {d1: null, d2: null, code: null};

            // --------------------------
            // 유틸
            // --------------------------
            function once(fn) {
                var called = false;
                return function () { if (called) return; called = true; return fn.apply(this, arguments); };
            }
            function waitUntil(checkFn, cb, timeoutMs) {
                var started = Date.now();
                (function loop() {
                    if (checkFn()) return cb(true);
                    if (Date.now() - started > (timeoutMs || 2000)) return cb(false);
                    setTimeout(loop, 50);
                })();
            }
            function scrollIntoViewWithin($container, $item, pad) {
                if (!$container.length || !$item.length) return;
                pad = (typeof pad === 'number') ? pad : 6;
                var ch = $container.innerHeight();
                var st = $container.scrollTop();
                var it = $item.position().top;
                var ih = $item.outerHeight();
                var inViewTop = it >= pad;
                var inViewBottom = (it + ih) <= (ch - pad);
                if (!inViewTop) {
                    $container.stop(true).animate({scrollTop: st + it - pad}, 200);
                } else if (!inViewBottom) {
                    var delta = (it + ih) - (ch - pad);
                    $container.stop(true).animate({scrollTop: st + delta}, 200);
                }
            }
            function canAddMore() {
                var maxCnt = Number(favorite_max_count) || 0;
                if (maxCnt <= 0) return true;
                return $root.find('.added-favorite-town').length < maxCnt;
            }

            // --------------------------
            // 즐겨찾기 추가 헬퍼
            // --------------------------
            function addFavoriteTownByNames(d1Name, d2Name, d3Name) {
                var $favoriteWrap = $root.find('.favorite-towns');
                var maxCnt = Number(favorite_max_count) || 0;

                var isDup = $favoriteWrap.find('.added-favorite-town').filter(function () {
                    var $ft = $(this);
                    return $ft.data('depth1-name') === d1Name &&
                        $ft.data('depth2-name') === d2Name &&
                        $ft.data('depth3-name') === d3Name;
                }).length > 0;
                if (isDup) return false;

                if (maxCnt > 0 && $favoriteWrap.find('.added-favorite-town').length >= maxCnt) {
                    alert('최대 ' + maxCnt + '개까지 선택할 수 있습니다.');
                    return false;
                }

                var $fav = $('<div class="added-favorite-town"></div>')
                    .attr({
                        'data-depth1-name': d1Name,
                        'data-depth2-name': d2Name,
                        'data-depth3-name': d3Name
                    })
                    .append('<span>' + d3Name + '</span>')
                    .append('<img src="<?php echo $wv_skin_url; ?>/delete.png" class="w-[16px]" alt="">');

                $favoriteWrap.append($fav);
                updateFavoriteCount();

                // 현재 보이는 목록에 즉시 반영(있을 때만)
                $root.find('.depth1 .text1').filter(function () {
                    return $(this).data('depth1-name') === d1Name;
                }).addClass('added');

                $root.find('.depth2 .text1').filter(function () {
                    return $(this).data('depth1-name') === d1Name &&
                        $(this).data('depth2-name') === d2Name;
                }).addClass('added');

                $root.find('.depth3 .text1').filter(function () {
                    return $(this).data('depth1-name') === d1Name &&
                        $(this).data('depth2-name') === d2Name &&
                        $(this).data('depth3-name') === d3Name;
                }).addClass('added');

                // 부모 토글(배지 기준)
                var hasAnyAddedInCurrentD3 = $root.find('.added-favorite-town').filter(function () {
                    var $ft = $(this);
                    return $ft.data('depth1-name') === d1Name &&
                        $ft.data('depth2-name') === d2Name;
                }).length > 0;

                $root.find('.depth2 .text1').filter(function () {
                    return $(this).data('depth1-name') === d1Name &&
                        $(this).data('depth2-name') === d2Name;
                }).toggleClass('added', hasAnyAddedInCurrentD3);

                var hasAnyAddedInCurrentD2 = $root.find('.added-favorite-town').filter(function () {
                    var $ft = $(this);
                    return $ft.data('depth1-name') === d1Name;
                }).length > 0;

                $root.find('.depth1 .text1').filter(function () {
                    return $(this).data('depth1-name') === d1Name;
                }).toggleClass('added', hasAnyAddedInCurrentD2);

                return true;
            }

            // --------------------------
            // 자동 이동 헬퍼 (depth1→2→3)
            // --------------------------
            function gotoDepth(d1Name, d2Name, d3Name, opt) {
                opt = $.extend({ensureAdded: false, highlightOnly: false}, opt || {});
                var $d1Item = $d1.find('.text1').filter(function () {
                    return $(this).data('depth1-name') === d1Name;
                }).first();
                if (!$d1Item.length) return;

                var handleD3 = function () {
                    if (!d3Name) return;
                    var $d3Item = $d3.find('.text1').filter(function () {
                        return $(this).data('depth1-name') === d1Name &&
                            $(this).data('depth2-name') === d2Name &&
                            $(this).data('depth3-name') === d3Name;
                    }).first();
                    if ($d3Item.length) {
                        scrollIntoViewWithin && scrollIntoViewWithin($d3, $d3Item, 6);
                        if (opt.ensureAdded) {
                            if (!$d3Item.hasClass('added')) {
                                if (canAddMore()) $d3Item.trigger('click');
                                else setActive($d3, $d3Item.data('code') || d3Name);
                            } else {
                                setActive($d3, $d3Item.data('code') || d3Name);
                            }
                        } else if (opt.highlightOnly) {
                            setActive($d3, $d3Item.data('code') || d3Name);
                        }
                        return true;
                    }
                    return false;
                };

                var handleD2 = function (done) {
                    var $d2Item = $d2.find('.text1').filter(function () {
                        return $(this).data('depth1-name') === d1Name &&
                            $(this).data('depth2-name') === d2Name;
                    }).first();

                    if (isD2LoadedFor(d1Name) && $d2Item.length) {
                        scrollIntoViewWithin && scrollIntoViewWithin($d2, $d2Item, 6);

                        if (isD3LoadedFor(d1Name, d2Name)) {
                            handleD3() || (function () {
                                var proceedD3 = once(function () { handleD3(); });
                                $d3.one('wv:rendered', proceedD3);
                                waitUntil(function () { return isD3LoadedFor(d1Name, d2Name); }, function (ok) {
                                    if (ok) proceedD3();
                                }, 2000);
                            })();
                            return done && done();
                        }

                        var proceedD3After = once(function () {
                            handleD3() || (function () {
                                var proceedD3 = once(function () { handleD3(); });
                                $d3.one('wv:rendered', proceedD3);
                                waitUntil(function () { return isD3LoadedFor(d1Name, d2Name); }, function (ok) {
                                    if (ok) proceedD3();
                                }, 2000);
                            })();
                        });

                        $d3.one('wv:rendered', proceedD3After);
                        waitUntil(function () { return isD3LoadedFor(d1Name, d2Name); }, function (ok) {
                            if (ok) proceedD3After();
                        }, 2000);

                        $d2Item.trigger('click');
                        return done && done();
                    }

                    var proceedD2 = once(function () { handleD2(done); });
                    $d2.one('wv:rendered', proceedD2);
                    waitUntil(function () { return isD2LoadedFor(d1Name); }, function (ok) {
                        if (ok) proceedD2();
                    }, 2000);
                };

                if (isD2LoadedFor(d1Name)) { handleD2(); return; }
                var proceedD2First = once(function () { handleD2(); });
                $d2.one('wv:rendered', proceedD2First);
                waitUntil(function () { return isD2LoadedFor(d1Name); }, function (ok) {
                    if (ok) proceedD2First();
                }, 2000);
                $d1Item.trigger('click');
            }

            function isD2LoadedFor(d1Name) {
                return $d2.find('.text1').filter(function () {
                    return $(this).data('depth1-name') === d1Name;
                }).length > 0;
            }
            function isD3LoadedFor(d1Name, d2Name) {
                return $d3.find('.text1').filter(function () {
                    return $(this).data('depth1-name') === d1Name &&
                        $(this).data('depth2-name') === d2Name;
                }).length > 0;
            }
            function normalizeD2Name(s) {
                s = (s || '').trim();
                // '전체'나 빈 값은 모두 ""로 통일
                if (!s || s === '전체') return '';
                return s;
            }

            // --------------------------
            // depth 목록 로드(원래 depth1~3 전체 로직)
            // --------------------------
            function loadDepthLists() {
                api({a: 'depth1', sort: 'code'}, function (data) {
                    renderItems($d1, data.depth1, {
                        click: function () {
                            sel.d1 = $(this).data('name');
                            sel.d2 = null; sel.code = null;
                            setActive($d1, sel.d1);

                            // depth2 로드
                            api({a: 'depth2', d1: sel.d1, sort: 'code'}, function (res2) {
                                renderItems($d2, res2.depth2, {
                                    click: function () {
                                        sel.d2 = $(this).data('name');
                                        setActive($d2, sel.d2);

                                        // depth3 로드
                                        api({a: 'depth3', d1: sel.d1, d2: sel.d2, sort: 'code'}, function (res3) {
                                            renderItems($d3, res3.items, {
                                                click: function () {
                                                    sel.code = $(this).data('code');
                                                    setActive($d3, sel.code);

                                                    var $this = $(this);
                                                    var d1Name = $this.data('depth1-name');
                                                    var realD2Name = normalizeD2Name($this.data('depth2-name'));
                                                    var d3Name = $this.data('depth3-name');
                                                    var $favoriteWrap = $root.find('.favorite-towns');

                                                    if ($this.hasClass('added')) {
                                                        // 제거
                                                        $this.removeClass('added');
                                                        $favoriteWrap.find('.added-favorite-town').filter(function () {
                                                            var $ft = $(this);
                                                            return $ft.data('depth1-name') === d1Name &&
                                                                $ft.data('depth2-name') === realD2Name &&
                                                                $ft.data('depth3-name') === d3Name;
                                                        }).remove();
                                                        updateFavoriteCount();
                                                    } else {
                                                        // 추가 전 개수 제한 확인
                                                        if (!canAddMore()) {
                                                            alert('최대 ' + favorite_max_count + '개까지 선택할 수 있습니다.');
                                                            return;
                                                        }
                                                        $this.addClass('added');
                                                        var $fav = $('<div class="added-favorite-town"></div>')
                                                            .attr({
                                                                'data-depth1-name': d1Name,
                                                                'data-depth2-name': realD2Name,
                                                                'data-depth3-name': d3Name
                                                            })
                                                            .append('<span>' + d3Name + '</span>')
                                                            .append('<img src="<?php echo $wv_skin_url; ?>/delete.png" class="w-[16px]" alt="">');
                                                        $favoriteWrap.append($fav);
                                                        updateFavoriteCount();
                                                    }

                                                    // 부모 depth2/1 added 토글 (배지 기준)
                                                    var remainingD2 = $root.find('.added-favorite-town').filter(function () {
                                                        var $ft2 = $(this);
                                                        return $ft2.data('depth1-name') === d1Name &&
                                                            $ft2.data('depth2-name') === realD2Name;
                                                    }).length > 0;

                                                    var $parentD2 = $d2.find('.text1').filter(function () {
                                                        return $(this).data('depth1-name') === d1Name &&
                                                            $(this).data('depth2-name') === realD2Name;
                                                    });
                                                    $parentD2.toggleClass('added', remainingD2);

                                                    var remainingD1 = $root.find('.added-favorite-town').filter(function () {
                                                        return $(this).data('depth1-name') === d1Name;
                                                    }).length > 0;

                                                    var $parentD1 = $d1.find('.text1').filter(function () {
                                                        return $(this).data('depth1-name') === d1Name;
                                                    });
                                                    $parentD1.toggleClass('added', remainingD1);
                                                }
                                            });

                                            // ★ 복원: 배지 기준
                                            var favList = [];
                                            $root.find('.added-favorite-town').each(function () {
                                                favList.push({
                                                    d1: $(this).data('depth1-name'),
                                                    d2: $(this).data('depth2-name'),
                                                    d3: $(this).data('depth3-name')
                                                });
                                            });

                                            // depth3 복원
                                            $d3.find('.text1').each(function () {
                                                var $item = $(this);
                                                var match = favList.some(function (f) {
                                                    return f.d1 === $item.data('depth1-name') &&
                                                        f.d2 === $item.data('depth2-name') &&
                                                        f.d3 === $item.data('depth3-name');
                                                });
                                                if (match) $item.addClass('added');
                                            });

                                            // depth2 복원
                                            $d2.find('.text1').each(function () {
                                                var $item = $(this);
                                                var match = favList.some(function (f) {
                                                    return f.d1 === $item.data('depth1-name') &&
                                                        f.d2 === $item.data('depth2-name');
                                                });
                                                if (match) $item.addClass('added');
                                            });

                                            // depth1 복원
                                            $d1.find('.text1').each(function () {
                                                var $item = $(this);
                                                var match = favList.some(function (f) { return f.d1 === $item.data('depth1-name'); });
                                                if (match) $item.addClass('added');
                                            });
                                        });
                                    }
                                });

                                // depth2가 '전체' 하나뿐이면 자동 선택해서 depth3 로드
                                $d2.one('wv:rendered', function () {
                                    var $items = $d2.find('.text1');
                                    if ($items.length === 1 &&
                                        ((($items.first().data('name') || '').trim() === '전체') ||
                                            (($items.first().text() || '').trim() === '전체'))) {
                                        $items.first().trigger('click');
                                    }
                                });

                                // depth2 복원(배지 기준)
                                var favList2 = [];
                                $root.find('.added-favorite-town').each(function () {
                                    favList2.push({
                                        d1: $(this).data('depth1-name'),
                                        d2: $(this).data('depth2-name')
                                    });
                                });
                                $d2.find('.text1').each(function () {
                                    var $item = $(this);
                                    var match = favList2.some(function (f) {
                                        return f.d1 === $item.data('depth1-name') &&
                                            f.d2 === $item.data('depth2-name');
                                    });
                                    if (match) $item.addClass('added');
                                });

                                $d3.empty(); // depth3 초기화
                            });
                        }
                    });

                    // depth1 복원(배지 기준)
                    var favList1 = [];
                    $root.find('.added-favorite-town').each(function () {
                        favList1.push($(this).data('depth1-name'));
                    });
                    $d1.find('.text1').each(function () {
                        var $item = $(this);
                        if (favList1.includes($item.data('depth1-name'))) $item.addClass('added');
                    });
                });
            }

            // --------------------------
            // 배지 클릭 → 삭제 동기화
            // --------------------------
            $root.on('click', '.added-favorite-town', function () {
                var $ft = $(this);
                var d1 = $ft.data('depth1-name');
                var d2 = $ft.data('depth2-name');
                var d3 = $ft.data('depth3-name');

                $ft.remove();
                updateFavoriteCount();

                // 현재 보이는 depth3에서만 시각적 해제
                $d3.find('.text1').filter(function () {
                    return $(this).data('depth1-name') === d1 &&
                        $(this).data('depth2-name') === d2 &&
                        $(this).data('depth3-name') === d3;
                }).removeClass('added');

                // 부모 added 토글(배지 기준)
                var remainingD2 = $root.find('.added-favorite-town').filter(function () {
                    var $ft2 = $(this);
                    return $ft2.data('depth1-name') === d1 &&
                        $ft2.data('depth2-name') === d2;
                }).length > 0;

                var $parentD2 = $d2.find('.text1').filter(function () {
                    return $(this).data('depth1-name') === d1 &&
                        $(this).data('depth2-name') === d2;
                });
                $parentD2.toggleClass('added', remainingD2);

                var remainingD1 = $root.find('.added-favorite-town').filter(function () {
                    return $(this).data('depth1-name') === d1;
                }).length > 0;

                var $parentD1 = $d1.find('.text1').filter(function () {
                    return $(this).data('depth1-name') === d1;
                });
                $parentD1.toggleClass('added', remainingD1);
            });

            // --------------------------
            // 현 위치로 설정하기
            // --------------------------
            $root.on('click', '.favorite-set-curr-location', function (e) {
                e.preventDefault();
                wv_get_current_location(function (result) {
                    try {
                        if (!result || !result.address) return;

                        var d1 = (wv_trans_sido(result.address.region_1depth_name) || '').trim();
                        var d2 = normalizeD2Name(result.address.region_2depth_name || '');
                        var d3 = (result.address.region_3depth_name || '').trim();
                        if (!d1 || !d3) return;

                        // ✅ 이미 추가된 경우: 알림 후 종료
                        if (isAlreadyAdded(d1, d2, d3)) {
                            alert('이미 추가된 동네입니다.');
                            return; // 추가/이동/스크롤 모두 중단
                        }

                        // 정원 초과 사전 체크
                        if (!canAddMore()) {
                            alert('최대 ' + favorite_max_count + '개까지 선택할 수 있습니다.');
                            return;
                        }

                        var ok = addFavoriteTownByNames(d1, d2, d3);
                        if (ok) gotoDepth(d1, d2, d3, { ensureAdded: true });

                    } catch (err) {
                        console.error(err);
                    }
                });
            });

            // --------------------------
            // 초기화
            // --------------------------
            $root.on('click', '.favorite-reset', function (e) {
                e.preventDefault();
                $root.find('.favorite-towns .added-favorite-town').remove();
                $d3.find('.text1.added').removeClass('added');
                $d2.find('.text1.added').removeClass('added');
                $d1.find('.text1.added').removeClass('added');
                updateFavoriteCount();
            });

            // --------------------------
            // 저장
            // --------------------------
            $root.on('click', '.favorite-save', function (e) {
                e.preventDefault();

                var payload = [];
                $root.find('.added-favorite-town').each(function () {
                    var d1 = ($(this).data('depth1-name') || '').trim();
                    var d2Raw = ($(this).data('depth2-name') || '').trim();
                    var d3 = ($(this).data('depth3-name') || '').trim();

                    // ✅ d2가 '전체'면 빈값으로 저장
                    var d2 = (d2Raw === '전체') ? '' : d2Raw;

                    // ✅ d2는 비어도 OK (세종 등)
                    if (d1 && d3) {
                        payload.push({
                            address: {
                                region_1depth_name: d1,
                                region_2depth_name: d2,
                                region_3depth_name: d3
                            }
                        });
                    }
                });

                if (!payload.length) { alert('선택된 동네가 없습니다.'); return; }

                var $btn = $(this);
                if ($btn.data('busy')) return;
                $btn.data('busy', true);

                $.ajax({
                    url: '<?php echo wv()->location->ajax_url() ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        wv_location_action: 'set',
                        wv_location_name: 'favorite',
                        wv_location_data: payload
                    }
                })
                    .done(function (res) { alert('저장완료');location.reload() })
                    .fail(function () { alert('통신 오류가 발생했습니다.'); })
                    .always(function () { $btn.data('busy', false); });
            });

            // --------------------------
            // 초기 진입: 서버 GET → 배지 주입 → depth 목록 로드
            // --------------------------
            $.ajax({
                url: '<?php echo wv()->location->ajax_url() ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    wv_location_action: 'get',
                    wv_location_name: 'favorite'
                }
            })
                .done(function (res) {
                    if (Array.isArray(res)) hydrateFavorites(res);
                    else if (res && Array.isArray(res.data)) hydrateFavorites(res.data);
                })
                .always(function () {
                    loadDepthLists(); // 배지를 기준으로 목록 복원/added 토글 자동 반영
                });


            function renderSearchResultList(list){
                var $wrap = $root.find('.location-search-result-wrap');
                var $box  = $wrap.find('.location-search-result');
                $box.empty();

                if (!list.length) {
                    $box.append('<div class="px-2 py-2 text-muted">검색 결과가 없습니다.</div>');
                    $wrap.addClass('active');
                    return;
                }

                list.forEach(function(row){
                    // 라벨: "시/도 (시/구/군) 동/읍/면" - d2 비면 생략
                    var label = row.d1 + ' ' + (row.d2 ? (row.d2 + ' ') : '') + row.d3;

                    var $a = $('<a href="" class="list-item d-block py-2 px-2 border-bottom" style="cursor:pointer;"></a>')
                        .text(label)
                        .attr({
                            'data-d1': row.d1,
                            'data-d2': row.d2, // 이미 normalize 된 값('' 또는 실제 구/군)
                            'data-d3': row.d3
                        });

                    $box.append($a);
                });

                $wrap.addClass('active');
            }

            function normalizeSearchResponse(res){
                // 가능한 배열 후보 추출
                var arr = [];
                if (Array.isArray(res)) arr = res;
                else if (Array.isArray(res.items)) arr = res.items;
                else if (Array.isArray(res.data)) arr = res.data;
                else if (Array.isArray(res.list)) arr = res.list;
                else if (res && res.ok && Array.isArray(res.result)) arr = res.result;

                // 각 요소를 {d1,d2,d3}로 정규화
                return (arr || []).map(function(it){
                    // address 중첩 형태 우선
                    var a = it.address || it.addr || {};
                    var d1 = a.region_1depth_name || it.region_1depth_name || it.d1 || it.sido || it.depth1 || it.depth1_name || '';
                    var d2 = a.region_2depth_name || it.region_2depth_name || it.d2 || it.sigungu || it.depth2 || it.depth2_name || '';
                    var d3 = a.region_3depth_name || it.region_3depth_name || it.d3 || it.dong || it.depth3 || it.depth3_name || it.name || '';

                    return {
                        d1: (d1 || '').trim(),
                        d2: normalizeD2Name(d2),
                        d3: (d3 || '').trim()
                    };
                }).filter(function(row){ return row.d1 && row.d3; }); // d3 필수
            }
            $root.on('submit', '.location-search-form', function(e){
                e.preventDefault();
                var q = $('.location-search-input', $root).val().trim();
                if (!q) {
                    $root.find('.location-search-result').empty();
                    $root.find('.location-search-result-wrap').removeClass('active');
                    return;
                }

                $.ajax({
                    url: '<?php echo wv()->location->ajax_url() ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        wv_location_action: 'api',
                        a: 'search',
                        q: q
                    }
                }).done(function(res){
                    var list = normalizeSearchResponse(res);
                    renderSearchResultList(list);
                }).fail(function(){
                    renderSearchResultList([]); // 실패 시 빈 결과 표시
                });
            });

// 결과 아이템 클릭 → 즐겨찾기 추가 & (필요 시) depth1~3 자동 이동
            $root.on('click', '.location-search-result .list-item', function(e){
                e.preventDefault();

                var d1 = ($(this).data('d1') || '').trim();
                var d2 = normalizeD2Name($(this).data('d2') || '');
                var d3 = ($(this).data('d3') || '').trim();
                if (!d1 || !d3) return;

                // ✅ 이미 추가된 경우: 알림 후 즉시 종료 (이동/스크롤 안함)
                if (isAlreadyAdded(d1, d2, d3)) {
                    alert('이미 추가된 동네입니다.');
                    $root.find('.location-search-result-wrap').removeClass('active');
                    return;
                }

                // 정원 초과 사전 체크
                if (!canAddMore()) {
                    alert('최대 ' + favorite_max_count + '개까지 선택할 수 있습니다.');
                    return; // 이동/스크롤 금지
                }

                // 정상 추가
                var ok = addFavoriteTownByNames(d1, d2, d3);
                if (ok) {
                    gotoDepth(d1, d2, d3, { ensureAdded: true });
                }

                $root.find('.location-search-result-wrap').removeClass('active');
                $root.find('.location-search-input').val('');
            });
            $root.on('click', '.location-search-result-close', function(e){
                $root.find('.location-search-result-wrap').removeClass('active');
            });
;
            function isAlreadyAdded(d1, d2, d3){
                return $root.find('.added-favorite-town').filter(function(){
                    var $ft = $(this);
                    return $ft.data('depth1-name') === d1 &&
                        $ft.data('depth2-name') === d2 &&
                        $ft.data('depth3-name') === d3;
                }).length > 0;
            }

         });
    </script>






</div>