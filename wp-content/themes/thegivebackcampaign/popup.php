<div class="root">
<div class="backdrop_login"></div>
	<div class="box_login">
                <div class="close_login" style="color:red;">X</div>
                <div class="header">
                   <img width="325" border="0" alt="The Give Back Campaign" class="logoclass" src="https://www.thegivebackcampaign.com/images/logo.png">
                </div>
                <div class="msg " style="margin-top:10px;border: none;padding:10px;"><div style="text-align:center;">
                 <div class="col tabbed">
                  <!--<ul class="tabs">
                   <li><a href="#logins1">I already have an account</a></li>
                   <li><a href="#logins2">I am a new user</a></li>
                  </ul>-->
                  <article id="logins1" class="tab-content" style="display: block;"> <div class="loginresponse"></div><br>
                    <strong>Enter your username and password to access your account.</strong>
                     <!--<form class="" name="login_frmss" id="login_frmss" method="post">
                      <table class="signup_tbl">
                       <tbody><tr><td>
                        <strong>User name</strong></td><td>
                        <input type="text" placeholder="Enter Email" class="validate[required,custom[email]]" name="login_email_id" id="login_email_id" value="" autocomplete="off" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAAAXNSR0IArs4c6QAAAPhJREFUOBHlU70KgzAQPlMhEvoQTg6OPoOjT+JWOnRqkUKHgqWP4OQbOPokTk6OTkVULNSLVc62oJmbIdzd95NcuGjX2/3YVI/Ts+t0WLE2ut5xsQ0O+90F6UxFjAI8qNcEGONia08e6MNONYwCS7EQAizLmtGUDEzTBNd1fxsYhjEBnHPQNG3KKTYV34F8ec/zwHEciOMYyrIE3/ehKAqIoggo9inGXKmFXwbyBkmSQJqmUNe15IRhCG3byphitm1/eUzDM4qR0TTNjEixGdAnSi3keS5vSk2UDKqqgizLqB4YzvassiKhGtZ/jDMtLOnHz7TE+yf8BaDZXA509yeBAAAAAElFTkSuQmCC&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; cursor: auto;"></td></tr>
                        <tr><td><strong>Password</strong></td><td> <input type="hidden" id="url" name="urls" value="">
                        <input type="password" required="" placeholder="Enter Password" class="field150 validate[required]" id="login_password" name="login_password" value="" autocomplete="off" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAAAXNSR0IArs4c6QAAAPhJREFUOBHlU70KgzAQPlMhEvoQTg6OPoOjT+JWOnRqkUKHgqWP4OQbOPokTk6OTkVULNSLVc62oJmbIdzd95NcuGjX2/3YVI/Ts+t0WLE2ut5xsQ0O+90F6UxFjAI8qNcEGONia08e6MNONYwCS7EQAizLmtGUDEzTBNd1fxsYhjEBnHPQNG3KKTYV34F8ec/zwHEciOMYyrIE3/ehKAqIoggo9inGXKmFXwbyBkmSQJqmUNe15IRhCG3byphitm1/eUzDM4qR0TTNjEixGdAnSi3keS5vSk2UDKqqgizLqB4YzvassiKhGtZ/jDMtLOnHz7TE+yf8BaDZXA509yeBAAAAAElFTkSuQmCC&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; cursor: auto;"></td></tr>
                       <tr><td colspan="2" style="text-align:center;"><br>
                           <input type="checkbox" name="autologin" id="autologin" value="1">  Remember Me<br><br>
                          <div style="display:none">
                               <input class="faux-submit" type="submit" value="Submit">
                          </div>
                         <span class="button btn-orange" style="" id="loginv" value="1">Login</span></td></tr>
                       </tbody></table>
                      </form>                     


					  <table> 
                        <tbody><tr>
                         <td><a href="https://www.thegivebackcampaign.com/forget">Forgotten Password</a> </td>  
                         <td>  <a href="https://www.thegivebackcampaign.com/reauthorize">Reauthorise your email</a>
                         </td>
                        </tr>
                      </tbody>
					  </table>-->
					  
					  
					  
					  <!-- Gravity Form -->
						<?php //dynamic_sibebar('Login Popup'); ?>
						<?php echo do_shortcode('[ihc-login-form]') ?>
					  <!-- Gravity Form -->
					  
					  
                 </article>
                 <article id="logins2" class="tab-content" style="display: none;">
                     <div class="signupresponse" style="color:red;"></div><br><strong>Register here to support your charity of choice raise funds</strong>
                      <form id="shopsign_frm">
                       <tr><tr></tr><tr>
					   <table class="signup_tbl">
                        <tbody>
						
						<tr><td>
                        <strong>First Name</strong></td><td>
                        <input type="text" placeholder="Enter First Name" class="validate[required]" style="width:273px;" name="first_name" id="login_firstnamess" value=""></td></tr>
                        <tr><td>
                        <strong>Email</strong></td><td>
                        <input type="text" placeholder="Enter Email" class="validate[required,custom[email]]" style="width:273px;" name="email" id="login_email_id1" value=""></td></tr>
                        <tr><td><strong>Country</strong></td><td align="left"><select class="field150 validate[required] " name="country" id="country">
                                                     </select></td>
                         </tr>
                         <tr><td><strong>Mobile No</strong></td><td>
                        <input type="text" required="" placeholder="Enter Mobile" class="field150 validate[required]" style="width:273px;" id="mobile" name="mobile" value=""></td></tr>
							 <tr>
								 <td>
									<strong>Charity</strong></td><td>
									<select name="campaign_id" id="campaign_id_fb" class="field150 validate[required] " style="max-width:300px;">
										<option value="">--- Select Campaign ---</option>
										<option value="202">Austin Pregnancy Resource Center</option>                            
										<option value="205">Bryan Adams PTSA</option>                            
										<option value="189">CIR </option>                            
										<option value="137">Clearbrook</option>                            
										<option value="164">CORA </option>                            
										<option value="130">Dallas Heritage Village</option>                            
										<option value="149">Dementia Society of America</option>                            
										<option value="215">Friends of the Bohm Theatre</option>                            
										<option value="228">Future, Hope &amp; Healing Center</option>                            
										<option value="190">Heavenly Mimi</option>                            
										<option value="141">LeaderSpark</option>                            
										<option value="127">Live in Love</option>                            
										<option value="155">MakeSafe International</option>                            
										<option value="118">Mission Waco, Mission World, Inc.</option>                            
										<option value="166">Presbyterian Center for Children</option>                            
										<option value="221">Progressive Residential Services, INC</option>                            
										<option value="214">The Art Center of Battle Creek</option>                            
										<option value="212">The Fountain Clinic </option>                            
										<option value="63">The Living Harvest, Inc.</option>                            
										<option value="134">The Women Warriors Foundation</option>                            
										<option value="201">United Way of Waco McLennan County</option>                            
										<option value="181">VFW Post 6796</option>                            
										<option value="173">When Everyone Survives Foundation</option>                            
										<option value="65">YAM of Daytona, Inc.</option>  
									</select>
								 </td>
							 </tr>
							<tr>
							   <td colspan="2" style="text-align:center;">
							   <br>							   
								<span class="button btn-orange" style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, #ff9e00 0%, #ff6a00 100%) repeat scroll 0 0; border: 1px solid #de6200;  border-radius: 3px; box-shadow: 0 2px 2px rgba(0, 0, 0, 0.2); color: #fff; display: inline-block;  height: 27px;font-weight:bold; line-height: 27px; padding: 0 20px;text-shadow: 0 -1px 0 #da5c00; text-transform: uppercase;" id="signupv" value="1">Signup</span>
							   </td>						  
							</tr>
						  
						</tbody>
						   </table>
                      </form>
                 </article>
                </div>  
               </div>   
              </div>   
            </div>            
           

			<style>
				#loginv{background: rgba(0, 0, 0, 0) linear-gradient(to bottom, #ff9e00 0%, #ff6a00 100%) repeat scroll 0 0;     border: 1px solid #de6200;  border-radius: 3px; box-shadow: 0 2px 2px rgba(0, 0, 0, 0.2); color: #fff; display: inline-block; font-weight:bold;height: 27px;     line-height: 27px; padding: 0 20px;text-shadow: 0 -1px 0 #da5c00; text-transform: uppercase;cursor: pointer;}
				#loginv:hover{background: rgba(0, 0, 0, 0) linear-gradient(to bottom, #ff6a00 0%, #ff9e00 100%) repeat scroll 0 0;}
				.backdrop_login{position:absolute;top:0px;
							left:0px;width:100%;
							height:100%;background:#000;
							opacity: 0.7;filter:alpha(opacity=0);
							z-index:50;display:block;
				}#login_password, #login_email_id{width:273px;}
				.img-border{box-shadow:none;}
				.box_login{		
							   position:absolute;top:8%;
						left:35%;width:450px;
						height:auto;background:#ffffff;
						z-index:51;padding:10px;
						-webkit-border-radius: 5px;-moz-border-radius: 5px;
						border-radius: 5px;-moz-box-shadow:0px 0px 5px #444444;
						-webkit-box-shadow:0px 0px 5px #444444;box-shadow:0px 0px 5px #444444;
						display:block;vertical-align:center;
				}
				.test{color:red;}
				.close_login{	float:right;margin-right:6px;cursor:pointer;} 
				.logoclass{margin-left:75px;}
				@media all and (max-width:768px){
				.logoclass{margin-left:0px;}
					#login_password, #login_email_id{width:195px;}
				}
				 @media all and (max-width:768px){
				  .gh .col2{width:100%;} body{padding:0px;}
				 }
			</style>
	<div class="backdropf" style="display:none;"></div>
		<div class="boxf" style="display:none;">
			<div class="close" style="color:red;">X</div>
			   <div class="header" style="background: #003366 none repeat scroll 0 0;">
				<img width="325" "border="0" alt="The Give Back Campaign" src="https://www.thegivebackcampaign.com/images/logo.png">
				</div>
			 
				<div class="msg " style="margin-top:10px;border: none;padding:10px;"><div style="text-align:center;"><h2>Please choose your country</h2>
				 <select id="current_country_id1" name="current_country_id" style="width:250px;"><option value="">Select Country</option>
								<option value="au">Australia</option>
									<option value="nz">New Zealand</option>
									<option value="gb">United Kingdom</option>
									<option value="us">United States</option>
						 </select>&nbsp;<button id="current_country_id1s" type="submit">GO</button><br><br><br>
				</div>  
			</div>               
		</div>
	</div>

	
