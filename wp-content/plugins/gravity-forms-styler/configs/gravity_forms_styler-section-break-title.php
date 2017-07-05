<?php
/**
*
* fieldconfig for gravity-forms-styler/ Section Break Title
*
* @package Gravity_Forms_Styler
* @author WordpressGurus support@wordpressgurus.net
* @license GPL-2.0+
* @link http://www.wordpressgurus.net
* @copyright 2014 WordpressGurus
*/


$group = array(
	'label' => __(' Section Break Title','gravity-forms-styler'),
	'id' => '116291215',
	'master' => 'sbt_color',
	'fields' => array(
		'sbt_color'	=>	array(
			'label'		=> 	__('Title Color','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'colorpicker',
			'default'	=> 	'',
		),
		'sbt_title_font_family'	=>	array(
			'label'		=> 	__('Title Font Family','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'sbt_title_font_size'	=>	array(
			'label'		=> 	__('Title Font Size','gravity-forms-styler'),
			'caption'	=>	__('px','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'sbt_title_position'	=>	array(
			'label'		=> 	__('Title Position','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'dropdown',
			'default'	=> 	'*left||Left,center||Center,right||Right',
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

