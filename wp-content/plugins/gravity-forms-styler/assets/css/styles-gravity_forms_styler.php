<?php if(yes == $atts['use_google_fonts']){ ?>
@import url(http://fonts.googleapis.com/css?family=<?php echo implode(", ", (array)$atts['google_font_name']); ?>);
<?php } ?>

body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> {
   
    border: <?php echo implode(", ", (array)$atts['fw_border_size']); ?>px <?php echo implode(", ", (array)$atts['fw_border_type']); ?> <?php echo implode(", ", (array)$atts['fw_border_color']); ?>;
    max-width: <?php echo implode(", ", (array)$atts['fw_maximum_width']); ?>%;
    border-radius: <?php echo implode(", ", (array)$atts['fw_border_radius']); ?>px;
	background: url(<?php echo implode(", ", (array)$atts['fw_background_image']); ?>) no-repeat <?php echo implode(", ", (array)$atts['fw_background_color']); ?>;
	margin: <?php echo implode(", ", (array)$atts['fw_margins']); ?>;
	padding: <?php echo implode(", ", (array)$atts['fw_padding']); ?>;
	over-flow: hidden;
}

body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_heading {
    border-bottom: <?php echo implode(", ", (array)$atts['fh_border_size']); ?>px <?php echo implode(", ", (array)$atts['fh_border_type']); ?> <?php echo implode(", ", (array)$atts['fh_border_color']); ?>;
    width: 100%;
    background-color:<?php echo implode(", ", (array)$atts['fh_background_color']); ?>;
    border-radius: <?php echo implode(", ", (array)$atts['fw_border_radius']); ?>px;
    margin: <?php echo implode(", ", (array)$atts['fh_margins']); ?>;
    padding: <?php echo implode(", ", (array)$atts['fh_padding']); ?>;
    border-bottom-right-radius:0px;
    border-bottom-left-radius:0px;
}
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_heading .gform_title {
    color: <?php echo implode(", ", (array)$atts['ft_title_color']); ?>;
    font-size: <?php echo implode(", ", (array)$atts['ft_font_size']); ?>px;
    font-family: <?php echo implode(", ", (array)$atts['ft_font_family']); ?>;
    text-align: <?php echo implode(", ", (array)$atts['ft_title_position']); ?>;
    margin-top: 0px;
    margin-bottom: 0px;
}

body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_heading .gform_description {
    color: <?php echo implode(", ", (array)$atts['fd_color']); ?>;
    font-size: <?php echo implode(", ", (array)$atts['fd_font_size']); ?>px;
    font-family: <?php echo implode(", ", (array)$atts['fd_font_family']); ?>;
    margin-left: 10px;
    text-align: <?php echo implode(", ", (array)$atts['fd_text_alignment']); ?>;
    display: block;
}
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_footer input[type=submit] {
    border: <?php echo implode(", ", (array)$atts['sb_border_size']); ?>px <?php echo implode(", ", (array)$atts['sb_border_type']); ?> <?php echo implode(", ", (array)$atts['sb_border_color']); ?>;
    background: <?php echo implode(", ", (array)$atts['sb_background_color']); ?>;
    color: <?php echo implode(", ", (array)$atts['sb_font_color']); ?>;
    font-size: <?php echo implode(", ", (array)$atts['sb_font_size']); ?>px;
    border-radius: <?php echo implode(", ", (array)$atts['sb_border_radius']); ?>px;
    font-family: <?php echo implode(", ", (array)$atts['sb_font_family']); ?>;
    width: <?php echo implode(", ", (array)$atts['sb_maximum_width']); ?>px;
    height: <?php echo implode(", ", (array)$atts['sb_height']); ?>px;
	text-align: <?php echo implode(", ", (array)$atts['sb_button_position']); ?>;
}
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_footer input[type=submit]:hover {
    background: <?php echo implode(", ", (array)$atts['sb_hover_color']); ?>;
}
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_footer {text-align: <?php echo implode(", ", (array)$atts['sb_button_position']); ?>;}

body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield input[type=text],
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield input[type=email],
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield input[type=tel],
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield input[type=url],
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield input[type=password]
{
    background-color: <?php echo implode(", ", (array)$atts['tf_background_color']); ?>;
    border: <?php echo implode(", ", (array)$atts['tf_border_size']); ?>px <?php echo implode(", ", (array)$atts['tf_border_type']); ?> <?php echo implode(", ", (array)$atts['tf_border_color']); ?>;
    font-size: <?php echo implode(", ", (array)$atts['tf_font_size']); ?>px;
    font-family: <?php echo implode(", ", (array)$atts['tf_font_family']); ?>;
    color: <?php echo implode(", ", (array)$atts['tf_font_color']); ?>;
    border-radius: <?php echo implode(", ", (array)$atts['tf_border_radius']); ?>px;
    max-width: <?php echo implode(", ", (array)$atts['tf_maximum_width']); ?>%;
	margin: <?php echo implode(", ", (array)$atts['tf_margins']); ?>;
	padding: <?php echo implode(", ", (array)$atts['tf_padding']); ?>;
	height:<?php echo implode(", ", (array)$atts['tf_height']); ?>px;
<?php if(yes == $atts['use_shadows']){ ?>	
-moz-box-shadow:    <?php echo implode(", ", (array)$atts['bs_hr_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_vertical_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_blur_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_spread_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_shadow_color']); ?>;
-webkit-box-shadow: <?php echo implode(", ", (array)$atts['bs_hr_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_vertical_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_blur_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_spread_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_shadow_color']); ?>;
box-shadow:         <?php echo implode(", ", (array)$atts['bs_hr_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_vertical_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_blur_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_spread_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_shadow_color']); ?>;
-moz-box-shadow:    inset <?php echo implode(", ", (array)$atts['bs_inner_hr_offset']); ?> px <?php echo implode(", ", (array)$atts['bs_inner_vr_offset']); ?>px <?php echo implode(", ", (array)$atts['inner_bl_radius']); ?>px <?php echo implode(", ", (array)$atts['inner_sp_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_inner_color']); ?>;
-webkit-box-shadow: inset <?php echo implode(", ", (array)$atts['bs_inner_hr_offset']); ?> px <?php echo implode(", ", (array)$atts['bs_inner_vr_offset']); ?>px <?php echo implode(", ", (array)$atts['inner_bl_radius']); ?>px <?php echo implode(", ", (array)$atts['inner_sp_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_inner_color']); ?>;
box-shadow:         inset <?php echo implode(", ", (array)$atts['bs_inner_hr_offset']); ?> px <?php echo implode(", ", (array)$atts['bs_inner_vr_offset']); ?>px <?php echo implode(", ", (array)$atts['inner_bl_radius']); ?>px <?php echo implode(", ", (array)$atts['inner_sp_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_inner_color']); ?>;
<?php } ?>
}

body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield textarea {   
    background-color: <?php echo implode(", ", (array)$atts['tf_background_color']); ?>;
    border: <?php echo implode(", ", (array)$atts['tf_border_size']); ?>px <?php echo implode(", ", (array)$atts['tf_border_type']); ?> <?php echo implode(", ", (array)$atts['tf_border_color']); ?>;
    font-size: <?php echo implode(", ", (array)$atts['tf_font_size']); ?>px;
    font-family: <?php echo implode(", ", (array)$atts['tf_font_family']); ?>;
    color: <?php echo implode(", ", (array)$atts['tf_font_color']); ?>;
    border-radius: <?php echo implode(", ", (array)$atts['tf_border_radius']); ?>px;
    max-width: <?php echo implode(", ", (array)$atts['pg_max_width']); ?>%;
    margin: <?php echo implode(", ", (array)$atts['pg_margins']); ?> !important;
    padding: <?php echo implode(", ", (array)$atts['pg_padding']); ?> !important;
    height:<?php echo implode(", ", (array)$atts['pg_height']); ?>px;
	<?php if(yes == $atts['use_shadows']){ ?>	
-moz-box-shadow:    <?php echo implode(", ", (array)$atts['bs_hr_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_vertical_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_blur_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_spread_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_shadow_color']); ?>;
-webkit-box-shadow: <?php echo implode(", ", (array)$atts['bs_hr_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_vertical_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_blur_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_spread_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_shadow_color']); ?>;
box-shadow:         <?php echo implode(", ", (array)$atts['bs_hr_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_vertical_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_blur_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_spread_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_shadow_color']); ?>;
-moz-box-shadow:    inset <?php echo implode(", ", (array)$atts['bs_inner_hr_offset']); ?> px <?php echo implode(", ", (array)$atts['bs_inner_vr_offset']); ?>px <?php echo implode(", ", (array)$atts['inner_bl_radius']); ?>px <?php echo implode(", ", (array)$atts['inner_sp_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_inner_color']); ?>;
-webkit-box-shadow: inset <?php echo implode(", ", (array)$atts['bs_inner_hr_offset']); ?> px <?php echo implode(", ", (array)$atts['bs_inner_vr_offset']); ?>px <?php echo implode(", ", (array)$atts['inner_bl_radius']); ?>px <?php echo implode(", ", (array)$atts['inner_sp_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_inner_color']); ?>;
box-shadow:         inset <?php echo implode(", ", (array)$atts['bs_inner_hr_offset']); ?> px <?php echo implode(", ", (array)$atts['bs_inner_vr_offset']); ?>px <?php echo implode(", ", (array)$atts['inner_bl_radius']); ?>px <?php echo implode(", ", (array)$atts['inner_sp_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_inner_color']); ?>;
<?php } ?>
}

body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield select {   
    background-color: <?php echo implode(", ", (array)$atts['dd_background_color']); ?>;
    border: <?php echo implode(", ", (array)$atts['dd_border_size']); ?>px <?php echo implode(", ", (array)$atts['dd_border_type']); ?> <?php echo implode(", ", (array)$atts['dd_border_color']); ?>;
    font-size: <?php echo implode(", ", (array)$atts['dd_font_size']); ?>px;
    font-family: <?php echo implode(", ", (array)$atts['dd_font_family']); ?>;
    color: <?php echo implode(", ", (array)$atts['dd_font_color']); ?>;
    border-radius: <?php echo implode(", ", (array)$atts['dp_border_radius']); ?>px;
    max-width: <?php echo implode(", ", (array)$atts['dd_maximum_width']); ?>%;
    margin: <?php echo implode(", ", (array)$atts['dd_margins']); ?>;
    padding: <?php echo implode(", ", (array)$atts['dd_padding']); ?>;
    <?php echo implode(", ", (array)$atts['dp_custom_css']); ?>
    height:<?php echo implode(", ", (array)$atts['dd_height']); ?>px;
   <?php if(yes == $atts['use_shadows']){ ?>	
-moz-box-shadow:    <?php echo implode(", ", (array)$atts['bs_hr_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_vertical_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_blur_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_spread_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_shadow_color']); ?>;
-webkit-box-shadow: <?php echo implode(", ", (array)$atts['bs_hr_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_vertical_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_blur_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_spread_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_shadow_color']); ?>;
box-shadow:         <?php echo implode(", ", (array)$atts['bs_hr_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_vertical_offset']); ?>px <?php echo implode(", ", (array)$atts['bs_blur_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_spread_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_shadow_color']); ?>;
-moz-box-shadow:    inset <?php echo implode(", ", (array)$atts['bs_inner_hr_offset']); ?> px <?php echo implode(", ", (array)$atts['bs_inner_vr_offset']); ?>px <?php echo implode(", ", (array)$atts['inner_bl_radius']); ?>px <?php echo implode(", ", (array)$atts['inner_sp_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_inner_color']); ?>;
-webkit-box-shadow: inset <?php echo implode(", ", (array)$atts['bs_inner_hr_offset']); ?> px <?php echo implode(", ", (array)$atts['bs_inner_vr_offset']); ?>px <?php echo implode(", ", (array)$atts['inner_bl_radius']); ?>px <?php echo implode(", ", (array)$atts['inner_sp_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_inner_color']); ?>;
box-shadow:         inset <?php echo implode(", ", (array)$atts['bs_inner_hr_offset']); ?> px <?php echo implode(", ", (array)$atts['bs_inner_vr_offset']); ?>px <?php echo implode(", ", (array)$atts['inner_bl_radius']); ?>px <?php echo implode(", ", (array)$atts['inner_sp_radius']); ?>px <?php echo implode(", ", (array)$atts['bs_inner_color']); ?>;
<?php } ?>
}

body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield .gfield_radio li input[type=radio] {
max-width: <?php echo implode(", ", (array)$atts['mc_maximum_width']); ?>%;
<?php echo implode(", ", (array)$atts['mpc_custom_css']); ?>
}

body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield .gfield_checkbox li input[type=checkbox] {
max-width: <?php echo implode(", ", (array)$atts['cb_maxium_width']); ?>%;
<?php echo implode(", ", (array)$atts['cbi_custom_css']); ?>
}
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield .gfield_description {margin-left: 10px;}
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield .gfield_label {
    color: <?php echo implode(", ", (array)$atts['fl_label_color']); ?>;
    font-size: <?php echo implode(", ", (array)$atts['label_font_size']); ?>px;
    font-family: <?php echo implode(", ", (array)$atts['label_font_family']); ?>;
    margin-left: 10px;
}
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gsection .gsection_title {
    color: <?php echo implode(", ", (array)$atts['sbt_color']); ?>;
    font-size: <?php echo implode(", ", (array)$atts['sbt_title_font_size']); ?>px;
    font-family: <?php echo implode(", ", (array)$atts['sbt_title_font_family']); ?>;
    text-align: <?php echo implode(", ", (array)$atts['sbt_title_position']); ?>;
    margin-left : 10px;
}
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gsection .gsection_description {
    color: <?php echo implode(", ", (array)$atts['sbd_color']); ?>;
    font-size: <?php echo implode(", ", (array)$atts['sbd_font_size']); ?>px;
    font-family: <?php echo implode(", ", (array)$atts['sbd_font_family']); ?>;
    text-align: <?php echo implode(", ", (array)$atts['sbd_description_position']); ?>;
    margin-left : 10px;
}
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield .ginput_container {
margin-left: 10px;
margin-right: 10px;
}
body #gforms_confirmation_message  {
    border: <?php echo implode(", ", (array)$atts['cm_border_size']); ?>px <?php echo implode(", ", (array)$atts['cm_border_type']); ?> <?php echo implode(", ", (array)$atts['cm_border_color']); ?>;
    border-radius: <?php echo implode(", ", (array)$atts['cm_border_radius']); ?>px;
    max-width: <?php echo implode(", ", (array)$atts['cm_maximum_width']); ?>%;
    font-size: <?php echo implode(", ", (array)$atts['cm_font_size']); ?>px;
    color: <?php echo implode(", ", (array)$atts['cm_font_color']); ?>;
    background-color: <?php echo implode(", ", (array)$atts['cm_background_color']); ?>;
    font-family: <?php echo implode(", ", (array)$atts['cm_font_family']); ?>;
    text-align: <?php echo implode(", ", (array)$atts['cm_text_alignment']); ?>;
}
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .validation_error {
    border: <?php echo implode(", ", (array)$atts['ve_border_size']); ?>px <?php echo implode(", ", (array)$atts['ve_border_type']); ?> <?php echo implode(", ", (array)$atts['ve_border_color']); ?>;
    border-radius: <?php echo implode(", ", (array)$atts['ve_border_radius']); ?>px;
    max-width: <?php echo implode(", ", (array)$atts['ve_maximum_width']); ?>;
    font-size: <?php echo implode(", ", (array)$atts['ve_font_size']); ?>px;
    color: <?php echo implode(", ", (array)$atts['ve_font_color']); ?>;
    background-color:<?php echo implode(", ", (array)$atts['ve_background_color']); ?>;
    font-family: <?php echo implode(", ", (array)$atts['ve_font_family']); ?>;
    text-align: <?php echo implode(", ", (array)$atts['ve_error_position']); ?>;
}
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield_error .validation_message {
    border: <?php echo implode(", ", (array)$atts['ve_border_size']); ?>px <?php echo implode(", ", (array)$atts['ve_border_type']); ?> <?php echo implode(", ", (array)$atts['ve_border_color']); ?>;
    border-radius: <?php echo implode(", ", (array)$atts['ve_border_radius']); ?>px;
    max-width: 15%;
    font-size: 12px;
    color: <?php echo implode(", ", (array)$atts['ve_font_color']); ?>;
    background-color:<?php echo implode(", ", (array)$atts['ve_background_color']); ?>;
    font-family: <?php echo implode(", ", (array)$atts['ve_font_family']); ?>;
    text-align: <?php echo implode(", ", (array)$atts['ve_error_position']); ?>;
    margin-left: 10px;
}

