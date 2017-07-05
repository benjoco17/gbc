<?php if(yes == $atts['use_placeholders']){ ?>
// Start allowance of jQuery to $ shortcut
jQuery(document).ready(function($){

    // Convert label to placeholder
    $.each($('.gform_wrapper input, .gform_wrapper textarea, .gform_wrapper select'), function () {
        var gfapId = this.id;
        var gfapLabel = $('label[for=' + gfapId + ']');
        $(gfapLabel).hide();
        var gfapLabelValue = $(gfapLabel).text();
        $(this).attr('placeholder',gfapLabelValue);
    });

    // Use modernizr to add placeholders for IE
    if(!Modernizr.input.placeholder){$("input,textarea").each(function(){if($(this).val()=="" && $(this).attr("placeholder")!=""){$(this).val($(this).attr("placeholder"));$(this).focus(function(){if($(this).val()==$(this).attr("placeholder")) $(this).val("");});$(this).blur(function(){if($(this).val()=="") $(this).val($(this).attr("placeholder"));});}});}

// Ends allowance of jQuery to $ shortcut
});
<?php } ?>