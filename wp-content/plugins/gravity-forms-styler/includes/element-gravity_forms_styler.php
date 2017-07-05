[gravityform id="<?php echo implode(", ", (array)$atts['form_id']); ?>" title="<?php echo implode(", ", (array)$atts['form_title']); ?>" description="<?php echo implode(", ", (array)$atts['form_description']); ?>" ajax="<?php echo implode(", ", (array)$atts['ajax']); ?>"]

<?php if(yes == $atts['use_tab_index']){ ?>
[gravityform id="<?php echo implode(", ", (array)$atts['form_id']); ?>" title="<?php echo implode(", ", (array)$atts['form_title']); ?>" description="<?php echo implode(", ", (array)$atts['form_description']); ?>" ajax="<?php echo implode(", ", (array)$atts['ajax']); ?>" tabindex="<?php echo implode(", ", (array)$atts['tab_index_number']); ?>"]
<?php } ?>
