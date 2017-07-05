<?php
/**
*
* fieldconfig for gravity-forms-styler/Text Field
*
* @package Gravity_Forms_Styler
* @author WordpressGurus support@wordpressgurus.net
* @license GPL-2.0+
* @link http://www.wordpressgurus.net
* @copyright 2014 WordpressGurus
*/


$group = array(
	'label' => __('Text Field','gravity-forms-styler'),
	'id' => '141111302',
	'master' => 'tf_background_color',
	'fields' => array(
		'tf_background_color'	=>	array(
			'label'		=> 	__('Background Color','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'colorpicker',
			'default'	=> 	'',
		),
		'tf_border_type'	=>	array(
			'label'		=> 	__('Border Type','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'dropdown',
			'default'	=> 	'dashed||Dashed,*solid||Solid',
		),
		'tf_border_size'	=>	array(
			'label'		=> 	__('Border Size','gravity-forms-styler'),
			'caption'	=>	__('px','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'tf_border_color'	=>	array(
			'label'		=> 	__('Border Color','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'colorpicker',
			'default'	=> 	'',
		),
		'tf_border_radius'	=>	array(
			'label'		=> 	__('Border Radius','gravity-forms-styler'),
			'caption'	=>	__('px','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'tf_font_color'	=>	array(
			'label'		=> 	__('Font Color','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'colorpicker',
			'default'	=> 	'',
		),
		'tf_font_size'	=>	array(
			'label'		=> 	__('Font Size','gravity-forms-styler'),
			'caption'	=>	__('px','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'tf_font_family'	=>	array(
			'label'		=> 	__('Font Family','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'tf_maximum_width'	=>	array(
			'label'		=> 	__('Maximum Width','gravity-forms-styler'),
			'caption'	=>	__('%','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
				'tf_margins'	=>	array(
				'label'		=> 	__('Margins','gravity-forms-styler'),
				'type'		=>	'smalltextfield',
				'default'	=> 	'10px 10px 10px 10px',
		),
				'tf_padding'	=>	array(
				'label'		=> 	__('Padding','gravity-forms-styler'),
				'type'		=>	'smalltextfield',
				'default'	=> 	'10px 10px 10px 10px',
		),
		'tf_height'	=>	array(
			'label'		=> 	__('Field Height','gravity-forms-styler'),
			'caption'	=>	__('px','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
	),
	'styles'	=> array(
		'minicolors.css',
	),
	'scripts'	=> array(
		'minicolors.js',
	),
	'multiple'	=> false,
);

