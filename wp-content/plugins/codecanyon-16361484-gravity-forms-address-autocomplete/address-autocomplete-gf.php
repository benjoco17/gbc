<?php
/*
* Plugin Name: Gravity Forms Address Autocomplete
* Plugin URI: 
* Description: This plugin adds autocomplete feature to gravity forms address field.
* Version: 2.0
* Author: Bhagwant Banger
* Author URI:https://codecanyon.net/user/bhagwantbanger/portfolio 
* License: Commercial 
*/
if(!defined('ABSPATH')) {
	exit;
}

class bb_gf_address_autocomplete{
	public $autofields;	
	public $RC;
		
	function __construct(){
		$this->autofields	=	array();
		$this->RC	=	array();
		
		add_filter( 'gform_addon_navigation', array($this,'bb_gf_aa_menu_item') );
		
		add_action( 'gform_field_standard_settings', array($this,'bb_gf_advanced_settings'), 10, 2 );
		add_action( 'gform_editor_js', array($this,'editor_script') );
		add_filter( 'gform_tooltips', array($this,'add_autocomplete_tooltips') );
		add_action( 'gform_enqueue_scripts', array($this,'bb_gf_scripts'), 10, 2 );
		
		add_action( 'wp_footer', array($this,'bb_gf_footer') );
	}
	
	function bb_gf_aa_menu_item($menu_items){
		$menu_items[] = array( 
			"name" => "bb_gf_aa_settings", 
			"label" => "Address Autocomplete Settings", 
			"callback" => array($this,"bb_gf_aa_set_fields"), 
			"permission" => "manage_options" );
	    return $menu_items;	
	}
	
