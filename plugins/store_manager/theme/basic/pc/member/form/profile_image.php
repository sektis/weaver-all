<div id="<?php echo $skin_id ?>" class="<?php echo $skin_class; ?> wv-part-skin" style="">
    <style>
        <?php echo $skin_selector; ?> { padding: var(--wv-24) 0; }
        <?php echo $skin_selector; ?> .profile-image-wrapper { position: relative; width: 100px; height: 100px; margin: 0 auto; }
        <?php echo $skin_selector; ?> .profile-image-wrapper img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        <?php echo $skin_selector; ?> .profile-image-label { display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; border-radius: 50%; background: #F5F5F5; cursor: pointer; overflow: hidden; }

    </style>

    <?php
    $ajax_data['action']='update_render';
    ?>

    <div class="text-center">
        <div class="profile-image-wrapper">
            <input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $row[$column]['id']; ?>">
            <label class="profile-image-label">
                <input type="file" name="<?php echo $field_name; ?>" accept="image/*" class="d-none">
                <img src="<?php echo $row['pf_img'] ?>" alt="사진">
            </label>
        </div>
        <div class="fs-[12/17/-0.48/500/#97989C] mt-[6px] wv-flex-box" style="gap:var(--wv-8)">
            <span>사진 편집</span>
            <i class="fa-solid fa-chevron-right lh-0 fs-07em"></i>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            var $skin = $("<?php echo $skin_selector?>");

            $("[type=file]",$skin).change(function () {
                var file = this.files[0];

                if (!file) return;

                if (!file.type.match('image.*')) {
                    alert('이미지 파일만 업로드 가능합니다.');
                    return;
                }

                var ajax_data = <?php echo json_encode($ajax_data); ?>;

                // 파트 키 추출
                var field_name = '<?php echo $field_name; ?>';
                var part_key = field_name.split('[')[0];

                // 파트 키로 빈 배열/객체 추가
                ajax_data[part_key] = ajax_data[part_key] || {};
                ajax_data[field_name] = file;

                wv_ajax('<?php echo wv()->store_manager->plugin_url . '/ajax.php'; ?>', {
                    replace_with: '<?php echo $skin_selector?>',
                    ajax_option: {
                        success: function(response) {
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                var $label = $('.profile-image-label', $skin);
                                $label.html('<img src="' + e.target.result + '" alt="프로필 사진">');
                            };
                            reader.readAsDataURL(file);


                        },
                        error: function() {
                            alert('업로드 실패');
                        }
                    }
                }, ajax_data);
            })
        });
    </script>
</div>