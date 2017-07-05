<?php
/**
*
* fieldconfig for gravity-forms-styler/ Paragraph (Textarea) Field
*
* @package Gravity_Forms_Styler
* @author WordpressGurus support@wordpressgurus.net
* @license GPL-2.0+
* @link http://www.wordpressgurus.net
* @copyright 2014 WordpressGurus
*/


$group = array(
	'label' => __(' Paragraph (Textarea) Field','gravity-forms-styler'),
	'id' => '141115545',
	'master' => 'pg_max_width',
	'fields' => array(
		'pg_max_width'	=>	array(
			'label'		=> 	__('Maximum Width','gravity-forms-styler'),
			'caption'	=>	__('%','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
				'pg_margins'	=>	array(
				'label'		=> 	__('Margins','gravity-forms-styler'),
				'type'		=>	'smalltextfield',
				'default'	=> 	'10px 10px 10px 10px',
		),
				'pg_padding'	=>	array(
				'label'		=> 	__('Padding','gravity-forms-styler'),
				'type'		=>	'smalltextfield',
				'default'	=> 	'10px 10px 10px 10px',
		),
		'pg_height'	=>	array(
			'label'		=> 	__('Field Height','gravity-forms-styler'),
			'caption'	=>	__('px','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'pg_custom_css'	=>	array(
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

