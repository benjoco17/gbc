<?php
/**
*
* fieldconfig for gravity-forms-styler/Form Header
*
* @package Gravity_Forms_Styler
* @author WordpressGurus support@wordpressgurus.net
* @license GPL-2.0+
* @link http://www.wordpressgurus.net
* @copyright 2014 WordpressGurus
*/


$group = array(
	'label' => __('Form Header','gravity-forms-styler'),
	'id' => '141031113',
	'master' => 'fh_background_color',
	'fields' => array(
		'fh_background_color'	=>	array(
			'label'		=> 	__('Background Color','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'colorpicker',
			'default'	=> 	'',
		),
		'fh_border_type'	=>	array(
			'label'		=> 	__('Border Type','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'dropdown',
			'default'	=> 	'*solid||Solid,dashed||Dashed',
		),
		'fh_border_size'	=>	array(
			'label'		=> 	__('Border Size','gravity-forms-styler'),
			'caption'	=>	__('px','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'fh_border_color'	=>	array(
			'label'		=> 	__('Border Color','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'colorpicker',
			'default'	=> 	'',
		),
				'fh_margins'	=>	array(
				'label'		=> 	__('Margins','gravity-forms-styler'),
				'type'		=>	'smalltextfield',
				'default'	=> 	'10px 10px 10px 10px',
		),
				'fh_padding'	=>	array(
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
	),
	'multiple'	=> false,
);

