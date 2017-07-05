<?php
/**
*
* fieldconfig for gravity-forms-styler/Custom CSS
*
* @package Gravity_Forms_Styler
* @author WordpressGurus support@wordpressgurus.net
* @license GPL-2.0+
* @link http://www.wordpressgurus.net
* @copyright 2014 WordpressGurus
*/


$group = array(
	'label' => __('Custom CSS','gravity-forms-styler'),
	'id' => '10100911',
	'master' => 'custom_css_code',
	'fields' => array(
		'custom_css_code'	=>	array(
			'label'		=> 	__('Custom CSS Code','gravity-forms-styler'),
			'caption'	=>	__('You can include any custom css from Gravity Forms Main CSS or Include your own if you have changed the classes in fuctions.php','gravity-forms-styler'),
			'type'		=>	'codeeditor',
			'default'	=> 	'',
		),
	),
	'styles'	=> array(
		'editor.css',
	),
	'scripts'	=> array(
		'codemirror-compressed.js',
	),
	'multiple'	=> false,
);

