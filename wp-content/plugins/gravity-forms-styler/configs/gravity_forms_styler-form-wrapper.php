<?php
/**
*
* fieldconfig for gravity-forms-styler/Form Wrapper
*
* @package Gravity_Forms_Styler
* @author WordpressGurus support@wordpressgurus.net
* @license GPL-2.0+
* @link http://www.wordpressgurus.net
* @copyright 2014 WordpressGurus
*/


$group = array(
	'label' => __('Form Wrapper','gravity-forms-styler'),
	'id' => '1215713511',
	'master' => 'fw_maximum_width',
	'fields' => array(
		'fw_maximum_width'	=>	array(
			'label'		=> 	__('Maximum Width','gravity-forms-styler'),
			'caption'	=>	__('%','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'fw_border_size'	=>	array(
			'label'		=> 	__('Border Size','gravity-forms-styler'),
			'caption'	=>	__('px','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'fw_border_type'	=>	array(
			'label'		=> 	__('Border Type','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'dropdown',
			'default'	=> 	'*solid||Solid,dashed||Dashed',
		),
		'fw_border_color'	=>	array(
			'label'		=> 	__('Border Color','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'colorpicker',
			'default'	=> 	'',
		),
		'fw_border_radius'	=>	array(
			'label'		=> 	__('Border Radius','gravity-forms-styler'),
			'caption'	=>	__('px','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'fw_background_color'	=>	array(
			'label'		=> 	__('Background Color','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'colorpicker',
			'default'	=> 	'',
		),
		'fw_background_image'	=>	array(
			'label'		=> 	__('Background Image','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'image',
			'default'	=> 	'',
		),
		'fw_margins'	=>	array(
		'label'		=> 	__('Margins','gravity-forms-styler'),
		'type'		=>	'smalltextfield',
		'default'	=> 	'10px 10px 10px 10px',
),
		'fw_padding'	=>	array(
		'label'		=> 	__('Padding','gravity-forms-styler'),
		'type'		=>	'smalltextfield',
		'default'	=> 	'10px 10px 10px 10px',
),
	),
	'styles'	=> array(
		'minicolors.css',
	),
	'scripts'	=> array(
		'minicolors.js',
		'image-modal.js',
	),
	'multiple'	=> false,
);

