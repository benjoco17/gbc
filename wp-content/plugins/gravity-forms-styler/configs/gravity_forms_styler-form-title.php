<?php
/**
*
* fieldconfig for gravity-forms-styler/Form Title
*
* @package Gravity_Forms_Styler
* @author WordpressGurus support@wordpressgurus.net
* @license GPL-2.0+
* @link http://www.wordpressgurus.net
* @copyright 2014 WordpressGurus
*/


$group = array(
	'label' => __('Form Title','gravity-forms-styler'),
	'id' => '88751415',
	'master' => 'ft_title_position',
	'fields' => array(
		'ft_title_position'	=>	array(
			'label'		=> 	__('Title Position','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'dropdown',
			'default'	=> 	'center||Center,*left||Left,right||Right',
		),
		'ft_title_color'	=>	array(
			'label'		=> 	__('Title Color','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'colorpicker',
			'default'	=> 	'',
		),
		'ft_font_family'	=>	array(
			'label'		=> 	__('Font Family','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'ft_font_size'	=>	array(
			'label'		=> 	__('Font Size','gravity-forms-styler'),
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

