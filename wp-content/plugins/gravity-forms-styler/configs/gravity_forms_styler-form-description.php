<?php
/**
*
* fieldconfig for gravity-forms-styler/Form Description
*
* @package Gravity_Forms_Styler
* @author WordpressGurus support@wordpressgurus.net
* @license GPL-2.0+
* @link http://www.wordpressgurus.net
* @copyright 2014 WordpressGurus
*/


$group = array(
	'label' => __('Form Description','gravity-forms-styler'),
	'id' => '91091510',
	'master' => 'fd_color',
	'fields' => array(
		'fd_color'	=>	array(
			'label'		=> 	__('Description Color','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'colorpicker',
			'default'	=> 	'',
		),
		'fd_font_family'	=>	array(
			'label'		=> 	__('Font Family','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'fd_font_size'	=>	array(
			'label'		=> 	__('Font Size','gravity-forms-styler'),
			'caption'	=>	__('px','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'fd_text_alignment'	=>	array(
			'label'		=> 	__('Text Alignment','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'dropdown',
			'default'	=> 	'*left||Left,right||Right,center||Center',
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

