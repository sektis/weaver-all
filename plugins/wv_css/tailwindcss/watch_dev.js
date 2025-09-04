(async function () {

    const chokidar = require('C:\\Users\\sekti\\AppData\\Roaming\\npm\\node_modules\\chokidar');
    const fs = require('fs').promises;
    const fs_sync = require('fs');
    const util = require('util');
    const exec = util.promisify(require('child_process').exec);
    const Configure = require('C:\\Users\\sekti\\AppData\\Roaming\\npm\\node_modules\\@sub0709/json-config');
    let conf = Configure.load('./config.json');
    const cssGen = require('C:\\Users\\sekti\\AppData\\Roaming\\npm\\node_modules\\css-generator');
    var CleanCSS = require('C:\\Users\\sekti\\AppData\\Roaming\\npm\\node_modules\\clean-css');

    const work_root = conf.work_root;
    let watcher_init = false;
    let added_path =[];
    let wv_array =[];
    let wv_responsive_array ={};

    let blocklist_matches = [];

    let start,end;


    for(let i=0;i<conf.blocklist_css_list.length;i++){

        await  fs.readFile(conf.blocklist_css_list[i], 'utf8' ).then(function (data){
            const cssContent = data;

            const varRegex = /(?<![0-9a-z])\.([a-zA-Z0-9_-]+)(?=\s*)/g;
            const matches =  cssContent.match(varRegex)?.map(match => match.slice(1));

            if(matches){
                let arr = blocklist_matches.concat(matches);
                blocklist_matches = arr;
            }

        })

    }
    let arr = blocklist_matches.concat(conf.blocklist_css_add);
    blocklist_matches = arr.filter(value => !conf.blocklist_css_ignore.includes(value));




    var blocklist_obj = {'class_list': arr_qnique(blocklist_matches)};
    var content = JSON.stringify(blocklist_obj);
    if(content){
        fs.writeFile('./blocklist.json', content, (err) => {
            if (err) console.log('Error: ', err);
        });
    }

    function arr_qnique(arr) {
        const set = new Set(arr);

        const uniqueArr = [...set];
        return uniqueArr;
    }

    function get_only_num(text){
        return parseFloat(text.match(/[0-9]+(\.[0-9]+)?/g, ''));
    }

    function getKeyByValue(object, value) {
        return Object.keys(object).find(key => object[key] === value);
    }
    function addWvValues(path,arr){
        if(wv_array[path]===undefined){`
            wv_array[path] = [];`
        }

        wv_array[path] = arr;

    }
    async function complete_process(){

        let tailwindcss_path = '../wv_tailwind.css';
        let responsivecss_path = '../wv_responsive.css';
        await exec('npx postcss ./input.css -o ../wv_tailwind.css');

        scanWvValues(tailwindcss_path).then(function (data) {


            addWvValues(tailwindcss_path,data);


            let mergedArray = arr_qnique(Object.values(wv_array).flat().sort((a, b) => a.replace(/[^0-9]/g, '') - b.replace(/[^0-9]/g, '')));





            mergedArray.flatMap((item, index) => {
                const varRegex = /--wv-?([a-z]{2,3})?-([\d_]+)/;
                const matches = item.match(varRegex);
                let screen = 'basic'
                let value = matches[2].replace('_','.');
                if(matches[1]!==undefined){
                    screen = matches[1];
                }
                if(wv_responsive_array[screen]==undefined){
                    wv_responsive_array[screen]=[];
                }
                wv_responsive_array[screen].push(value)

            });

            let css = cssGen.create( {
                indentation: '  ' // 2 spaces
            });
            const make = conf.make;
            const selector = make.selector;


            if(make.mobile_screen){
                let screen_size = get_only_num(conf.screens[make.mobile_screen]['max']);
                css_wrap_media(css,'.view-pc',{'display': 'none!important'},screen_size)
                let min = screen_size+0.02;
                css_wrap_media(css,'.view-mobile',{'display': 'none!important'},min,'min')
            }

            let make_screens = make.screens;

            Object.keys(make_screens).forEach((screen) => {

                let opt = make_screens[screen];

                if(!wv_responsive_array[screen])return true;
                let styles ={};
                let screen_name = screen==='basic'?'':`-${screen}`;



                styles[`--wv-ini-width${screen_name}`]=opt.width || get_only_num(conf.screens[screen]['max']);
                styles[`--wv-ini-vw${screen_name}`]=`calc(10 / (var(--wv-ini-width${screen_name}) / 100))`;
                styles[`--wv-ini-min${screen_name}`]=opt.min || '0';
                styles[`--wv-ini-max${screen_name}`]=opt.max || '1';
                styles[`--wv-ini-add${screen_name}`]=opt.add || '1';
                styles[`--wv-standard-value${screen_name}`]=`clamp(calc(10px * var(--wv-ini-min${screen_name}) * var(--wv-ini-add${screen_name})),calc(var(--wv-ini-vw${screen_name}) * 1vw * var(--wv-ini-add${screen_name})),calc(10px * var(--wv-ini-max${screen_name}) * var(--wv-ini-add${screen_name})))`;

                for(let i=0;i<wv_responsive_array[screen].length;i++){
                    let replace_key = wv_responsive_array[screen][i].replace('.','_');
                    styles[`--wv${screen_name}-${replace_key}`]=`calc(var(--wv-standard-value${screen_name}) *  ${wv_responsive_array[screen][i]} / 10 )`;

                }

                if(screen!=='basic'){
                    css_wrap_media(css,make.selector,styles,get_only_num(conf.screens[screen]['max']));
                }else{
                    css.addRule(make.selector, styles)
                }




                if(opt.zoom){
                    Object.keys(opt.zoom).forEach((point) => {
                        let zoom_opt = opt.zoom[point];
                        let zoom_styles = {};
                        if(zoom_opt.min){
                            zoom_styles[`--wv-ini-min${screen_name}`]=zoom_opt.min
                        }
                        if(zoom_opt.max){
                            zoom_styles[`--wv-ini-max${screen_name}`]=zoom_opt.max
                        }
                        if(zoom_opt.add){
                            zoom_styles[`--wv-ini-add${screen_name}`]=zoom_opt.add
                        }

                        css_wrap_media(css,make.selector,zoom_styles,get_only_num(conf.screens[point]['max']));
                    })
                }


            });


            let css_output = new CleanCSS({
                level: 2,
                format: 'keep-breaks'
            }).minify(css.getOutput());
            fs.writeFile(responsivecss_path, css_output.styles, (err) => {
                if (err) console.error('write error:', err);
            });

            // fs.writeFile(responsivecss_path, css.getOutput() );

            wv_debug('end');

        })

    }



    function css_wrap_media(css,selector,styles,screen_size,etc='max'){
        css.openBlock('media', `(${etc}-width: ${screen_size}px)`)
        css.addRule(selector, styles)
        css.closeBlock();

    }

    function wv_debug(type){
        if(type==='start'){
            start = new Date();
            return;
        }
        if(type==='end'){
            end = new Date();
            let executionTime = (end - start) / 1000.0; // 초 단위로 실행 시간 확인
            console.log('complete',executionTime+'sec');
        }
    }

    async function scanWvValues(path){

        let matches = await fs.readFile(path, 'utf8' ).then(function (data){
            const cssContent = data;
            const varRegex = /--wv-?([a-z]{2,3})?-([\d_]+)/g;
            const matches = cssContent.match(varRegex);

            return matches?matches:[];
        })
        return matches;
    }

    var watch_files_arr = [];
    for(var i=0;i<work_root.length;i++){
        watch_files_arr.push(`${work_root[i]}/**/*.php`);
        watch_files_arr.push(`${work_root[i]}/**/*.css`);
    }

    const watcher = chokidar.watch(watch_files_arr, {
        ignored:[`${work_root[0]}/**/.*/*`,`${work_root[0]}/**/tailwindcss/*`,`${work_root[0]}/**/vendor/*`,`${work_root[0]}/**/assets/font/*`,`${work_root[0]}/**/assets/plugin/(?!weaver)**/*`,`${work_root[0]}/**/wv_tailwind.css`,`${work_root[0]}/**/wv_responsive.css`], // ignore dotfiles
        persistent: true,
        awaitWriteFinish: {
            stabilityThreshold: 1500,
            pollInterval: 100
        },
    })
        .on('unlink', function (path) {
            wv_debug('start');
            // console.log('unlink',path);
            delete wv_array[path];
            var added_key = getKeyByValue(added_path,path)
            delete added_path[added_key];
            complete_process()
        })
        .on('change', function (path) {
            wv_debug('start');
            var added_key = getKeyByValue(added_path,path)
            if(added_key){
                delete added_path[added_key];
                return true;
            }
            // console.log('change',path);
            scanWvValues(path).then(function (data) {
                addWvValues(path,data);
                complete_process()
            })
        })
        .on('add',   function (path) {

            wv_debug('start');

            let  init= watcher_init;
            if(init===true){
                added_path.push(path);
            }
            scanWvValues(path).then(function (data) {
                addWvValues(path,data);

                if(init===true){
                    // console.log('add',path);
                    complete_process()
                }
            })

        })
        .on('ready', () => {
            // console.log('ready');
            wv_debug('start');
            watcher_init=true;
            complete_process();
        })

})();