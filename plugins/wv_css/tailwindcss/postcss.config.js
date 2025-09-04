
const tailwindcss_cli = require('C:\\Users\\sekti\\AppData\\Roaming\\npm\\node_modules\\@tailwindcss/postcss/dist');
const config = require("./config.json");
function wv_px_trans(value, media) {
    if (!value || typeof value !== 'string') return value;
    if (!value.match(/[\d.]+px/)) return value;

    // 이미 변환된 값은 패스
    if (value.includes('var(--wv')) return value;

    // 27.5 -> 27_5
    value = value.replace(/\.(\d)/g, '_$1');

    const pxRegex = /(-?[\d_]+)px/g;

    return value.replace(pxRegex, (match, num) => {
        const absNum = num.replace(/^-/, ''); // 음수 부호 제거
        const val = `--wv${media ? `-${media}` : ''}-${absNum}`;
        return num.startsWith('-')
            ? `calc(var(${val}) * -1)`
            : `var(${val})`;
    });
}
function wv_get_screen(px_value) {
    const target = parseFloat(px_value);

    if (!config || !config.screens) return null;

    for (const key in config.screens) {
        const screen = config.screens[key];
        const screenMax = parseFloat(screen.max || screen); // max 또는 숫자 문자열

        if (Math.abs(screenMax - target) < 0.01) {
            return key;
        }
    }

    return null;
}


module.exports = {
    plugins: [
        tailwindcss_cli('./tailwind.config.js'),
        {
            postcssPlugin: 'postcss-wv-transform',
            OnceExit(root) {
                // Tailwind v4 스타일 @media (991.98px >= width) 처리
                root.walkAtRules((rule) => {
                    const regex = /^\(?(\d+(?:\.\d+)?)px\s*>=\s*width\)?$/;
                    const match = rule.params.match(regex);

                    if (match && match[1]) {
                        const media = wv_get_screen(match[1]);
                        if (media) {
                            rule.walkDecls((decl) => {
                                if (decl.value.includes('px') || decl.value.includes('calc')) {
                                    decl.value = wv_px_trans(decl.value, media);
                                }
                            });
                        }
                    }
                });

                // 일반 rule 내부의 px, calc 값 처리
                root.walkDecls((decl) => {
                    if (decl.value.includes('px') || decl.value.includes('calc')) {
                        decl.value = wv_px_trans(decl.value);
                    }
                });
            }
        },
    ],
};

module.exports.postcss = true;