	function bb_gf_aa_set_fields(){
		?>
		<div class="wrap">
			<h3>Gravity Forms Address Autocomplete Settings</h3>
			<hr />
			<?php
			if(!empty($_POST)){
				foreach($_POST as $k=>$v){
					update_option( $k , $v );
				}
			}
			$bb_gf_aa_google_api_key	=	get_option('bb_gf_aa_google_api_key');
			echo $bb_gf_aa_google_api_key;
			?>
			<div id="tab_settings" >
				<form method="post">
				<table class="form-table">
					<tbody>		
						<tr valign="top">
							<th><label>Google Places API Key</label></th>
							<td><input type="text" name="bb_gf_aa_google_api_key" value="<?php echo empty($bb_gf_aa_google_api_key)?'':$bb_gf_aa_google_api_key; ?>"></td>
						</tr>
						<tr valign="top">
							<th>&nbsp;</th>
							<td><input class="button button-large button-primary" type="submit" name="submit" value="Save"></td>
						</tr>
					</tbody>
				</table>
				</form>
			</div>
		</div>
		<?php		
	}
	function bb_gf_advanced_settings( $position, $form_id ) {    
	      if ( $position == 25 ) { ?>
	        <li class="autocomplete_setting field_setting">
	            <label for="field_admin_label">
	                <?php _e("Enable Autocomplete/Suggest with Google Places API", "gravityforms"); ?>
	                <?php gform_tooltip("form_field_autocomplete_value") ?>
	            </label>
	            <input type="checkbox" id="field_autocomplete_value" onclick="SetFieldProperty('autocompleteField', this.checked);" /> Enable
	        </li>
	        <li class="autocomplete_RC_setting field_setting">
	        <label for="field_admin_label"> <?php _e("Restrict Autocomplete/Suggest to", "gravityforms"); ?>
				<?php gform_tooltip("form_field_autocomplete_RC_value") ?> </label>
				<?php
				$countries = array('AF' => 'Afghanistan','AX' => 'Aland Islands','AL' => 'Albania','DZ' => 'Algeria','AS' => 'American Samoa',	'AD' => 'Andorra',
							'AO' => 'Angola','AI' => 'Anguilla','AQ' => 'Antarctica',	'AG' => 'Antigua And Barbuda',	'AR' => 'Argentina','AM' => 'Armenia',
							'AW' => 'Aruba','AU' => 'Australia',	'AT' => 'Austria',			'AZ' => 'Azerbaijan',
							'BS' => 'Bahamas',			'BH' => 'Bahrain',				'BD' => 'Bangladesh',				'BB' => 'Barbados',
							'BY' => 'Belarus','BE' => 'Belgium','BZ' => 'Belize','BJ' => 'Benin','BM' => 'Bermuda','BT' => 'Bhutan','BO' => 'Bolivia','BA' => 'Bosnia And Herzegovina','BW' => 'Botswana',
							'BV' => 'Bouvet Island','BR' => 'Brazil','IO' => 'British Indian Ocean Territory','BN' => 'Brunei Darussalam','BG' => 'Bulgaria','BF' => 'Burkina Faso','BI' => 'Burundi',
							'KH' => 'Cambodia','CM' => 'Cameroon','CA' => 'Canada','CV' => 'Cape Verde','KY' => 'Cayman Islands','CF' => 'Central African Republic','TD' => 'Chad','CL' => 'Chile',
							'CN' => 'China','CX' => 'Christmas Island','CC' => 'Cocos (Keeling) Islands','CO' => 'Colombia','KM' => 'Comoros','CG' => 'Congo','CD' => 'Congo, Democratic Republic',
							'CK' => 'Cook Islands','CR' => 'Costa Rica','CI' => 'Cote D\'Ivoire','HR' => 'Croatia','CU' => 'Cuba','CY' => 'Cyprus','CZ' => 'Czech Republic','DK' => 'Denmark',
							'DJ' => 'Djibouti','DM' => 'Dominica','DO' => 'Dominican Republic','EC' => 'Ecuador','EG' => 'Egypt','SV' => 'El Salvador','GQ' => 'Equatorial Guinea','ER' => 'Eritrea',
							'EE' => 'Estonia','ET' => 'Ethiopia','FK' => 'Falkland Islands (Malvinas)','FO' => 'Faroe Islands','FJ' => 'Fiji','FI' => 'Finland','FR' => 'France','GF' => 'French Guiana','PF' => 'French Polynesia',
							'TF' => 'French Southern Territories','GA' => 'Gabon','GM' => 'Gambia','GE' => 'Georgia','DE' => 'Germany','GH' => 'Ghana','GI' => 'Gibraltar','GR' => 'Greece','GL' => 'Greenland','GD' => 'Grenada',
							'GP' => 'Guadeloupe','GU' => 'Guam','GT' => 'Guatemala','GG' => 'Guernsey','GN' => 'Guinea','GW' => 'Guinea-Bissau','GY' => 'Guyana','HT' => 'Haiti','HM' => 'Heard Island & Mcdonald Islands',
							'VA' => 'Holy See (Vatican City State)','HN' => 'Honduras','HK' => 'Hong Kong','HU' => 'Hungary','IS' => 'Iceland','IN' => 'India','ID' => 'Indonesia','IR' => 'Iran, Islamic Republic Of','IQ' => 'Iraq',
							'IE' => 'Ireland','IM' => 'Isle Of Man','IL' => 'Israel','IT' => 'Italy','JM' => 'Jamaica','JP' => 'Japan','JE' => 'Jersey','JO' => 'Jordan','KZ' => 'Kazakhstan','KE' => 'Kenya','KI' => 'Kiribati','KR' => 'Korea',
							'KW' => 'Kuwait','KG' => 'Kyrgyzstan','LA' => 'Lao People\'s Democratic Republic','LV' => 'Latvia','LB' => 'Lebanon','LS' => 'Lesotho','LR' => 'Liberia','LY' => 'Libyan Arab Jamahiriya','LI' => 'Liechtenstein',
							'LT' => 'Lithuania','LU' => 'Luxembourg','MO' => 'Macao','MK' => 'Macedonia','MG' => 'Madagascar','MW' => 'Malawi','MY' => 'Malaysia','MV' => 'Maldives','ML' => 'Mali','MT' => 'Malta','MH' => 'Marshall Islands',
							'MQ' => 'Martinique','MR' => 'Mauritania','MU' => 'Mauritius','YT' => 'Mayotte','MX' => 'Mexico','FM' => 'Micronesia, Federated States Of','MD' => 'Moldova','MC' => 'Monaco','MN' => 'Mongolia','ME' => 'Montenegro',
							'MS' => 'Montserrat','MA' => 'Morocco','MZ' => 'Mozambique','MM' => 'Myanmar','NA' => 'Namibia','NR' => 'Nauru','NP' => 'Nepal','NL' => 'Netherlands','AN' => 'Netherlands Antilles',
							'NC' => 'New Caledonia','NZ' => 'New Zealand','NI' => 'Nicaragua','NE' => 'Niger','NG' => 'Nigeria','NU' => 'Niue','NF' => 'Norfolk Island','MP' => 'Northern Mariana Islands','NO' => 'Norway',
							'OM' => 'Oman','PK' => 'Pakistan','PW' => 'Palau','PS' => 'Palestinian Territory, Occupied','PA' => 'Panama','PG' => 'Papua New Guinea','PY' => 'Paraguay','PE' => 'Peru',
							'PH' => 'Philippines','PN' => 'Pitcairn','PL' => 'Poland','PT' => 'Portugal','PR' => 'Puerto Rico','QA' => 'Qatar','RE' => 'Reunion','RO' => 'Romania','RU' => 'Russian Federation',
							'RW' => 'Rwanda','BL' => 'Saint Barthelemy','SH' => 'Saint Helena','KN' => 'Saint Kitts And Nevis','LC' => 'Saint Lucia','MF' => 'Saint Martin','PM' => 'Saint Pierre And Miquelon',
							'VC' => 'Saint Vincent And Grenadines','WS' => 'Samoa','SM' => 'San Marino','ST' => 'Sao Tome And Principe','SA' => 'Saudi Arabia','SN' => 'Senegal',
							'RS' => 'Serbia','SC' => 'Seychelles','SL' => 'Sierra Leone','SG' => 'Singapore','SK' => 'Slovakia','SI' => 'Slovenia','SB' => 'Solomon Islands','SO' => 'Somalia',
							'ZA' => 'South Africa','GS' => 'South Georgia And Sandwich Isl.','ES' => 'Spain','LK' => 'Sri Lanka','SD' => 'Sudan','SR' => 'Suriname','SJ' => 'Svalbard And Jan Mayen',
							'SZ' => 'Swaziland','SE' => 'Sweden','CH' => 'Switzerland','SY' => 'Syrian Arab Republic','TW' => 'Taiwan','TJ' => 'Tajikistan','TZ' => 'Tanzania','TH' => 'Thailand',
							'TL' => 'Timor-Leste','TG' => 'Togo','TK' => 'Tokelau','TO' => 'Tonga','TT' => 'Trinidad And Tobago','TN' => 'Tunisia','TR' => 'Turkey','TM' => 'Turkmenistan','TC' => 'Turks And Caicos Islands',
							'TV' => 'Tuvalu','UG' => 'Uganda','UA' => 'Ukraine','AE' => 'United Arab Emirates','GB' => 'United Kingdom','US' => 'United States','UM' => 'United States Outlying Islands',
							'UY' => 'Uruguay','UZ' => 'Uzbekistan','VU' => 'Vanuatu','VE' => 'Venezuela','VN' => 'Viet Nam','VG' => 'Virgin Islands, British','VI' => 'Virgin Islands, U.S.',
							'WF' => 'Wallis And Futuna','EH' => 'Western Sahara','YE' => 'Yemen','ZM' => 'Zambia','ZW' => 'Zimbabwe');
				?> 
				<select id="field_autocomplete_RC_value" onChange="SetFieldProperty('autocomplete_RCField', this.value);">
					<option value="0">--select a country--</option>
					<?php
					foreach($countries as $code=>$country){
					?>
					<option value="<?php echo $code; ?>"><?php echo $country; ?></option>
					<?php
					}
					?>
				</select>
			</li>
	        
	        
	        <?php
	      } 
	}