body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_page_footer .gform_next_button {

    border: <?php echo implode(", ", (array)$atts['sb_border_size']); ?>px <?php echo implode(", ", (array)$atts['sb_border_type']); ?> <?php echo implode(", ", (array)$atts['sb_border_color']); ?>;
    background: <?php echo implode(", ", (array)$atts['sb_background_color']); ?>;
    color: <?php echo implode(", ", (array)$atts['sb_font_color']); ?>;
    font-size: <?php echo implode(", ", (array)$atts['sb_font_size']); ?>px;
    border-radius: <?php echo implode(", ", (array)$atts['sb_border_radius']); ?>px;
    font-family: <?php echo implode(", ", (array)$atts['sb_font_family']); ?>;
    width: <?php echo implode(", ", (array)$atts['sb_maximum_width']); ?>px;
    height: <?php echo implode(", ", (array)$atts['sb_height']); ?>px;
   
}
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_page_footer .gform_previous_button {

    border: <?php echo implode(", ", (array)$atts['sb_border_size']); ?>px <?php echo implode(", ", (array)$atts['sb_border_type']); ?> <?php echo implode(", ", (array)$atts['sb_border_color']); ?>;
    background: <?php echo implode(", ", (array)$atts['sb_background_color']); ?>;
    color: <?php echo implode(", ", (array)$atts['sb_font_color']); ?>;
    font-size: <?php echo implode(", ", (array)$atts['sb_font_size']); ?>px;
    border-radius: <?php echo implode(", ", (array)$atts['sb_border_radius']); ?>px;
    font-family: <?php echo implode(", ", (array)$atts['sb_font_family']); ?>;
    width: <?php echo implode(", ", (array)$atts['sb_maximum_width']); ?>px;
    height: <?php echo implode(", ", (array)$atts['sb_height']); ?>px;
   
}
<?php if(yes == $atts['use_icons']){ ?>
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield input[type=email]{
    background: url(<?php echo implode(", ", (array)$atts['email_icon']); ?>) no-repeat;
    padding: 11px 15px 10px 50px;
}
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield input[type=tel]{
    background: url(<?php echo implode(", ", (array)$atts['phone_icon']); ?>) no-repeat;
    padding: 11px 15px 10px 50px;
}
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield input[type=url]{
    background: url(<?php echo implode(", ", (array)$atts['url_icon']); ?>) no-repeat;
    padding: 11px 15px 10px 50px;
}
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield input[type=password]{
    background: url(<?php echo implode(", ", (array)$atts['password_icon']); ?>) no-repeat;
    padding: 11px 15px 10px 50px;
}
body #gform_wrapper_<?php echo implode(", ", (array)$atts['form_id']); ?> .gform_body .gform_fields .gfield .datepicker {
    background: url(<?php echo implode(", ", (array)$atts['date_icon']); ?>) no-repeat;
    padding: 11px 15px 10px 50px;
    width: 400px;
}
<?php } ?>
body .gform_wrapper form .gform_body ul,
body .gform_wrapper form .gform_body ul li { 
	list-style-type: none !important; 
	list-style-image: none !important;
	list-style: none !important; 
	background-image: none !important;
	background: none !important;
	padding: 0 !important;
	margin: 0 !important;
	border: none !important
}
body .gform_wrapper form .gform_body ul > li:before {
	content: "";
}
<?php echo implode(", ", (array)$atts['custom_css_code']); ?>


