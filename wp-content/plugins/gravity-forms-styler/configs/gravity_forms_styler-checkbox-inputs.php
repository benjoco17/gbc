<?php
/**
*
* fieldconfig for gravity-forms-styler/ Checkbox Inputs
*
* @package Gravity_Forms_Styler
* @author WordpressGurus support@wordpressgurus.net
* @license GPL-2.0+
* @link http://www.wordpressgurus.net
* @copyright 2014 WordpressGurus
*/


$group = array(
	'label' => __(' Checkbox Inputs','gravity-forms-styler'),
	'id' => '153966',
	'master' => 'cb_maxium_width',
	'fields' => array(
		'cb_maxium_width'	=>	array(
			'label'		=> 	__('Maximum Width','gravity-forms-styler'),
			'caption'	=>	__('%','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'cbi_custom_css'	=>	array(
			'label'		=> 	__('Custom CSS','gravity-forms-styler'),
          'caption'   =>  '',
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

