/** @type {import('tailwindcss').Config} */
function _interop_require_default(obj) {

    return obj && obj.__esModule ? obj : {
        default: obj
    };
}

const plugin = require('C:\\Users\\sekti\\AppData\\Roaming\\npm\\node_modules\\tailwindcss/dist/plugin');
const Configure = require('C:\\Users\\sekti\\AppData\\Roaming\\npm\\node_modules\\@sub0709/json-config');
const wv_postcss =   _interop_require_default(require('C:\\Users\\sekti\\AppData\\Roaming\\npm\\node_modules\\postcss'));
const fs = require('fs');
const conf = Configure.load('./config.json');

const block_json = Configure.load('blocklist.json');
const blocklist = block_json.class_list;


var files_arr = [];
for(var i=0;i<conf.work_root.length;i++){
    files_arr.push(`${conf.work_root[i]}/**/*.php`);
}

module.exports = {
    important:true,
    content: {
        files: files_arr,
    },
    theme: {
        extend: {
            screens: conf.screens,

        },
    },
    corePlugins: {
        preflight: false,
    },
    blocklist: blocklist,
    plugins: [


        plugin( function ({matchUtilities,postcss }) {

            // postcss.root = root_func;

            matchUtilities(
                {
                    fs: (value) => {
                        var value_split = value.split('/');
                        var obj = {};
                        if(value_split[0]){
                            obj['font-size'] = `${value_split[0]}px`;
                        }
                        if(value_split[1]){
                            obj['line-height'] = `${value_split[1]}px`;
                        }
                        if(value_split[2]){

                            let remove_num = value_split[2].replace(/[-\\.0-9]/g, "");

                            if(remove_num===''){

                                let  letter_spacing = (value_split[2]/value_split[0]).toFixed(2);
                                obj['letter-spacing'] = `${letter_spacing}em`;
                            }else{
                                if(/^[-0-9=.]+%$/.test(value_split[2]) ){
                                    obj['letter-spacing'] = (value_split[2].replace(/[^0-9\\.\-]/,"")/100).toFixed(2)+'em';
                                }else{
                                    obj['letter-spacing'] = value_split[2];
                                }

                            }
                        }
                        if(value_split[3]){


                            obj['font-weight'] = value_split[3];
                        }
                        if(value_split[4]){

                            obj['color'] = value_split[4];
                        }
                        return obj
                    },
                    'wv-min': (value) => {
                        var value_split = value.split('/');
                        let obj = {};
                        let point = '';
                        if(value_split[1]){
                            point = '-'+`${value_split[1]}`;
                        }
                        obj['--wv-ini-min'+point] =  `${value_split[0]}`;
                        return obj
                    },
                    'wv-max': (value) => {
                        var value_split = value.split('/');
                        let obj = {};
                        let point = '';
                        if(value_split[1]){
                            point = '-'+`${value_split[1]}`;
                        }
                        obj['--wv-ini-max'+point] =  `${value_split[0]}`;
                        return obj
                    },
                    'wv-add': (value) => {
                        var value_split = value.split('/');
                        let obj = {};
                        let point = '';
                        if(value_split[1]){
                            point = '-'+`${value_split[1]}`;
                        }
                        obj['--wv-ini-add'+point] =  `${value_split[0]}`;
                        return obj
                    },


                }
            )





        }),


    ],
}

