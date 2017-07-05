<?php

	 function add_how_it_works($one, $two){ 

	 	$var_howItWorks = shortcode_atts(array(

	 			'title'			=> 'How it Works',
	 			'banner_img'	=> get_template_directory_uri().'/images/question.png',	
	 			'desc'			=> '',
	 			'ad_image'		=> '',
	 			'ad_image_link' => '',
	 			'bottom_img'	=> '',
	 				
	 		), $one);

	 		$img_banner 	= wp_get_attachment_image_src($var_howItWorks['banner_img'], 'full');
	 		$ad_img_banner  = wp_get_attachment_image_src($var_howItWorks['ad_image'], 'full');
	 		$bottom_img	    = wp_get_attachment_image_src($var_howItWorks['bottom_img'], 'full');

	 		ob_start();
	 	?>
			
		<div class="how_it_works">
			<div class="left_hiw">
				<section class="main single">

					<style>
						@media screen and ( max-width:768px ){
						.main  .img-border > img {width:100%;}
						.columns{padding:8px;}
						}
					</style>

					<section class="columns">
						<h2> <?php echo $var_howItWorks['title']; ?> </h2>
						<span class="img-border"><img src="<?php echo $img_banner[0]; ?>"></span><br><br>
						<div class="td_txt-color" style="text-align: justify">
							<?php echo $var_howItWorks['desc']; ?>
							<div align="center"><span class="img-border"><img src="<?php echo $bottom_img[0]; ?>"></span><br></div>
						</div>
					</section>
				</section>
			</div>

			<div class="right_hiw">
				<aside>

					<style>.content > aside section{margin:0px;}
					.so_line a{color: #0089B5 !important;} .imagh img{width:100% !important;}
					</style>

					<section class="columns_imagh" style="text-align:center;">
					<a href="<?php echo $var_howItWorks['ad_image_link']; ?>"><span class="img-border"><img src="<?php echo $ad_img_banner[0]; ?>"></span></a>
					<br><br><span style="text-align:center;"><strong>Give Back: 2.50%</strong></span>
					</section>

				</aside>
			</div>
		</div>

	 <?php
	 	return ob_get_clean();
	}
	 add_shortcode('howItWorks','add_how_it_works');

	 if(function_exists(vc_map)){

	 	vc_map(array(
	 			'name'				=> 'How It Works',
	 			'base'				=> 'howItWorks',
	 			'params'			=> array(
	 					array(
	 							'param_name'			=> 'title',
	 							'type'					=> 'textfield',
	 							'heading'				=> 'Put the title here'
	 						),
	 					array(
	 							'param_name'			=> 'banner',
	 							'type'					=> 'attach_image',
	 							'heading'				=> 'Page banner image'
	 						),
	 					array(
	 							'param_name'			=> 'desc',
	 							'type'					=> 'textarea',
	 							'heading'				=> 'Put the description here'
	 						),
	 					array(
	 							'param_name'			=> 'ad_image',
	 							'type'					=> 'attach_image',
	 							'heading'				=> 'Attach Ad Image here'
	 						),
	 					array(
	 							'param_name'			=> 'ad_image_link',
	 							'type'					=> 'textfield',
	 							'heading'				=> 'Put Ad Image link here'
	 						),
	 					array(
	 							'param_name'			=> 'bottom_img',
	 							'type'					=> 'attach_image',
	 							'heading'				=> 'Insert bottom image here'
	 						),
	 				)
	 		));
	 }



?>