<?php
if (!defined('ABSPATH')) exit;

class GFHANNANSMS_Pro_Configurations
{

    public static function construct()
    {

        if (defined('RG_CURRENT_PAGE') && in_array(RG_CURRENT_PAGE, array('admin-ajax.php'))) {
            add_action('wp_ajax_gf_select_hannansms_form', array('GFHANNANSMS_Pro_Configurations', 'select_forms_ajax'));
            add_action('wp_ajax_nopriv_gf_select_hannansms_form', array('GFHANNANSMS_Pro_Configurations', 'select_forms_ajax'));
        }

    }

    public static function configuration()
    {

        wp_register_style('gform_admin_sms', GFCommon::get_base_url() . '/css/admin.css');
        wp_print_styles(array('jquery-ui-styles', 'gform_admin_sms', 'wp-pointer')); ?>

        <div class="wrap gforms_edit_form gf_browser_gecko">

            <?php
            $id = !rgempty("hannansms_setting_id") ? rgpost("hannansms_setting_id") : absint(rgget("id"));
            $config = empty($id) ? array("is_active" => true, "meta" => array()) : GFHANNANSMS_Pro_SQL::get_feed($id);
            $get_feeds = GFHANNANSMS_Pro_SQL::get_feeds();
            $form_name = '';

            $_get_form_id = !empty($config["form_id"]) ? $config["form_id"] : rgget('fid');

            foreach ((array)$get_feeds as $get_feed) {
                if ($get_feed['id'] == $id) {
                    $form_name = $get_feed['form_title'];
                }
            }
            ?>

            <img alt="<?php _e("HANNANSMS", "GF_SMS") ?>"
                 style="position:absolute; top: 15px; <?php echo is_rtl() ? 'left:10px' : 'right:10px' ?>;"
                 src="<?php echo GF_SMS_URL ?>/assets/images/logo.png"/>
				 
				 
            <h2 class="gf_admin_page_title"><?php _e("SMS configuration for forms", "GF_SMS") ?>

                <?php if (!empty($_get_form_id)) { ?>
                    <span class="gf_admin_page_subtitle">
					<span class="gf_admin_page_formid"><?php echo sprintf(__("Feed ID: %s", "GF_SMS"), $id) ?></span>
					<span
                        class="gf_admin_page_formname"><?php echo sprintf(__("Form Name: %s", "GF_SMS"), $form_name) ?></span>
				</span>
                <?php } ?>

            </h2>
            <a class="button add-new-h2" href="admin.php?page=gf_settings&subview=gf_sms_pro"
               style="margin:8px 9px;"><?php _e("SMS General settings", "GF_SMS") ?></a>

            <?php
            $settings = GFHANNANSMS_Pro::get_option();
            $is_OK = (!empty($settings["ws"]) && $settings["ws"] != 'no');

            if ($is_OK) {
                echo '<div style="display:inline-table;margin-top:7px !important">';
                GFHANNANSMS_Pro::show_credit($settings["cr"], true);
                echo '</div>';
            } else {
                wp_die();
            }

            if (!rgempty("gf_hannansms_submit")) {

                check_admin_referer("update", "gf_hannansms_feed");
				
                $config["form_id"] = absint(rgpost("gf_hannansms_form"));
                $config["meta"]["from"] = rgpost("gf_hannansms_from");
                $config["meta"]["to"] = rgpost("gf_hannansms_to");
                $config["meta"]["to_c"] = rgpost("gf_hannansms_to_c");
                $config["meta"]["message"] = rgpost("gf_hannansms_message");
                $config["meta"]["message_c"] = rgpost("gf_hannansms_message_c");
                $config["meta"]["gf_sms_change_code"] = rgpost('gf_sms_change_code');
                $config["meta"]["gf_change_code_type"] = rgpost("gf_change_code_type");
                $config["meta"]["gf_code_static"] = rgpost("gf_code_static");
                $config["meta"]["gf_code_dyn"] = rgpost("hannansms_gf_code_dyn");
                $config["meta"]["gf_sms_is_gateway_checked"] = rgpost('gf_sms_is_gateway_checked');
                $config["meta"]["when"] = rgpost("gf_hannansms_when");
                $config["meta"]["adminsms_conditional_enabled"] = rgpost('gf_adminsms_conditional_enabled');
                $config["meta"]["adminsms_conditional_field_id"] = rgpost('gf_adminsms_conditional_field_id');
                $config["meta"]["adminsms_conditional_operator"] = rgpost('gf_adminsms_conditional_operator');
                $config["meta"]["adminsms_conditional_value"] = rgpost('gf_adminsms_conditional_value');
                $config["meta"]["clientsms_conditional_enabled"] = rgpost('gf_clientsms_conditional_enabled');
                $config["meta"]["clientsms_conditional_field_id"] = rgpost('gf_clientsms_conditional_field_id');
                $config["meta"]["clientsms_conditional_operator"] = rgpost('gf_clientsms_conditional_operator');
                $config["meta"]["clientsms_conditional_value"] = rgpost('gf_clientsms_conditional_value');
                $config["meta"]["customer_field_clientnum"] = rgpost("hannansms_customer_field_clientnum");

                $safe_data = array();
				foreach ( $config["meta"] as $key => $val )
					if ( ! is_array($val) )
						$safe_data[$key] = sanitize_text_field($val);
					else
						$safe_data[$key] = array_map( 'sanitize_text_field' , $val);
				$config["meta"] = $safe_data;
				
                $id = GFHANNANSMS_Pro_SQL::update_feed($id, $config["form_id"], $config["is_active"], $config["meta"]);

                if (!headers_sent()) {
                    wp_redirect(admin_url('admin.php?page=gf_hannansms&view=edit&id=' . $id . '&updated=true'));
                    exit;
                }
                ?>

                <div class="updated fade"
                     style="padding:6px"><?php echo sprintf(__("Feed Updated. %sback to list%s", "GF_SMS"), "<a href='?page=gf_hannansms'>", "</a>") ?></div>

                <?php
            }

            $_get_form_id = !empty($config["form_id"]) ? $config["form_id"] : rgget('fid');

            if (rgget('updated') == 'true') {

                $id = empty($id) && isset($_GET['id']) ? rgget('id') : $id; ?>

                <div class="updated fade"
                     style="padding:6px"><?php echo sprintf(__("Feed Updated. %sback to list%s", "GF_SMS"), "<a href='?page=gf_hannansms'>", "</a>") ?></div>

                <?php
            }

            if (!empty($_get_form_id)) { ?>

                <div id="gf_form_toolbar">
                    <ul id="gf_form_toolbar_links">

                        <?php
                        $menu_items = apply_filters('gform_toolbar_menu', GFForms::get_toolbar_menu_items($_get_form_id), $_get_form_id);
                        echo GFForms::format_toolbar_menu_items($menu_items); ?>

                        <li class="gf_form_switcher">
                            <label for="export_form"><?php _e('Select a feed', 'GF_SMS') ?></label>
                            <?php
                            $feeds = GFHANNANSMS_Pro_SQL::get_feeds();
                            if (RG_CURRENT_VIEW != 'entry') { ?>
                                <select name="form_switcher" id="form_switcher"
                                        onchange="GF_SwitchForm(jQuery(this).val());">
                                    <option value=""><?php _e('Switch SMS feed', 'GF_SMS') ?></option>
                                    <?php foreach ($feeds as $feed) {
                                        $selected = $feed["id"] == $id ? "selected='selected'" : ""; ?>
                                        <option
                                            value="<?php echo $feed["id"] ?>" <?php echo $selected ?> ><?php echo sprintf(__('Form:%s (Feed:%s)', 'GF_SMS'), $feed["form_title"], $feed["id"]) ?></option>
                                    <?php } ?>
                                </select>
                                <?php
                            }
                            ?>
                        </li>
                    </ul>
                </div>
            <?php } ?>

            <div id="gform_tab_group" class="gform_tab_group vertical_tabs">
                <?php if (!empty($_get_form_id)) { ?>
                    <ul id="gform_tabs" class="gform_tabs">

                        <?php
                        $title = '';
                        $get_form = GFFormsModel::get_form_meta($_get_form_id);
                        $current_tab = rgempty('subview', $_GET) ? 'settings' : rgget('subview');
                        $current_tab = !empty($current_tab) ? $current_tab : '';
                        $setting_tabs = GFFormSettings::get_tabs($get_form['id']);
                        if (!empty($current_tab)) {
                            foreach ($setting_tabs as $tab) {
                                if ($tab['name'] == $current_tab) {
                                    $title = $tab['label'];
                                }
                                $query = array('page' => 'gf_edit_forms', 'view' => 'settings', 'subview' => $tab['name'], 'id' => $get_form['id']);
                                $url = add_query_arg($query, admin_url('admin.php'));
                                echo $tab['name'] == 'sms' ? '<li class="active">' : '<li>';
                                ?>
                                <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($tab['label']) ?></a>
                                <span></span>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                <?php } ?>

                <div id="gform_tab_container_<?php echo $_get_form_id ? $_get_form_id : 1 ?>"
                     class="gform_tab_container">
                    <div class="gform_tab_content" id="tab_<?php echo !empty($current_tab) ? $current_tab : '' ?>">
                        <div id="form_settings" class="gform_panel gform_panel_form_settings">
                            <h3>
								<span>
									<i class="fa fa-mobile"></i>
                                    <?php _e("General configuration", "GF_SMS"); ?>
								</span>
                            </h3>
                            <form method="post" action="" id="gform_form_settings">
							
								<?php wp_nonce_field("update", "gf_hannansms_feed") ?>
								
                                <input type="hidden" name="hannansms_setting_id" value="<?php echo $id ?>"/>
                                <table class="gforms_form_settings" cellspacing="0" cellpadding="0">
                                    <tbody>
                                    <tr>
                                        <td colspan="2">
                                            <h4 class="gf_settings_subgroup_title">
                                                <?php _e("General configuration", "GF_SMS"); ?>
                                            </h4>
                                        </td>
                                    </tr>

                                    <tr id="hannansms_form_container">
                                        <th>
                                            <?php _e("Select form", "GF_SMS"); ?>
                                        </th>
                                        <td>
                                            <select id="gf_hannansms_form" name="gf_hannansms_form"
                                                    onchange="SelectForm(jQuery(this).val());">
                                                <option
                                                    value=""><?php _e("Please select a form", "GF_SMS"); ?> </option>
                                                <?php
                                                $forms = RGFormsModel::get_forms();
                                                foreach ((array)$forms as $form) {
                                                    $selected = absint($form->id) == $_get_form_id ? "selected='selected'" : ""; ?>
                                                    <option
                                                        value="<?php echo absint($form->id) ?>" <?php echo $selected ?>><?php echo esc_html($form->title) ?></option>
                                                <?php } ?>
                                            </select>&nbsp;&nbsp;
                                            <img src="<?php echo esc_url(GFCommon::get_base_url()) ?>/images/spinner.gif"
                                                 id="hannansms_wait" style="display: none;"/>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                <table class="gforms_form_settings"
                                       id="hannansms_field_group" <?php echo empty($_get_form_id) ? "style='display:none;'" : "" ?>
                                       cellspacing="0" cellpadding="0">
                                    <tbody>
                                    <tr>
                                        <th>
                                            <?php _e("Sender Number", "GF_SMS"); ?>
                                        </th>
                                        <td>
                                            <select id="gf_hannansms_from" name="gf_hannansms_from">
                                                <option value=""><?php _e("Select Sender Number", "GF_SMS"); ?></option>
                                                <?php
                                                $sender_num = isset($settings["from"]) ? $settings["from"] : '';
                                                if ($sender_num == '' || strpos($settings["from"], ',') === false) {
                                                    if ($sender_num and $sender_num != '') {
                                                        $selected = (isset($config["meta"]["from"]) && $sender_num == $config["meta"]["from"]) ? "selected='selected'" : "";
                                                        ?>
                                                        <option
                                                            value="<?php echo $sender_num ?>" <?php echo $selected ?> ><?php echo $sender_num ?></option>
                                                        <?php
                                                    }
                                                } else {
                                                    unset($sender_num);
                                                    $sender_nums = array();
                                                    if (!empty($settings["from"]))
                                                        $sender_nums = explode(',', $settings["from"]);
                                                    foreach ((array)$sender_nums as $sender_num) {
                                                        $selected = (isset($config["meta"]["from"]) && $sender_num == $config["meta"]["from"]) ? "selected='selected'" : "";
                                                        ?>
                                                        <option
                                                            value="<?php echo $sender_num ?>" <?php echo $selected ?> ><?php echo $sender_num ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <br/>
                                        </td>
                                    </tr>


                                    <tr>
                                        <th>
                                            <?php _e("Payment Gateway integration", "GF_SMS"); ?>
                                        </th>
                                        <td>
                                            <input type="checkbox" id="gf_sms_is_gateway_checked"
                                                   name="gf_sms_is_gateway_checked" value="1"
                                                   onclick="if(this.checked){jQuery('#gf_sms_is_gateway_checked_box').fadeIn('fast');} else{ jQuery('#gf_sms_is_gateway_checked_box').fadeOut('fast'); }" <?php echo rgar($config['meta'], 'gf_sms_is_gateway_checked') ? "checked='checked'" : "" ?>/>
                                            <label style="font-family:tahoma !important;"
                                                   for="gf_sms_is_gateway_checked"><?php _e("Check if only your form is connected to payment gateways.", "GF_SMS"); ?></label><br/>
                                            <table cellspacing="0" cellpadding="0">

                                                <tr>
                                                    <td>
                                                        <div id="gf_sms_is_gateway_checked_box">
                                                            <p class="HANNANSmsp"><?php _e("Sending time configuration : ", "GF_SMS") ?></p>
                                                            <select id="gf_hannansms_when" name="gf_hannansms_when">
                                                                <option
                                                                    value="send_immediately" <?php echo (isset($config["meta"]["when"]) && "send_immediately" == $config["meta"]["when"]) ? "selected='selected'" : ""; ?> ><?php _e("Immediately after form submission", "GF_SMS"); ?> </option>
                                                                <option
                                                                    value="after_pay" <?php echo (isset($config["meta"]["when"]) && "after_pay" == $config["meta"]["when"]) ? "selected='selected'" : ""; ?> ><?php _e("After Payment(All payment statuses)", "GF_SMS"); ?> </option>
                                                                <option
                                                                    value="after_pay_success" <?php echo (isset($config["meta"]["when"]) && "after_pay_success" == $config["meta"]["when"]) ? "selected='selected'" : ""; ?> ><?php _e("After Successful Payment", "GF_SMS"); ?> </option>
                                                            </select>
                                                            <p class="description"><?php _e('<strong>Note : </strong>Your Payment gateway must be standard. it is required to use "gform_post_payment_status" action.', 'GF_SMS') ?></p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>
                                            <?php _e("Change Country Code", "GF_SMS"); ?>
                                        </th>
                                        <td>
                                            <input type="checkbox" id="gf_sms_change_code" name="gf_sms_change_code"
                                                   value="1"
                                                   onclick="if(this.checked){jQuery('#gf_sms_change_code_box').fadeIn('fast');} else{ jQuery('#gf_sms_change_code_box').fadeOut('fast'); }" <?php echo rgar($config['meta'], 'gf_sms_change_code') ? "checked='checked'" : "" ?>/>
                                            <label for="gf_sms_change_code"
                                                   style="font-family:tahoma !important;"><?php _e("Check if you want to change default country code.", "GF_SMS"); ?></label><br/>
                                            <table cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td>

                                                        <div id="gf_sms_change_code_box">


                                                            <input type="radio" name="gf_change_code_type"
                                                                   id="gf_change_code_type_static" size="10"
                                                                   value="static" <?php echo rgar($config['meta'], 'gf_change_code_type') != 'dyn' ? "checked='checked'" : "" ?>/>
                                                            <label for="gf_change_code_type_static" class="inline">
                                                                <?php _e('Static', 'GF_SMS'); ?>
                                                            </label>

                                                            <input type="radio" name="gf_change_code_type"
                                                                   id="gf_change_code_type_dyn" size="10"
                                                                   value="dyn" <?php echo rgar($config['meta'], 'gf_change_code_type') == 'dyn' ? "checked='checked'" : "" ?>/>
                                                            <label for="gf_change_code_type_dyn" class="inline">
                                                                <?php _e('Dynamic', 'GF_SMS'); ?>
                                                            </label>

                                                            <input type="text" name="gf_code_static"
                                                                   id="hannansms_gf_code_static"
                                                                   value="<?php echo isset($config["meta"]["gf_code_static"]) ? esc_attr($config["meta"]["gf_code_static"]) : (isset($settings["code"]) ? $settings["code"] : ''); ?>"
                                                                   style="direction:ltr !important; text-align:left;">
																
																<span id="hannansms_gf_code_dyn_div">
																<?php
                                                                if (!empty($_get_form_id)) {
                                                                    $form_meta = RGFormsModel::get_form_meta($_get_form_id);
                                                                    echo !empty($form_meta) ? self::get_country_code($form_meta, $config) : '';
                                                                }
                                                                ?>
																</span>

                                                            <p class="description"><?php _e('<strong>Note : </strong>You can change the default country code. but If entered mobile phone number was international format, this country code will be effectless.', 'GF_SMS'); ?></p>

                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>


                                    <tr>
                                        <td colspan="2">
                                            <h4 class="gf_settings_subgroup_title">
                                                <?php _e("Admin SMS Configuration : ", "GF_SMS"); ?>
                                                <?php _e("Leave blank for unsending.", "GF_SMS"); ?>
                                            </h4>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>
                                            <?php _e("Admin Numbers", "GF_SMS"); ?>
                                        </th>
                                        <td>
                                            <input type="text" class="fieldwidth-1" name="gf_hannansms_to"
                                                   value="<?php echo isset($config["meta"]["to"]) ? esc_attr($config["meta"]["to"]) : (isset($settings["to"]) ? $settings["to"] : ''); ?>"
                                                   style="direction:ltr !important; text-align:left;">
                                            <span
                                                class="description"><?php _e("Separate with commas (,). Format with a '+' and country code e.g., +16175551212", "GF_SMS") ?></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>
                                            <?php _e("Admin Message Body", "GF_SMS"); ?>
                                        </th>
                                        <td>
                                            <select id="gf_hannansms_message_variable_select"
                                                    onchange="InsertVariable('gf_hannansms_message');">
                                                <?php if (!empty($_get_form_id)) {
                                                    $form_meta = RGFormsModel::get_form_meta($_get_form_id);
                                                    echo !empty($form_meta) ? self::get_form_fields_merge($form_meta) : '';
                                                } ?>
                                            </select>
                                            <br/>
                                            <textarea id="gf_hannansms_message" name="gf_hannansms_message"
                                                      style="height: 150px; width:550px;"><?php echo rgget("message", $config["meta"]) ?></textarea>
                                        </td>
                                    </tr>

                                    <tr id="gf_adminsms_conditional_option">
                                        <th>
                                            <?php _e("Conditional Logic", "GF_SMS"); ?>
                                        </th>
                                        <td>
                                            <input type="checkbox" id="gf_adminsms_conditional_enabled"
                                                   name="gf_adminsms_conditional_enabled" value="1"
                                                   onclick="if(this.checked){jQuery('#gf_adminsms_conditional_container').fadeIn('fast');} else{ jQuery('#gf_adminsms_conditional_container').fadeOut('fast'); }" <?php echo rgar($config['meta'], 'adminsms_conditional_enabled') ? "checked='checked'" : "" ?>/>
                                            <label style="font-family:tahoma !important;"
                                                   for="gf_adminsms_conditional_enabled"><?php _e("Enable Condition for admin", "GF_SMS"); ?></label><br/>
                                            <table cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td>
                                                        <div
                                                            id="gf_adminsms_conditional_container" <?php echo !rgar($config['meta'], 'adminsms_conditional_enabled') ? "style='display:none'" : "" ?>>
                                                            <div id="gf_adminsms_conditional_fields"
                                                                 style="display:none">
                                                                <p class="HANNANSmsp"><?php _e("Send SMS to Admin if :", "GF_SMS") ?></p>
                                                                <select id="gf_adminsms_conditional_field_id"
                                                                        name="gf_adminsms_conditional_field_id"
                                                                        class="optin_select"
                                                                        onchange='jQuery("#gf_adminsms_conditional_value_container").html(GetFieldValues_admin(jQuery(this).val(), "", 20));'></select>
                                                                <select
                                                                    style="font-family:tahoma !important; width:148px;"
                                                                    id="gf_adminsms_conditional_operator"
                                                                    name="gf_adminsms_conditional_operator">
                                                                    <option
                                                                        value="is" <?php echo rgar($config['meta'], 'adminsms_conditional_operator') == "is" ? "selected='selected'" : "" ?>><?php _e("is", "GF_SMS") ?></option>
                                                                    <option
                                                                        value="isnot" <?php echo rgar($config['meta'], 'adminsms_conditional_operator') == "isnot" ? "selected='selected'" : "" ?>><?php _e("is not", "GF_SMS") ?></option>
                                                                    <option
                                                                        value=">" <?php echo rgar($config['meta'], 'adminsms_conditional_operator') == ">" ? "selected='selected'" : "" ?>><?php _e("greater than", "GF_SMS") ?></option>
                                                                    <option
                                                                        value="<" <?php echo rgar($config['meta'], 'adminsms_conditional_operator') == "<" ? "selected='selected'" : "" ?>><?php _e("less than", "GF_SMS") ?></option>
                                                                    <option
                                                                        value="contains" <?php echo rgar($config['meta'], 'adminsms_conditional_operator') == "contains" ? "selected='selected'" : "" ?>><?php _e("contains", "GF_SMS") ?></option>
                                                                    <option
                                                                        value="starts_with" <?php echo rgar($config['meta'], 'adminsms_conditional_operator') == "starts_with" ? "selected='selected'" : "" ?>><?php _e("starts with", "GF_SMS") ?></option>
                                                                    <option
                                                                        value="ends_with" <?php echo rgar($config['meta'], 'adminsms_conditional_operator') == "ends_with" ? "selected='selected'" : "" ?>><?php _e("ends with", "GF_SMS") ?></option>
                                                                </select>
                                                                <div id="gf_adminsms_conditional_value_container"
                                                                     name="gf_adminsms_conditional_value_container"
                                                                     style="display:inline;"></div>
                                                            </div>
                                                            <div id="gf_adminsms_conditional_message"
                                                                 style="display:none;background-color:#FFDFDF; margin-top:4px; margin-bottom:6px; padding-top:6px; padding:18px; border:1px dotted #C89797;">
                                                                <?php _e("To create a registration condition, your form must have a field supported by conditional logic.", "GF_SMS") ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <h4 class="gf_settings_subgroup_title">
                                                <?php _e("User SMS Configuration :", "GF_SMS"); ?>
                                                <?php _e("Leave them blank for unsending.", "GF_SMS"); ?>
                                            </h4>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php _e("Phone Number Mapping", "GF_SMS"); ?>
                                        </th>
                                        <td id="hannansms_customer_field">
                                            <?php
                                            if (!empty($_get_form_id)) {
                                                $form_meta = RGFormsModel::get_form_meta($_get_form_id);
                                                echo !empty($form_meta) ? self::get_client_information($form_meta, $config) : '';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php _e("Extra Numbers", "GF_SMS"); ?>
                                        </th>
                                        <td>
                                            <input type="text" class="fieldwidth-1" name="gf_hannansms_to_c"
                                                   value="<?php echo isset($config["meta"]["to_c"]) ? esc_attr($config["meta"]["to_c"]) : ''; ?>"
                                                   style="direction:ltr !important; text-align:left;">

                                            <span
                                                class="description"><?php _e("Separate with commas (,). Format with a '+' and country code e.g., +16175551212", "GF_SMS") ?></span>

                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <?php _e("User SMS Body", "GF_SMS"); ?>
                                        </th>
                                        <td>
                                            <select id="gf_hannansms_message_c_variable_select"
                                                    onchange="InsertVariable('gf_hannansms_message_c');">
                                                <?php
                                                if (!empty($_get_form_id)) {
                                                    $form_meta = RGFormsModel::get_form_meta($_get_form_id);
                                                    echo !empty($form_meta) ? self::get_form_fields_merge($form_meta) : '';
                                                }
                                                ?>
                                            </select>
                                            <br/>
                                            <textarea id="gf_hannansms_message_c" name="gf_hannansms_message_c"
                                                      style="height: 150px; width:550px;"><?php echo rgget("message_c", $config["meta"]) ?></textarea>
                                        </td>
                                    </tr>
                                    <tr id="gf_clientsms_conditional_option">
                                        <th>
                                            <?php _e("Condition Logic", "GF_SMS"); ?>
                                        </th>
                                        <td>
                                            <input type="checkbox" id="gf_clientsms_conditional_enabled"
                                                   name="gf_clientsms_conditional_enabled" value="1"
                                                   onclick="if(this.checked){jQuery('#gf_clientsms_conditional_container').fadeIn('fast');} else{ jQuery('#gf_clientsms_conditional_container').fadeOut('fast'); }" <?php echo rgar($config['meta'], 'clientsms_conditional_enabled') ? "checked='checked'" : "" ?>/>
                                            <label style="font-family:tahoma !important;"
                                                   for="gf_clientsms_conditional_enabled"><?php _e("Enable Condition for users", "GF_SMS"); ?></label><br/>
                                            <table cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td>
                                                        <div
                                                            id="gf_clientsms_conditional_container" <?php echo !rgar($config['meta'], 'clientsms_conditional_enabled') ? "style='display:none'" : "" ?>>
                                                            <div id="gf_clientsms_conditional_fields"
                                                                 style="display:none">
                                                                <p class="HANNANSmsp"><?php _e("Send SMS to Users if : ", "GF_SMS") ?></p>
                                                                <select id="gf_clientsms_conditional_field_id"
                                                                        name="gf_clientsms_conditional_field_id"
                                                                        class="optin_selectc"
                                                                        onchange='jQuery("#gf_clientsms_conditional_value_container").html(GetFieldValues_client(jQuery(this).val(), "", 20));'></select>
                                                                <select
                                                                    style="font-family:tahoma !important; width:148px;"
                                                                    id="gf_clientsms_conditional_operator"
                                                                    name="gf_clientsms_conditional_operator">
                                                                    <option
                                                                        value="is" <?php echo rgar($config['meta'], 'clientsms_conditional_operator') == "is" ? "selected='selected'" : "" ?>><?php _e("is", "GF_SMS") ?></option>
                                                                    <option
                                                                        value="isnot" <?php echo rgar($config['meta'], 'clientsms_conditional_operator') == "isnot" ? "selected='selected'" : "" ?>><?php _e("is not", "GF_SMS") ?></option>
                                                                    <option
                                                                        value=">" <?php echo rgar($config['meta'], 'clientsms_conditional_operator') == ">" ? "selected='selected'" : "" ?>><?php _e("greater than", "GF_SMS") ?></option>
                                                                    <option
                                                                        value="<" <?php echo rgar($config['meta'], 'clientsms_conditional_operator') == "<" ? "selected='selected'" : "" ?>><?php _e("less than", "GF_SMS") ?></option>
                                                                    <option
                                                                        value="contains" <?php echo rgar($config['meta'], 'clientsms_conditional_operator') == "contains" ? "selected='selected'" : "" ?>><?php _e("contains", "GF_SMS") ?></option>
                                                                    <option
                                                                        value="starts_with" <?php echo rgar($config['meta'], 'clientsms_conditional_operator') == "starts_with" ? "selected='selected'" : "" ?>><?php _e("starts with", "GF_SMS") ?></option>
                                                                    <option
                                                                        value="ends_with" <?php echo rgar($config['meta'], 'clientsms_conditional_operator') == "ends_with" ? "selected='selected'" : "" ?>><?php _e("ends with", "GF_SMS") ?></option>
                                                                </select>
                                                                <div id="gf_clientsms_conditional_value_container"
                                                                     name="gf_clientsms_conditional_value_container"
                                                                     style="display:inline;"></div>
                                                            </div>
                                                            <div id="gf_clientsms_conditional_message"
                                                                 style="display:none; font-family:tahoma !important;">
                                                                <br/>
                                                                <?php _e("To create a registration condition, your form must have a field supported by conditional logic.", "GF_SMS") ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>


                                    <tr>
                                        <td>
                                            <input type="submit" class="button-primary gfbutton"
                                                   name="gf_hannansms_submit" value="<?php _e("Save", "GF_SMS"); ?>"/>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            var form = Array();
            function GF_ReplaceQuery(key, newValue) {
                var new_query = "";
                var query = document.location.search.substring(1);
                var ary = query.split("&");
                var has_key = false;
                for (i = 0; i < ary.length; i++) {
                    var key_value = ary[i].split("=");
                    if (key_value[0] == key) {
                        new_query += key + "=" + newValue + "&";
                        has_key = true;
                    }
                    else if (key_value[0] != "display_settings") {
                        new_query += key_value[0] + "=" + key_value[1] + "&";
                    }
                }
                if (new_query.length > 0)
                    new_query = new_query.substring(0, new_query.length - 1);
                if (!has_key)
                    new_query += new_query.length > 0 ? "&" + key + "=" + newValue : "?" + key + "=" + newValue;
                return new_query;
            }
            function GF_RemoveQuery(key, query) {
                var new_query = "";
                if (query == "") {
                    query = document.location.search.substring(1);
                }
                var ary = query.split("&");
                for (i = 0; i < ary.length; i++) {
                    var key_value = ary[i].split("=");
                    if (key_value[0] != key) {
                        new_query += key_value[0] + "=" + key_value[1] + "&";
                    }
                }
                if (new_query.length > 0)
                    new_query = new_query.substring(0, new_query.length - 1);
                return new_query;
            }

            function GF_SwitchForm(id) {
                if (id.length > 0) {
                    query = GF_ReplaceQuery("id", id);
                    new_query = GF_RemoveQuery("paged", query);
                    new_query = new_query.replace("gf_new_form", "gf_edit_forms");
                    new_query = GF_RemoveQuery("s", new_query);
                    new_query = GF_RemoveQuery("operator", new_query);
                    new_query = GF_RemoveQuery("type", new_query);
                    new_query = GF_RemoveQuery("field_id", new_query);
                    var is_form_settings = new_query.indexOf("page=gf_edit_forms") >= 0 && new_query.indexOf("view=settings");
                    if (is_form_settings) {
                        new_query = "page=gf_hannansms&view=edit&id=" + id;
                    }
                    document.location = "?" + new_query;
                }
            }
            function ToggleFormSettings() {
                FieldClick(jQuery('#gform_heading')[0]);
            }
            jQuery(document).ready(function () {
                if (document.location.search.indexOf("display_settings") > 0)
                    ToggleFormSettings()
                jQuery('a.gf_toolbar_disabled').click(function (event) {
                    event.preventDefault();
                });
            });
            function SelectForm(formId) {
                if (!formId) {
                    jQuery("#hannansms_field_group").slideUp();
                    return;
                }
                jQuery("#hannansms_wait").show();
                jQuery("#hannansms_field_group").slideUp();
                jQuery.post(ajaxurl, {
                        action: "gf_select_hannansms_form",
                        gf_select_hannansms_form: "<?php echo wp_create_nonce("gf_select_hannansms_form") ?>",
                        form_id: formId,
                        cookie: encodeURIComponent(document.cookie)
                    },
                    function (data) {
                        form = data.form;
                        fields = data["fields"];
                        customer_field = data["customer_field"];
                        gf_code = data["gf_code"];
                        jQuery("#gf_sms_is_gateway_checked_box").hide();
                        jQuery("#gf_sms_change_code_box").hide();
                        jQuery("#gf_sms_is_gateway_checked").attr('checked', false);
                        jQuery("#gf_sms_change_code").attr('checked', false);
                        jQuery("#gf_hannansms_message_variable_select").html(fields);
                        jQuery("#gf_hannansms_message_c_variable_select").html(fields);
                        jQuery("#hannansms_customer_field").html(customer_field);
                        jQuery("#hannansms_gf_code_dyn_div").html(gf_code);
                        jQuery("#gf_adminsms_conditional_enabled").attr('checked', false);
                        Set_Admin_Condition("", "");
                        jQuery("#gf_clientsms_conditional_enabled").attr('checked', false);
                        Set_Clients_Condition("", "");
                        jQuery("#hannansms_field_group").slideDown();
                        jQuery("#hannansms_wait").hide();
                    }, "json"
                );
            }
            function InsertVariable(element_id, callback, variable) {
                if (!variable)
                    variable = jQuery('#' + element_id + '_variable_select').val();
                var messageElement = jQuery("#" + element_id);
                if (document.selection) {
                    messageElement[0].focus();
                    document.selection.createRange().text = variable;
                }
                else if (messageElement[0].selectionStart) {
                    obj = messageElement[0]
                    obj.value = obj.value.substr(0, obj.selectionStart) + variable + obj.value.substr(obj.selectionEnd, obj.value.length);
                }
                else {
                    messageElement.val(variable + messageElement.val());
                }
                jQuery('#' + element_id + '_variable_select')[0].selectedIndex = 0;
                if (callback && window[callback])
                    window[callback].call();
            }
            form = <?php echo !empty($form_meta) ? GFCommon::json_encode($form_meta) : GFCommon::json_encode(array()) ?>;
            jQuery(document).ready(function () {
                <?php if( !rgar($config['meta'], 'gf_sms_is_gateway_checked') ) { ?>
                jQuery("#gf_sms_is_gateway_checked_box").hide();
                <?php  }
                if( isset($config['meta']) && !rgar($config['meta'], 'gf_sms_change_code') ) { ?>
                jQuery("#gf_sms_change_code_box").hide();
                <?php  }
                if ( isset($config['meta']) && rgar($config['meta'], 'gf_change_code_type') == 'dyn' ) { ?>
                jQuery("#hannansms_gf_code_static").hide();
                jQuery("#hannansms_gf_code_dyn").show();
                jQuery("#hannansms_gf_code_dyn_div").show("slow");
                <?php  } else { ?>
                jQuery("#hannansms_gf_code_static").show();
                jQuery("#hannansms_gf_code_dyn").hide();
                jQuery("#hannansms_gf_code_dyn_div").hide();
                <?php  } ?>
                jQuery('input[name="gf_change_code_type"]').on("click", function () {
                    if (jQuery('input[name="gf_change_code_type"]:checked').val() == 'dyn') {
                        jQuery("#hannansms_gf_code_dyn").show("slow");
                        jQuery("#hannansms_gf_code_dyn_div").show("slow");
                        jQuery("#hannansms_gf_code_static").hide("slow");
                    }
                    else {
                        jQuery("#hannansms_gf_code_dyn").hide("slow");
                        jQuery("#hannansms_gf_code_dyn_div").hide("slow");
                        jQuery("#hannansms_gf_code_static").show("slow");
                    }
                });
                var selectedField = '';
                var selectedValue = '';
                <?php if ( !empty($config["meta"]["adminsms_conditional_field_id"])  ) { ?>
                var selectedField = "<?php echo str_replace('"', '\"', $config["meta"]["adminsms_conditional_field_id"])?>";
                var selectedValue = "<?php echo str_replace('"', '\"', $config["meta"]["adminsms_conditional_value"])?>";
                <?php } ?>
                Set_Admin_Condition(selectedField, selectedValue);

                var selectedFieldc = '';
                var selectedValuec = '';
                <?php if ( !empty($config["meta"]["clientsms_conditional_field_id"])  ) { ?>
                var selectedFieldc = "<?php echo str_replace('"', '\"', $config["meta"]["clientsms_conditional_field_id"])?>";
                var selectedValuec = "<?php echo str_replace('"', '\"', $config["meta"]["clientsms_conditional_value"])?>";
                <?php } ?>
                Set_Clients_Condition(selectedFieldc, selectedValuec);
            });

            function Set_Admin_Condition(selectedField, selectedValue) {
                jQuery("#gf_adminsms_conditional_field_id").html(GetSelectableFields_admin(selectedField, 20));
                var optinConditionField = jQuery("#gf_adminsms_conditional_field_id").val();
                var checked = jQuery("#gf_adminsms_conditional_enabled").attr('checked');
                if (optinConditionField) {
                    jQuery("#gf_adminsms_conditional_message").hide();
                    jQuery("#gf_adminsms_conditional_fields").show();
                    jQuery("#gf_adminsms_conditional_value_container").html(GetFieldValues_admin(optinConditionField, selectedValue, 20));
                    jQuery("#gf_adminsms_conditional_value").val(selectedValue);
                }
                else {
                    jQuery("#gf_adminsms_conditional_message").show();
                    jQuery("#gf_adminsms_conditional_fields").hide();
                }
                if (!checked) jQuery("#gf_adminsms_conditional_container").hide();
            }
            function GetFieldValues_admin(fieldId, selectedValue, labelMaxCharacters) {
                if (!fieldId)
                    return "";
                var str = "";
                var field = GetFieldById(fieldId);
                if (!field)
                    return "";
                var isAnySelected = false;
                if (field["type"] == "post_category" && field["displayAllCategories"]) {
                    str += '<?php $dd = wp_dropdown_categories(array("class" => "optin_select", "orderby" => "name", "id" => "gf_adminsms_conditional_value", "name" => "gf_adminsms_conditional_value", "hierarchical" => true, "hide_empty" => 0, "echo" => false)); echo str_replace("\n", "", str_replace("'", "\\'", $dd)); ?>';
                }
                else if (field.choices) {
                    str += '<select id="gf_adminsms_conditional_value" name="gf_adminsms_conditional_value" class="optin_select">'
                    for (var i = 0; i < field.choices.length; i++) {
                        var fieldValue = field.choices[i].value ? field.choices[i].value : field.choices[i].text;
                        var isSelected = fieldValue == selectedValue;
                        var selected = isSelected ? "selected='selected'" : "";
                        if (isSelected)
                            isAnySelected = true;
                        str += "<option value='" + fieldValue.replace(/'/g, "&#039;") + "' " + selected + ">" + TruncateMiddle_admin(field.choices[i].text, labelMaxCharacters) + "</option>";
                    }
                    if (!isAnySelected && selectedValue) {
                        str += "<option value='" + selectedValue.replace(/'/g, "&#039;") + "' selected='selected'>" + TruncateMiddle_admin(selectedValue, labelMaxCharacters) + "</option>";
                    }
                    str += "</select>";
                }
                else {
                    selectedValue = selectedValue ? selectedValue.replace(/'/g, "&#039;") : "";
                    str += "<input type='text' style='padding:5px' placeholder='<?php _e("Enter value", "GF_SMS"); ?>' id='gf_adminsms_conditional_value' name='gf_adminsms_conditional_value' value='" + selectedValue.replace(/'/g, "&#039;") + "'>";
                }
                return str;
            }
            function TruncateMiddle_admin(text, maxCharacters) {
                if (!text)
                    return "";
                if (text.length <= maxCharacters)
                    return text;
                var middle = parseInt(maxCharacters / 2);
                return text.substr(0, middle) + "..." + text.substr(text.length - middle, middle);
            }
            function GetSelectableFields_admin(selectedFieldId, labelMaxCharacters) {
                var str = "";
                var inputType;
                for (var i = 0; i < form.fields.length; i++) {
                    fieldLabel = form.fields[i].adminLabel ? form.fields[i].adminLabel : form.fields[i].label;
                    inputType = form.fields[i].inputType ? form.fields[i].inputType : form.fields[i].type;
                    if (IsConditionalLogicField(form.fields[i])) {
                        var selected = form.fields[i].id == selectedFieldId ? "selected='selected'" : "";
                        str += "<option value='" + form.fields[i].id + "' " + selected + ">" + TruncateMiddle_admin(fieldLabel, labelMaxCharacters) + "</option>";
                    }
                }
                return str;
            }
            function Set_Clients_Condition(selectedField, selectedValue) {
                jQuery("#gf_clientsms_conditional_field_id").html(GetSelectableFields_client(selectedField, 20));
                var optinConditionField = jQuery("#gf_clientsms_conditional_field_id").val();
                var checked = jQuery("#gf_clientsms_conditional_enabled").attr('checked');
                if (optinConditionField) {
                    jQuery("#gf_clientsms_conditional_message").hide();
                    jQuery("#gf_clientsms_conditional_fields").show();
                    jQuery("#gf_clientsms_conditional_value_container").html(GetFieldValues_client(optinConditionField, selectedValue, 20));
                    jQuery("#gf_clientsms_conditional_value").val(selectedValue);
                }
                else {
                    jQuery("#gf_clientsms_conditional_message").show();
                    jQuery("#gf_clientsms_conditional_fields").hide();
                }
                if (!checked) jQuery("#gf_clientsms_conditional_container").hide();

            }
            function GetFieldValues_client(fieldId, selectedValue, labelMaxCharacters) {
                if (!fieldId)
                    return "";
                var str = "";
                var field = GetFieldById(fieldId);
                if (!field)
                    return "";
                var isAnySelected = false;
                if (field["type"] == "post_category" && field["displayAllCategories"]) {
                    str += '<?php $dd = wp_dropdown_categories(array("class" => "optin_selectc", "orderby" => "name", "id" => "gf_clientsms_conditional_value", "name" => "gf_clientsms_conditional_value", "hierarchical" => true, "hide_empty" => 0, "echo" => false)); echo str_replace("\n", "", str_replace("'", "\\'", $dd)); ?>';
                }
                else if (field.choices) {
                    str += '<select id="gf_clientsms_conditional_value" name="gf_clientsms_conditional_value" class="optin_selectc">'
                    for (var i = 0; i < field.choices.length; i++) {
                        var fieldValue = field.choices[i].value ? field.choices[i].value : field.choices[i].text;
                        var isSelected = fieldValue == selectedValue;
                        var selected = isSelected ? "selected='selected'" : "";
                        if (isSelected)
                            isAnySelected = true;
                        str += "<option value='" + fieldValue.replace(/'/g, "&#039;") + "' " + selected + ">" + TruncateMiddle_client(field.choices[i].text, labelMaxCharacters) + "</option>";
                    }
                    if (!isAnySelected && selectedValue) {
                        str += "<option value='" + selectedValue.replace(/'/g, "&#039;") + "' selected='selected'>" + TruncateMiddle_client(selectedValue, labelMaxCharacters) + "</option>";
                    }
                    str += "</select>";
                }
                else {
                    selectedValue = selectedValue ? selectedValue.replace(/'/g, "&#039;") : "";
                    str += "<input type='text' style='padding:5px' placeholder='<?php _e("Enter value", "GF_SMS"); ?>' id='gf_clientsms_conditional_value' name='gf_clientsms_conditional_value' value='" + selectedValue.replace(/'/g, "&#039;") + "'>";
                }
                return str;
            }
            function TruncateMiddle_client(text, maxCharacters) {
                if (!text)
                    return "";
                if (text.length <= maxCharacters)
                    return text;
                var middle = parseInt(maxCharacters / 2);
                return text.substr(0, middle) + "..." + text.substr(text.length - middle, middle);
            }
            function GetSelectableFields_client(selectedFieldId, labelMaxCharacters) {
                var str = "";
                var inputType;
                for (var i = 0; i < form.fields.length; i++) {
                    fieldLabel = form.fields[i].adminLabel ? form.fields[i].adminLabel : form.fields[i].label;
                    inputType = form.fields[i].inputType ? form.fields[i].inputType : form.fields[i].type;
                    if (IsConditionalLogicField(form.fields[i])) {
                        var selected = form.fields[i].id == selectedFieldId ? "selected='selected'" : "";
                        str += "<option value='" + form.fields[i].id + "' " + selected + ">" + TruncateMiddle_client(fieldLabel, labelMaxCharacters) + "</option>";
                    }
                }
                return str;
            }
            function GetFieldById(fieldId) {
                for (var i = 0; i < form.fields.length; i++) {
                    if (form.fields[i].id == fieldId)
                        return form.fields[i];
                }
                return null;
            }
            function IsConditionalLogicField(field) {
                inputType = field.inputType ? field.inputType : field.type;
                var supported_fields = ["checkbox", "radio", "select", "text", "website", "textarea", "email", "hidden", "number", "phone", "multiselect", "post_title",
                    "post_tags", "post_custom_field", "post_content", "post_excerpt"];
                var index = jQuery.inArray(inputType, supported_fields);
                return index >= 0;
            }
        </script>
        <?php
    }

    public static function get_client_information($form, $config)
    {
        $form_fields = self::get_client_form_fields($form);
        $str = "";
        $selected_field = $config && !empty($config["meta"]["customer_field_clientnum"]) ? $config["meta"]["customer_field_clientnum"] : "";
        $str .= self::get_mapped_fields("customer_field_clientnum", $selected_field, $form_fields, 'true');
        return $str;
    }

    public static function get_country_code($form, $config)
    {
        $form_fields = self::get_client_form_fields($form);
        $str = "";
        $selected_field = $config && !empty($config["meta"]["gf_code_dyn"]) ? $config["meta"]["gf_code_dyn"] : "";
        $str .= self::get_mapped_fields("gf_code_dyn", $selected_field, $form_fields, 'false');
        return $str;
    }

    public static function get_mapped_fields($variable_name, $selected_field, $fields, $empty)
    {
        $field_name = "hannansms_" . $variable_name;
        $str = "<select name=\"$field_name\" id=\"$field_name\">";
        $str .= $empty == 'true' ? "<option value=\"\"></option>" : "";
        foreach ((array)$fields as $field) {
            $field_id = $field[0];
            $field_label = esc_html(GFCommon::truncate_middle($field[1], 40));
            $selected = $field_id == $selected_field ? "selected='selected'" : "";
            $str .= "<option value=\"$field_id\" " . $selected . ">" . $field_label . "</option>";
        }
        $str .= "</select>";
        return $str;
    }

    public static function get_client_form_fields($form)
    {
        $fields = array();
        if (is_array($form["fields"])) {
            foreach ((array)$form["fields"] as $field) {
                if (isset($field["inputs"]) && is_array($field["inputs"])) {
                    foreach ((array)$field["inputs"] as $input) {
                        if (!(GFCommon::is_pricing_field($field["type"]) || ($field["type"] == 'total')))
                            $fields[] = array($input["id"], GFCommon::get_label($field, $input["id"]));
                    }
                } else if (!rgar($field, 'displayOnly')) {
                    if (!(GFCommon::is_pricing_field($field["type"]) || ($field["type"] == 'total')))
                        $fields[] = array($field["id"], GFCommon::get_label($field));
                }
            }
        }
        return $fields;
    }


    public static function get_form_fields_merge($form)
    {
        $str = "<option value=''>" . __("Merge Tags", "gravityforms") . "</option>";
        $required_fields = array();
        $optional_fields = array();
        $pricing_fields = array();
        foreach ((array)$form["fields"] as $field) {
            if ($field["displayOnly"])
                continue;
            $input_type = RGFormsModel::get_input_type($field);
            if ($field["isRequired"]) {
                switch ($input_type) {
                    case "name" :
                        if ($field["nameFormat"] == "extended") {
                            $prefix = GFCommon::get_input($field, $field["id"] + 0.2);
                            $suffix = GFCommon::get_input($field, $field["id"] + 0.8);
                            $optional_field = $field;
                            $optional_field["inputs"] = array($prefix, $suffix);
                            $optional_fields[] = $optional_field;
                            unset($field["inputs"][0]);
                            unset($field["inputs"][3]);
                        }
                        $required_fields[] = $field;
                        break;
                    default:
                        $required_fields[] = $field;
                }
            } else {
                $optional_fields[] = $field;
            }
            if (GFCommon::is_pricing_field($field["type"])) {
                $pricing_fields[] = $field;
            }
        }
        if (!empty($required_fields)) {
            $str .= "<optgroup label='" . __("Required form fields", "gravityforms") . "'>";
            foreach ((array)$required_fields as $field) {
                $str .= self::get_fields_options($field);
            }
            $str .= "</optgroup>";
        }
        if (!empty($optional_fields)) {
            $str .= "<optgroup label='" . __("Optional form fields", "gravityforms") . "'>";
            foreach ((array)$optional_fields as $field) {
                $str .= self::get_fields_options($field);
            }
            $str .= "</optgroup>";
        }
        if (!empty($pricing_fields)) {
            $str .= "<optgroup label='" . __("Pricing form fields", "gravityforms") . "'>";
            foreach ((array)$pricing_fields as $field) {
                $str .= self::get_fields_options($field);
            }
            $str .= "</optgroup>";
        }
        $str .= "<optgroup label='" . __("Other", "gravityforms") . "'>
					<option value='{payment_gateway}'>" . __("Payment Gateway / Method", "GF_SMS") . "</option>
					<option value='{payment_status}'>" . __("Payment Status", "gravityforms") . "</option>
					<option value='{transaction_id}'>" . __("Transaction Id", "gravityforms") . "</option>
					<option value='{ip}'>" . __("IP", "gravityforms") . "</option>
					<option value='{date_mdy}'>" . __("Date", "gravityforms") . " (mm/dd/yyyy)</option>
					<option value='{date_dmy}'>" . __("Date", "gravityforms") . " (dd/mm/yyyy)</option>
					<option value='{embed_post:ID}'>" . __("Embed Post/Page Id", "gravityforms") . "</option>
					<option value='{embed_post:post_title}'>" . __("Embed Post/Page Title", "gravityforms") . "</option>
					<option value='{embed_url}'>" . __("Embed URL", "gravityforms") . "</option>
					<option value='{entry_id}'>" . __("Entry Id", "gravityforms") . "</option>
					<option value='{entry_url}'>" . __("Entry URL", "gravityforms") . "</option>
					<option value='{form_id}'>" . __("Form Id", "gravityforms") . "</option>
					<option value='{form_title}'>" . __("Form Title", "gravityforms") . "</option>
					<option value='{user_agent}'>" . __("HTTP User Agent", "gravityforms") . "</option>";
        if (GFCommon::has_post_field($form["fields"])) {
            $str .= "<option value='{post_id}'>" . __("Post Id", "gravityforms") . "</option>
                    <option value='{post_edit_url}'>" . __("Post Edit URL", "gravityforms") . "</option>";
        }
        $str .= "<option value='{user:display_name}'>" . __("User Display Name", "gravityforms") . "</option>
				<option value='{user:user_email}'>" . __("User Email", "gravityforms") . "</option>
				<option value='{user:user_login}'>" . __("User Login", "gravityforms") . "</option>
			</optgroup>";
        return $str;
    }

    public static function get_fields_options($field, $max_label_size = 100)
    {
        $str = "";
        if (is_array($field["inputs"])) {
            foreach ((array)$field["inputs"] as $input) {
                $str .= "<option value='{" . esc_attr(GFCommon::get_label($field, $input["id"])) . ":" . $input["id"] . "}'>" . esc_html(GFCommon::truncate_middle(GFCommon::get_label($field, $input["id"]), $max_label_size)) . "</option>";
            }
        } else {
            $str .= "<option value='{" . esc_html(GFCommon::get_label($field)) . ":" . $field["id"] . "}'>" . esc_html(GFCommon::truncate_middle(GFCommon::get_label($field), $max_label_size)) . "</option>";
        }
        return $str;
    }

    public static function select_forms_ajax()
    {
        check_ajax_referer("gf_select_hannansms_form", "gf_select_hannansms_form");
        $form_id = intval(rgpost("form_id"));
        $form = RGFormsModel::get_form_meta($form_id);
        $fields = self::get_form_fields_merge($form);
        $customer_field = self::get_client_information($form, '');
        $gf_code = self::get_country_code($form, '');
        $result = array("form" => $form, "fields" => $fields, "customer_field" => str_replace("'", "\'", $customer_field), "gf_code" => str_replace("'", "\'", $gf_code));
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        wp_die(GFCommon::json_encode($result));
        exit;
    }

}