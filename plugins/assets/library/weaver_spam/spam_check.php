<script>
    $(document).ready(function () {

        var $form =  $("form#<?php echo $form_id?>[name='<?php echo $form_id?>']");

        let lastX = null;
        let lastY = null;
        let lastDirection = null;
        let straightCount = 0;
        let naturalCount = 0;
        let suspicious = true; // 초기값 true

        const REQUIRED_BOT_COUNT = 80;      // 완벽 직선이 몇 번 이상이면 suspicious 유지
        const REQUIRED_HUMAN_COUNT = 20;    // 자연스러운 움직임이 몇 번 이상이면 해제

        document.addEventListener('mousemove', (e) => {
            if (lastX === null || lastY === null) {
                lastX = e.clientX;
                lastY = e.clientY;
                return;
            }

            const dx = e.clientX - lastX;
            const dy = e.clientY - lastY;

            const direction = [dx, dy];

            const isPerfectLine = (
                (dy === 0 && Math.abs(dx) === 1) ||
                (dx === 0 && Math.abs(dy) === 1) ||
                (Math.abs(dx) === 1 && Math.abs(dy) === 1)
            );

            const isSameDirection = (
                lastDirection &&
                direction[0] === lastDirection[0] &&
                direction[1] === lastDirection[1]
            );

            const isSuspiciousMove = isPerfectLine && isSameDirection;

            if (isSuspiciousMove) {
                straightCount++;
                naturalCount = 0; // 자연스러운 카운터 리셋
            } else {
                naturalCount++;
                straightCount = 0; // 봇 카운터 리셋
            }

            if (!suspicious && isSuspiciousMove && straightCount >= REQUIRED_BOT_COUNT) {

                suspicious = true;
            }

            if (suspicious && naturalCount >= REQUIRED_HUMAN_COUNT) {
                suspicious = false;
            }

            lastX = e.clientX;
            lastY = e.clientY;
            lastDirection = direction;
        });

        document.addEventListener('touchmove', (e) => {
            suspicious = false;

        });

        $($form).on('submit',function () {

           if(!suspicious){
               $("#wv_h_spot",$form).val('wv');
           }
       })


        $form.prepend('<input type="hidden" name="wv_h_spot" id="wv_h_spot"  value="">');

    })
</script>