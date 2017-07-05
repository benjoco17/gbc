<div class="ihc-ap-wrap">
	<?php if (!empty($data['title'])):?>
		<h3><?php echo do_shortcode($data['title']);?></h3>
	<?php endif;?>
	<?php if (!empty($data['content'])):?>
		<p><?php echo do_shortcode($data['content']);?></p>
	<?php endif;?>
	<form method="post" action="">
		<div class="ihc-form-line-register ihc-form-text">
			<label class="ihc-labels-register" style="font-weight:bold"><?php _e('User Token', 'uap');?></label>
			<input type="text" name="ihc_pushover_token" value="<?php echo $data['ihc_pushover_token'];?>"/>
		</div>
		<div class="ihc-submit-form" style="margin-top:30px;">
			<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="indeed_submit" class="ihc-submit-bttn-fe" />
		</div>
	</form>
</div>
