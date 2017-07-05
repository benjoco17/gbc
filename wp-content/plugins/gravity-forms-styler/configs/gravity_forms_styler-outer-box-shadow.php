<?php
/**
*
* fieldconfig for gravity-forms-styler/Outer Box Shadow
*
* @package Gravity_Forms_Styler
* @author WordpressGurus support@wordpressgurus.net
* @license GPL-2.0+
* @link http://www.wordpressgurus.net
* @copyright 2014 WordpressGurus
*/


$group = array(
	'label' => __('Outer Box Shadow','gravity-forms-styler'),
	'id' => '1151161411',
	'master' => 'bs_hr_offset',
	'fields' => array(
		'bs_hr_offset'	=>	array(
			'label'		=> 	__('The horizontal offset','gravity-forms-styler'),
			'caption'	=>	__('The horizontal offset of the shadow, positive means the shadow will be on the right of the box, a negative offset will put the shadow on the left of the box.','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'bs_vertical_offset'	=>	array(
			'label'		=> 	__('The vertical offset','gravity-forms-styler'),
			'caption'	=>	__('The vertical offset of the shadow, a negative one means the box-shadow will be above the box, a positive one means the shadow will be below the box.','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'bs_blur_radius'	=>	array(
			'label'		=> 	__('The blur radius','gravity-forms-styler'),
			'caption'	=>	__('The blur radius (optional), if set to 0 the shadow will be sharp, the higher the number, the more blurred it will be.','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'bs_spread_radius'	=>	array(
			'label'		=> 	__('The spread radius','gravity-forms-styler'),
			'caption'	=>	__('The spread radius (optional), positive values increase the size of the shadow, negative values decrease the size. Default is 0 (the shadow is same size as blur).','gravity-forms-styler'),
			'type'		=>	'smalltextfield',
			'default'	=> 	'',
		),
		'bs_shadow_color'	=>	array(
			'label'		=> 	__('Color','gravity-forms-styler'),
          'caption'   =>  '',
			'type'		=>	'colorpicker',
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

