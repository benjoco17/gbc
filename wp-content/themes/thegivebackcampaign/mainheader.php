<header class="h6 ">
			<section class="top">
				<div>
					<p></p>
					<nav style="font-size:14px;margin-top: 6px;">Welcome  Guest |  						<?php if (is_user_logged_in()) {   ?>							<a style="font-size:14px;color:#ff8400;" href="<?php echo do_shortcode('[ihc-logout-link]') ?>">Logout</a>						<?php } else { ?>							<a style="font-size:14px;color:#ff8400;" id="login_popup" href="javascript:void(0);">Login</a>						<?php } ?>					</nav>
				</div>
			</section>
			<section class="main-header">
			<div class="maillogo1">

          <p class="logo"><a href="https://thegivebackcampaign.com/"><img src="<?php bloginfo('template_url')?>/images/page_template/logo.png" border="0" alt="The Give Back Campaign"></a></p>
		      <div class="logopart1" style="">
            <nav class="social">    			
        				<ul id="css3menu1">
                    <li>
                        <a href="https://facebook.com/TheGiveBackCampaignAU" target="blank" title="facebook">
        					       	<img src="<?php bloginfo('template_url')?>/images/page_template/Facebook_32x32.png" width="32" height="32" class="icons-view">
                         </a>
        							</li>
        							<li>
        									<a href="https://twitter.com/givebackcampau" target="blank" title="twitter">
        					         	<img src="<?php bloginfo('template_url')?>/images/page_template/Twitter_32x32.png" width="32" height="32" class="icons-view">
                          </a>
        							</li>
        							<li>
        									<a href="http://www.youtube.com/user/TheGiveBackCampaign" target="blank" title="youtube">
        					         	<img src="<?php bloginfo('template_url')?>/images/page_template/Youtube_32x32.png" width="32" height="32" class="icons-view">
                          </a>
        							</li>
        							<li>
        									<a href="http://www.linkedin.com/company/2722472" target="blank" title="linkedin">
        					         	<img src="<?php bloginfo('template_url')?>/images/page_template/Linkedin_32x32.png" width="32" height="32" class="icons-view">
                          </a>
        							</li>
        															 
        					   	<li>
          								<a href="https://uk.pinterest.com/thegivebackcamp/" target="blank" title="pinterest">
          					       	<img src="<?php bloginfo('template_url')?>/images/page_template/instagram.jpg" width="32" height="32" class="icons-view">
                          </a>
                      </li>
                  </ul>
        			</nav>
              <select name="current_country_id" id="current_country_id">
							    <option value="au" selected="selected">Australia</option>
								    <option value="nz">New Zealand</option>
								    <option value="gb">United Kingdom</option>
								    <option value="us">United States</option>
				      </select>
				    </div>

				</div>
			
      
      <?php wp_nav_menu(array('menu'=>'primary-menu', 'menu_id' => 'css3menu1', 'container_class' => 'mainmenu',  ));?>
			
			<div class="clear"></div>
		</section>
		</header>