	//Action to inject supporting script to the form editor page
	function editor_script(){
	    ?>
	    <script type='text/javascript'>
	        //adding setting to fields of type "text"
	        fieldSettings["address"] += ", .autocomplete_setting";
			fieldSettings["address"] += ", .autocomplete_RC_setting";
			
	        //binding to the load field settings event to initialize the checkbox
	        jQuery(document).bind("gform_load_field_settings", function(event, field, form){
	            jQuery("#field_autocomplete_value").attr("checked", field["autocompleteField"] == true);
	            jQuery("#field_autocomplete_RC_value").val( field["autocomplete_RCField"] );
	        });
	    </script>
	    <?php
	}
	
	//Filter to add a new tooltip
	function add_autocomplete_tooltips( $tooltips ) {
	   $tooltips['form_field_autocomplete_value'] = "<h6>Enable Autocomplete</h6>Check this box to enable Google Places Suggest on address";
	   return $tooltips;
	}

	function bb_gf_scripts($form){
		//$this->autofields	=	array();
		foreach($form['fields'] as &$field){
			if($field['type'] == 'address'){	
				if($field['autocompleteField']==1){
					$field_id	=	$form['id'].'_'.$field['id'];
					$this->autofields[]	=	$field_id;
					$this->RC['rc_'.$field_id]	=	empty($field['autocomplete_RCField'])?'default':$field['autocomplete_RCField'];
				}
			}
			else{
				continue;
			}
		}
		return $form;
	}
	function bb_gf_footer(){
				
		$bb_gf_aa_google_api_key	=	get_option('bb_gf_aa_google_api_key');
		if(!empty($bb_gf_aa_google_api_key)){
			wp_enqueue_script('BB google places',"https://maps.googleapis.com/maps/api/js?v=3.exp&key=".$bb_gf_aa_google_api_key."&libraries=places");
		}
		else{
			wp_enqueue_script('BB google places',"https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places");
		}
		?>
		<script type="text/javascript">
		jQuery(document).bind('gform_post_render', function(evt,formid){
			var autoCompleteControllers	=	<?php echo json_encode(array_unique($this->autofields)); ?>;
			var RCs	=	<?php echo json_encode($this->RC); ?>;
			
			var autocomplete = {};
			var autocompletesWraps	=	new Array();
			jQuery.each(autoCompleteControllers,function(i,field){
				var control	=	'input_'+field+'_1';
				autocompletesWraps.push(control);
				return autocompletesWraps; 				
			});

			jQuery.each(autocompletesWraps, function(index, name) {

				var field_id	=	name.split('_')[1]+'_'+name.split('_')[2];
				var rc_field	=	'rc_'+field_id;
				var restrict_country	=	RCs[rc_field];
				

				if( restrict_country!=='default' ){
					autocomplete[name] = new google.maps.places.Autocomplete(document.getElementById(name) , 
					{ 
						types: ['geocode'] , 
						componentRestrictions: {country: restrict_country}
					});					
				}
				else{
					autocomplete[name] = new google.maps.places.Autocomplete(document.getElementById(name) , { types: ['geocode'] });
				}
				google.maps.event.addListener(autocomplete[name], 'place_changed', function() {
					var place = autocomplete[name].getPlace();
					var form_id	=	name.split('_')[1];
					var field_id	=	name.split('_')[2];
					var addressLine1	=	'';
					var addressLine2	=	'';
					var addressLine3	=	'';
					var city	=	'';
					var state	=	'';
					var country	=	'';
					var postal_code	='';
					

					for (var i = 0; i < place.address_components.length; i++) {
 						var addressType = place.address_components[i].types[0];
 						var val	=	place.address_components[i].long_name
                    
						switch(addressType){
							case 'subpremise':
								addressLine1	+=	val+'/';		
							break;
							
							case 'street_number':
							case 'route':
								addressLine1	+=	val+' ';		
							break;	

                            case 'sublocality_level_1':    
							case 'sublocality_level_2':	
								addressLine2	=	val;
							break;

							case 'locality':	
							//case 'administrative_area_level_2':							
								city	+=	val+' ';
							break;

							case 'administrative_area_level_1':
								state	=	val;
							break;	

							case 'country':
								country	=	val;
							break;	

							case 'postal_code':
								postal_code	=	val;
							break;	

							default:

						}
 	 				}
					jQuery('#input_'+form_id+'_'+field_id+'_1').val(addressLine1);
					jQuery('#input_'+form_id+'_'+field_id+'_2').val(addressLine2);
					jQuery('#input_'+form_id+'_'+field_id+'_3').val(city);
					jQuery('#input_'+form_id+'_'+field_id+'_4').val(state);
					jQuery('#input_'+form_id+'_'+field_id+'_5').val(postal_code);
					jQuery('#input_'+form_id+'_'+field_id+'_6').val(country);
				});
			});	
		});
		</script>
		<?php 
	}
}
$address_autocomplete_gf	=	 new bb_gf_address_autocomplete();
?>