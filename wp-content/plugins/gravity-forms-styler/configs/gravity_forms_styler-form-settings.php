<?php
/**
*
* fieldconfig for gravity-forms-styler/Form Settings
*
* @package Gravity_Forms_Styler
* @author WordpressGurus support@wordpressgurus.net
* @license GPL-2.0+
* @link http://www.wordpressgurus.net
* @copyright 2014 WordpressGurus
*/


$group = array(
	'label' => __('Form Settings','gravity-forms-styler'),
	'id' => '6101571215',
	'master' => 'form_id',
	'fields' => array(
		'form_id'	=>	array(
			'label'		=> 	__('Gravity Form ID','gravity-forms-styler'),
			'caption'	=>	__('Please enter gravity form ID you want to style.','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'form_title'	=>	array(
			'label'		=> 	__('Gravity Form Title','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'onoff',
			'default'	=> 	'*true||Yes,false||No',
			'inline'	=> 	true,
		),
		'form_description'	=>	array(
			'label'		=> 	__('Gravity Form Description','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'onoff',
			'default'	=> 	'*true||Yes,false||No',
			'inline'	=> 	true,
		),
		'ajax'	=>	array(
			'label'		=> 	__('Use Ajax','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'onoff',
			'default'	=> 	'*true||Yes,false||No',
			'inline'	=> 	true,
		),
		'use_icons'	=>	array(
			'label'		=> 	__('Use Icons','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'onoff',
			'default'	=> 	'*yes||Yes,no||No',
			'inline'	=> 	true,
		),
		'use_shadows'	=>	array(
			'label'		=> 	__('Use Shadows','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'onoff',
			'default'	=> 	'*yes||Yes,no||No',
			'inline'	=> 	true,
		),
		'use_google_fonts'	=>	array(
			'label'		=> 	__('Use Google Fonts','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'onoff',
			'default'	=> 	'*yes||Yes,no||No',
			'inline'	=> 	true,
		),
		'google_font_name'	=>	array(
			'label'		=> 	__('Google Font Name','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'use_tab_index'	=>	array(
			'label'		=> 	__('Use Tab Index','gravity-forms-styler'),
		  'caption'   =>  '',
			'type'		=>	'onoff',
			'default'	=> 	'*yes||Yes,no||No',
			'inline'	=> 	true,
		),
		'tab_index_no'	=>	array(
			'label'		=> 	__('Tab Index Number','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
	),
	'styles'	=> array(
		'toggles.css',
	),
	'scripts'	=> array(
		'toggles.min.js',
	),
	'multiple'	=> false,
);

