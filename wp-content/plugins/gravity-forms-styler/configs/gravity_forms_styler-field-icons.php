<?php
/**
*
* fieldconfig for gravity-forms-styler/Field Icons
*
* @package Gravity_Forms_Styler
* @author WordpressGurus support@wordpressgurus.net
* @license GPL-2.0+
* @link http://www.wordpressgurus.net
* @copyright 2014 WordpressGurus
*/


$group = array(
	'label' => __('Field Icons','gravity-forms-styler'),
	'id' => '2891213',
	'master' => 'time_icon',
	'fields' => array(
		'time_icon'	=>	array(
			'label'		=> 	__('Time Icon','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'image',
			'default'	=> 	'',
		),
		'email_icon'	=>	array(
			'label'		=> 	__('Email Icon','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'image',
			'default'	=> 	'',
		),
		'phone_icon'	=>	array(
			'label'		=> 	__('Phone Icon','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'image',
			'default'	=> 	'',
		),
		'date_icon'	=>	array(
			'label'		=> 	__('Date Icon','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'image',
			'default'	=> 	'',
		),
		'url_icon'	=>	array(
			'label'		=> 	__('Url Icon','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'image',
			'default'	=> 	'',
		),
		'password_icon'	=>	array(
			'label'		=> 	__('Password Icon','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'image',
			'default'	=> 	'',
		),
	),
	'scripts'	=> array(
		'image-modal.js',
	),
	'multiple'	=> false,
);

