<?php
/**
*
* fieldconfig for gravity-forms-styler/ Multiple-Choice (Radio) Inputs
*
* @package Gravity_Forms_Styler
* @author WordpressGurus support@wordpressgurus.net
* @license GPL-2.0+
* @link http://www.wordpressgurus.net
* @copyright 2014 WordpressGurus
*/


$group = array(
	'label' => __(' Multiple-Choice (Radio) Inputs','gravity-forms-styler'),
	'id' => '412713100',
	'master' => 'mc_maximum_width',
	'fields' => array(
		'mc_maximum_width'	=>	array(
			'label'		=> 	__('Maximum Width','gravity-forms-styler'),
			'caption'	=>	__('%','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'mpc_custom_css'	=>	array(
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

