<?php
/**
*
* fieldconfig for gravity-forms-styler/ Drop-Down (Select) Field 
*
* @package Gravity_Forms_Styler
* @author WordpressGurus support@wordpressgurus.net
* @license GPL-2.0+
* @link http://www.wordpressgurus.net
* @copyright 2014 WordpressGurus
*/


$group = array(
	'label' => __(' Drop-Down (Select) Field ','gravity-forms-styler'),
	'id' => '0510004',
	'master' => 'dd_maximum_width',
	'fields' => array(
		'dd_maximum_width'	=>	array(
			'label'		=> 	__('Maximum Width','gravity-forms-styler'),
			'caption'	=>	__('%','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'dd_font_size'	=>	array(
			'label'		=> 	__('Font Size','gravity-forms-styler'),
			'caption'	=>	__('px','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'dd_font_color'	=>	array(
			'label'		=> 	__('Font Color','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'colorpicker',
			'default'	=> 	'',
		),
		'dd_font_family'	=>	array(
			'label'		=> 	__('Font Family','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'dd_border_size'	=>	array(
			'label'		=> 	__('Border Size','gravity-forms-styler'),
			'caption'	=>	__('px','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'dd_border_type'	=>	array(
			'label'		=> 	__('Border Type','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'dropdown',
			'default'	=> 	'*solid||Solid,thick||Thick,dashed||Dashed',
		),
		'dd_border_color'	=>	array(
			'label'		=> 	__('Border Color','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'colorpicker',
			'default'	=> 	'',
		),
		'dp_border_radius'	=>	array(
			'label'		=> 	__('Border Radius','gravity-forms-styler'),
			'caption'	=>	__('px','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'dd_background_color'	=>	array(
			'label'		=> 	__('Background Color','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'colorpicker',
			'default'	=> 	'',
		),
			'dd_margins'	=>	array(
			'label'		=> 	__('Margins','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'10px 10px 10px 10px',
	),
			'dd_padding'	=>	array(
			'label'		=> 	__('Padding','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'10px 10px 10px 10px',
	),
	'dd_height'	=>	array(
		'label'		=> 	__('Field Height','gravity-forms-styler'),
		'caption'	=>	__('px','gravity-forms-styler'),
		'type'		=>	'smalltextfield',
		'default'	=> 	'',
	),
	'dp_custom_css'	=>	array(
		'label'		=> 	__('Custom CSS','gravity-forms-styler'),
	  'caption'   =>  '',
		'type'		=>	'codeeditor',
		'default'	=> 	'',
	),
	
	),
	'styles'	=> array(
		'minicolors.css',
		'editor.css',
	),
	'scripts'	=> array(
		'minicolors.js',
		'codemirror-compressed.js',
	),
	'multiple'	=> false,
);

