(function() {
  
    tinymce.create('tinymce.plugins.pushortcodes', {

        init : function(ed, url) {

          var t = this;
          t.editor = ed;

          ed.addButton('wps_pro', {
              title : 'WP Symposium Pro',
              cmd : 'wps_pro_cmd',
              icon : 'wps_pro',
          });
          ed.addCommand('wps_pro_cmd', function() {

            if (jQuery('#content_wps_pro').length > 0) {
              var offset = jQuery('#content_wps_pro').offset();
              var top = offset.top + 24;
              var left = offset.left;

            } else {

              var offset = jQuery('.mce-i-wps_pro').offset();
              var top = offset.top + 23;
              var left = offset.left - 4;

            }
          
            jQuery('#wps_admin_shortcodes').css('top', top).css('left', left).show();

          });       

        },

    });

    tinymce.PluginManager.add('wps_pro', tinymce.plugins.pushortcodes);

})();

jQuery(document).mouseup(function (e) {
  jQuery('#wps_admin_shortcodes').hide();
});
