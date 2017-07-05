<?php
if (!empty($_POST['import']) && !empty($_FILES['import_file'])){
	////////////////// IMPORT
	$filename = IHC_PATH . 'import.xml';
	move_uploaded_file($_FILES['import_file']['tmp_name'], $filename);
	require_once IHC_PATH . 'classes/Indeed_Import_Export/IndeedImport.class.php';
	require_once IHC_PATH . 'classes/Indeed_Import_Export/Ihc_Indeed_Import.class.php';	
	$import = new Ihc_Indeed_Import();
	$import->setFile($filename);
	$import->run();
} 
?>
<div class="ihc-stuffbox">
	<h3><?php _e('Export', 'ihc');?></h3>
	<div class="inside">				
		<div class="iump-form-line">
			<span class="iump-labels-special"></span>
			<div class="iump-form-line">
				<span style="font-weight:bold; display:inline-block; width: 25%;"><?php _e('Users', 'ihc');?></span>
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#import_users');" />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="import_users" value=0 id="import_users"/>			
			</div>	
			<div class="iump-form-line">
				<span style="font-weight:bold; display:inline-block; width: 25%;"><?php _e('Settings', 'ihc');?></span>
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#import_settings');" />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="import_settings" value=0 id="import_settings"/>			
			</div>		
			<div class="iump-form-line">
				<span style="font-weight:bold; display:inline-block; width: 25%;"><?php _e('Post Settings', 'ihc');?></span>
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#import_postmeta');" />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="import_postmeta" value=0 id="import_postmeta"/>			
			</div>												
		</div>	
			
		<div class="ihc-hidden-download-link" style="display: none;"><a href="" target="_blank" download>export.xml</a></div>	
		
		<div class="ihc-wrapp-submit-bttn">
			<div class="button button-primary button-large" onClick="ihc_make_export_file();"><?php _e('Export', 'ihc');?></div>
		</div>							
	</div>
</div>		

<form action="" method="post" enctype="multipart/form-data">
	<div class="ihc-stuffbox">
		<h3><?php _e('Import', 'ihc');?></h3>
		<div class="inside">				
			<div class="iump-form-line">
				<span class="iump-labels-special"><?php _e('File', 'ihc');?></span>
				<input type="file" name="import_file" />
			</div>	
					
			<div class="ihc-wrapp-submit-bttn">
				<input type="submit" value="<?php _e('Import', 'ihc');?>" name="import" class="button button-primary button-large">
			</div>							
		</div>
	</div>			
</form>

<?php


