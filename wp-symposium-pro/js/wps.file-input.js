jQuery.fn.wpsbootstrapFileInput = function() {

  this.each(function(i,elem){

    var jQueryelem = jQuery(elem);

    // Maybe some fields don't need to be standardized.
    if (typeof jQueryelem.attr('data-bfi-disabled') != 'undefined') {
      return;
    }

    // Set the word to be displayed on the button
    var buttonWord = 'Browse';

    if (typeof jQueryelem.attr('title') != 'undefined') {
      buttonWord = jQueryelem.attr('title');
    }

    var className = '';

    if (!!jQueryelem.attr('class')) {
      className = ' ' + jQueryelem.attr('class');
    }

    // Now we're going to wrap that input field with a Bootstrap button.
    // The input will actually still be there, it will just be float above and transparent (done with the CSS).
    jQueryelem.wrap('<a class="file-input-wrapper ' + className + '"></a>').parent().prepend(buttonWord);
  })

  // After we have found all of the file inputs let's apply a listener for tracking the mouse movement.
  // This is important because the in order to give the illusion that this is a button in FF we actually need to move the button from the file input under the cursor. Ugh.
  .promise().done( function(){

    // As the cursor moves over our new Bootstrap button we need to adjust the position of the invisible file input Browse button to be under the cursor.
    // This gives us the pointer cursor that FF denies us

    jQuery('.file-input-wrapper').mousemove(function(cursor) {

      var input, wrapper,
        wrapperX, wrapperY,
        inputWidth, inputHeight,
        cursorX, cursorY;

      // This wrapper element (the button surround this file input)
      wrapper = jQuery(this);
      // The invisible file input element
      input = wrapper.find("input");
      // The left-most position of the wrapper
      wrapperX = wrapper.offset().left;
      // The top-most position of the wrapper
      wrapperY = wrapper.offset().top;
      // The with of the browsers input field
      inputWidth= input.width();
      // The height of the browsers input field
      inputHeight= input.height();
      //The position of the cursor in the wrapper
      cursorX = cursor.pageX;
      cursorY = cursor.pageY;

      //T he positions we are to move the invisible file input
      // The 20 at the end is an arbitrary number of pixels that we can shift the input such that cursor is not pointing at the end of the Browse button but somewhere nearer the middle
      moveInputX = cursorX - wrapperX - inputWidth + 20;
      // Slides the invisible input Browse button to be positioned middle under the cursor
      moveInputY = cursorY- wrapperY - (inputHeight/2);

      // Apply the positioning styles to actually move the invisible file input
      input.css({
        left:0,
        top:0
      });
      /*
      // Removed this and forced position above, to avoid taking over screen area
      input.css({
        left:moveInputX,
        top:moveInputY
      });
      */
    });

    jQuery('body').on('change', '.file-input-wrapper input[type=file]', function(){

      var fileName;
      fileName = jQuery(this).val();

      // Remove any previous file names
      jQuery(this).parent().next('.file-input-name').remove();
      if (!!jQuery(this).prop('files') && jQuery(this).prop('files').length > 1) {
        fileName = jQuery(this)[0].files.length+' files';
        //jQuery(this).parent().after('<span class="file-input-name">'+jQuery(this)[0].files.length+' files</span>');
      }
      else {
        // var fakepath = 'C:\\fakepath\\';
        // fileName = jQuery(this).val().replace('C:\\fakepath\\','');
        fileName = fileName.substring(fileName.lastIndexOf('\\')+1,fileName.length);
      }

      jQuery(this).parent().after('<span class="file-input-name">'+fileName+'</span>');
    });

  });

};

// Add the styles before the first stylesheet
// This ensures they can be easily overridden with developer styles
var cssHtml = '<style>'+
  '.file-input-wrapper { overflow: hidden; position: relative; cursor: pointer; z-index: 1; }'+
  '.file-input-wrapper input[type=file], .file-input-wrapper input[type=file]:focus, .file-input-wrapper input[type=file]:hover { position: absolute; top: 0; left: 0; cursor: pointer; opacity: 0; filter: alpha(opacity=0); z-index: 99; outline: 0; }'+
  '.file-input-name { margin-left: 8px; }'+
  '</style>';
jQuery('link[rel=stylesheet]').eq(0).before(cssHtml);


