$(document).ready(function () {


   setTimeout(function () {
       var Sticky = new hcSticky('#header-wrapper', {
           stickTo: '#site-wrapper',
           onStart:function () {
               $("body").addClass('scroll-on')
           },
           onStop:function () {
               $("body").removeClass('scroll-on')
           }
       });
   },500)


})