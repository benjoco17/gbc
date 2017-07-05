<?php
/**
*
* fieldconfig for gravity-forms-styler/Fields Labels
*
* @package Gravity_Forms_Styler
* @author WordpressGurus support@wordpressgurus.net
* @license GPL-2.0+
* @link http://www.wordpressgurus.net
* @copyright 2014 WordpressGurus
*/


$group = array(
	'label' => __('Fields Labels','gravity-forms-styler'),
	'id' => '1431071',
	'master' => 'fl_label_color',
	'fields' => array(
		'fl_label_color'	=>	array(
			'label'		=> 	__('Label Font Color','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'colorpicker',
			'default'	=> 	'',
		),
		'label_font_size'	=>	array(
			'label'		=> 	__('Label Font Size','gravity-forms-styler'),
			'caption'	=>	__('px','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'label_font_family'	=>	array(
			'label'		=> 	__('Label Font Family','gravity-forms-styler'),
          'caption'   =>  '',
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

