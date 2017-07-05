
jQuery(function($){
	var selection = false;
	var gravity_forms_stylerShortcodePanel = $('#gravity-forms-styler-shortcode-panel-tmpl').html();

	$('body').append(gravity_forms_stylerShortcodePanel);
	$('.media-modal-backdrop, .media-modal-close').on('click', function(){
		gravity_forms_styler_hideModal();
	})
	$(document).keyup(function(e) {
		if (e.keyCode == 27) {
			gravity_forms_styler_hideModal();
		}
	});

	// show modal
	$(document).on('click', '#gravity-forms-styler-shortcodeinsert', function(){
		if($(this).data('shortcode')){
			window.send_to_editor('['+$(this).data('shortcode')+']');
			return;
		}
				
		// autoload item
		var autoload = $('.gravity-forms-styler-autoload');
		if(autoload.length){
			gravity_forms_styler_loadtemplate(autoload.data('shortcode'));
		}
		$('#gravity-forms-styler-category-selector').on('change', function(){
			gravity_forms_styler_loadtemplate('');
			$('.gravity-forms-styler-elements-selector').hide();
			$('#gravity-forms-styler-elements-selector-'+this.value).show().val('');
		});

		$('.gravity-forms-styler-elements-selector').on('change', function(){
			gravity_forms_styler_loadtemplate(this.value);
		});

		if(typeof tinyMCE !== 'undefined'){
			if(tinyMCE.activeEditor !== null){
				selection = tinyMCE.activeEditor.selection.getContent();
			}else{
				selection = false;
			}
		}else{
			selection = false;
		}
		if(selection.length > 0){
			$('#gravity-forms-styler-content').html(selection);
		}
		$('#gravity-forms-styler-shortcode-panel').show();
	});
	$('#gravity-forms-styler-insert-shortcode').on('click', function(){
		gravity_forms_styler_sendCode();
	})
	// modal tabs
	$('#gravity-forms-styler-shortcode-config').on('click', '.gravity-forms-styler-shortcode-config-nav li a', function(){
		$('.gravity-forms-styler-shortcode-config-nav li').removeClass('current');
		$('.group').hide();
		$(''+$(this).attr('href')+'').show();
		$(this).parent().addClass('current');
		return false;
	});


});

function gravity_forms_styler_loadtemplate(shortcode){
	var target = jQuery('#gravity-forms-styler-shortcode-config');
	if(shortcode.length <= 0){
		target.html('');
	}
	target.html(jQuery('#gravity-forms-styler-'+shortcode+'-config-tmpl').html());
}

function gravity_forms_styler_sendCode(){

	var shortcode = jQuery('#gravity-forms-styler-shortcodekey').val(),
		output = '['+shortcode,
		ctype = '',
		fields = {};
	
	if(shortcode.length <= 0){return; }

	if(jQuery('#gravity-forms-styler-shortcodetype').val() === '2'){
		ctype = jQuery('#gravity-forms-styler-default-content').val()+'[/'+shortcode+']';
	}
	jQuery('#gravity-forms-styler-shortcode-config input,#gravity-forms-styler-shortcode-config select,#gravity-forms-styler-shortcode-config textarea').not('.configexclude').each(function(){
		if(this.value){
			// see if its a checkbox
			var thisinput = jQuery(this),
				attname = this.name;

			if(thisinput.prop('type') == 'checkbox'){
				if(!thisinput.prop('checked')){
					return;
				}
			}
			if(thisinput.prop('type') == 'radio'){
				if(!thisinput.prop('checked')){
					return;
				}
			}

			if(attname.indexOf('[') > -1){
				attname = attname.split('[')[0];
				var newloop = {};
				newloop[attname] = this.value;
				if(!fields[attname]){
					fields[attname] = [];
				}
				fields[attname].push(newloop);
			}else{
				var newfield = {};
				fields[attname] = this.value;
			}
		}
	});
	for( var field in fields){
		if(typeof fields[field] == 'object'){
			for(i=0;i<fields[field].length; i++){
				output += ' '+field+'_'+(i+1)+'="'+fields[field][i][field]+'"';
			}
		}else{
			output += ' '+field+'="'+fields[field]+'"';
		}
	}
	gravity_forms_styler_hideModal();
	window.send_to_editor(output+']'+ctype);

}
function gravity_forms_styler_hideModal(){
	jQuery('#gravity-forms-styler-shortcode-panel').hide();
	gravity_forms_styler_loadtemplate('');
	jQuery('#gravity-forms-styler-elements-selector').show();
	jQuery('.gravity-forms-styler-elements-selector').val('');	
	jQuery('#gravity-forms-styler-category-selector').val('');
}
