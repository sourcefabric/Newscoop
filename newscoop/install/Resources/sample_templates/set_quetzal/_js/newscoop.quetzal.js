$(document).ready(function() {
  
  $.support.placeholder = (function(){
      var i = document.createElement('input');
      return 'placeholder' in i;
  })();

	// initialize timeago plugin for dates
 	$(".timeago").timeago();

 	// boostrap initialize components
  	$("a[rel]").popover();
  	$("a[rel=tooltip]").tooltip();

  	// Place holder on inputs for IE
    if(!$.support.placeholder) {
        var active = document.activeElement;
        $('textarea,:password, :text, #searchinput').focus(function () {
                if ($(this).attr('placeholder') != '' && $(this).val() == $(this).attr('placeholder')) {
                        $(this).val('').removeClass('hasPlaceholder');
                }
        }).blur(function () {
                if ($(this).attr('placeholder') != '' && typeof $(this).attr('placeholder') != 'undefined' && ($(this).val() == '' || $(this).val() == $(this).attr('placeholder'))) {
                        $(this).val($(this).attr('placeholder')).addClass('hasPlaceholder');
                }
        });
        $('textarea,:password, :text, #searchinput').blur();
        $(active).focus();
        $('form').submit(function () {
                $(this).find('.hasPlaceholder').each(function() { $(this).val(''); });
        });
    }

    /* Poll Ajaxified
    *   -------------------------------------------------------*/
    $('#poll-button').click(function(){
      $.post($('form[name=debate]').attr("action"),$('form[name=debate]').serialize(),function(data){$('#poll').html(data);});
      return false;
    }); 
});
