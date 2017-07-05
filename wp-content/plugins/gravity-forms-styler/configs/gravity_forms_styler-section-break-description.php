<?php
/**
*
* fieldconfig for gravity-forms-styler/ Section Break Description
*
* @package Gravity_Forms_Styler
* @author WordpressGurus support@wordpressgurus.net
* @license GPL-2.0+
* @link http://www.wordpressgurus.net
* @copyright 2014 WordpressGurus
*/


$group = array(
	'label' => __(' Section Break Description','gravity-forms-styler'),
	'id' => '0411111511',
	'master' => 'sbd_color',
	'fields' => array(
		'sbd_color'	=>	array(
			'label'		=> 	__('Description Color','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'colorpicker',
			'default'	=> 	'',
		),
		'sbd_font_size'	=>	array(
			'label'		=> 	__('Font Size','gravity-forms-styler'),
			'caption'	=>	__('px','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'sbd_font_family'	=>	array(
			'label'		=> 	__('Font Family','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'sbd_description_position'	=>	array(
			'label'		=> 	__('Description Position','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'dropdown',
			'default'	=> 	'left||Left,center||Center,right||Right',
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

