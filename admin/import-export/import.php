<?php
	do_action( 'cherry_plugin_pre_import' );
	wp_enqueue_script('cherry-plugin-import', CHERRY_PLUGIN_URL.'admin/js/import.js', array('jquery'), '1', true);
	wp_localize_script( 'cherry-plugin-import', 'import_ajax', array('url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('import_ajax-nonce')));

	$upload_size_unit = $max_upload_size = wp_max_upload_size();
	$byte_sizes = array( 'KB', 'MB', 'GB' );

	for ( $u = -1; $upload_size_unit > 1024 && $u < count( $byte_sizes ) - 1; $u++ ) {
		$upload_size_unit /= 1024;
	}

	if ( $u < 0 ) {
		$upload_size_unit = 0;
		$u = 0;
	} else {
		$upload_size_unit = (int) $upload_size_unit;
	}
	$upload_dir = wp_upload_dir();
	$upload_dir = $upload_dir['path'].'/';
	$action_url = CHERRY_PLUGIN_URL.'admin/import-export/upload.php?upload_dir='.str_replace("\\", "/", $upload_dir);

	add_thickbox();
	echo cherry_plugin_help_import_popup();
?>
<script type="text/javascript">
	var import_text = new Array(),
		action_url = '<?php echo $action_url ?>',
		max_file_size = <?php echo wp_max_upload_size(); ?>,
		step_href = 'admin.php?page=import-page&step=2';

		import_text['error_upload']		= "<?php _e( 'Upload Error', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['error_size']		= "<?php _e( 'The file is too big!', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['error_type']		= "<?php _e( 'The file type is error!', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['error_folder']		= "<?php _e( 'Folder cannot be uploaded!', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['uploading']		= "<?php _e( 'Uploading', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['upload']			= "<?php _e( 'Upload', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['upload_complete']	= "<?php _e( 'Upload Complete', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['item']				= "<?php _e( 'item', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['items']			= "<?php _e( 'items', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['uploaded_status_text']= "<?php _e( 'Sample data installing. Some steps may take some time depending on your server settings. Please be patient.', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['uploaded_status_text_1']= "<?php _e( 'Upload complete please click Continue Install button to proceed.', CHERRY_PLUGIN_DOMAIN) ?>";
		//xml status text
		import_text['import_xml']= "<?php _e( 'Importing XML', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['import_categories']= "<?php _e( 'Importing categories', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['import_tags']= "<?php _e( 'Importing tags', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['process_terms']= "<?php _e( 'Processing dependencies', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['import_posts']= "<?php _e( 'Importing posts. This may take some time. Please wait.', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['import_menu_item']= "<?php _e( 'Importing menu items. This may take some time. Please wait.', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['import_attachment']= "<?php _e( 'Importing media library.', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['import_attachment_metadata']= "<?php _e( 'Importing attachements meta.', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['generate_attachment_metadata']= "<?php _e( 'Generating attachements meta. This may take some time. Please wait.', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['import_parents']= "<?php _e( 'Generating content hierarchy', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['update_featured_images']= "<?php _e( 'Updating featured images', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['update_attachment']= "<?php _e( 'Updating attachments', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['import_json']= "<?php _e( 'Importing JSON', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['import_complete']= "<?php _e( 'Installing content complete', CHERRY_PLUGIN_DOMAIN) ?>";
		import_text['instal_error']= "<?php _e( 'Installing content error', CHERRY_PLUGIN_DOMAIN) ?>";
</script>
<?php
	echo cherry_add_notice(array(
			'wrapper_id' => 'importing_warning',
			'wrapper_class' => 'error',
			'notice_content' => '<b>'.__('Warning!', CHERRY_PLUGIN_DOMAIN).'</b> '.__('Installing sample data will replace your website content. Please make sure to backup your website data before importing content.', CHERRY_PLUGIN_DOMAIN)
		)
	);
?>
<!-- drag drop form -->
<form enctype="multipart/form-data" method="post" action="<?php echo $action_url ?>" id="upload_files">
	<div id="area-drag-drop">
		<div class="drag-drop-inside">
			<p class="drag-drop-info"><?php _e('Please Drop all needed files here <br> to import sample data', CHERRY_PLUGIN_DOMAIN); ?></p>
			<p><?php _e('or', CHERRY_PLUGIN_DOMAIN); ?></p>
			<p class="drag-drop-buttons">
				<input class="uplupload-files" type="button" value="<?php _e('Browse local files', CHERRY_PLUGIN_DOMAIN); ?>" class="button" >
				<input id="upload_files_html5" style="visibility: hidden; width: 0; height: 0; overflow: hidden; margin:0;" type="file" multiple>
			</p>
			<p class="max-upload-size"><?php printf( __( 'Maximum upload file size: %d %s.', CHERRY_PLUGIN_DOMAIN), esc_html($upload_size_unit), esc_html($byte_sizes[$u]) ); ?></p>
			<p id="import-demo-video">
				<a href="#TB_inline?width=600&height=505&inlineId=help_import" class="thickbox" title="<?php _e('Files Import demo', CHERRY_PLUGIN_DOMAIN); ?>">
					<?php _e('View Demo', CHERRY_PLUGIN_DOMAIN); ?>
					<i class="icon-facetime-video"></i>
				</a>
			</p>
		</div>
	</div>
</form>
<!-- end drag drop form -->
<div id="import_step_2" class="hidden_ell">
<!-- file_list -->
	<div id="file_list_holder">
		<div id="file_list">
			<div id="file_list_header">
				<div class='row'>
					<div class="column_1"><?php _e( "File name", CHERRY_PLUGIN_DOMAIN) ?></div><div class="column_2"><?php _e( "File size", CHERRY_PLUGIN_DOMAIN) ?></div><div class="column_3"><?php _e('Uploaded file:', CHERRY_PLUGIN_DOMAIN); ?> <span id="upload_counter"><b>0</b></span> <span class="items_name"><?php _e( "item", CHERRY_PLUGIN_DOMAIN) ?></span></div>
				</div>
			</div>
			<div id="file_list_body"></div>
		</div>
	</div>
<!-- end file_list -->
<!-- log -->
	<div id="import_xml_status" class="hidden_ell">
		<div id="status_log">
			<p><i class ="spinner"></i><?php _e('Installing content started.', CHERRY_PLUGIN_DOMAIN); ?></p>
		</div>
	</div>
<!--end log -->
	<div id="import_status" class="clearfix">
		<div id='upload_status'>
			<div style="text-align: right; margin: 0 10px 3px 0;"><?php _e('Upload', CHERRY_PLUGIN_DOMAIN); ?></div>
			<div class="loader_bar"><span class='transition_2'></span></div>
		</div>
		<div id='import_data' class="clearfix">
			<div  style="margin: 0 0 3px 10px;"><?php _e('Install', CHERRY_PLUGIN_DOMAIN); ?></div>
			<div class="loader_bar"><span class='transition'></span></div>
		</div>
		<div id="info_holder" class="hidden_ell">
			<p>
				<span class="upload_status_text"><?php _e( "Files successfully uploaded. Please make sure you have uploaded <b>.JSON</b> and <b>.XML</b> files to install theme sample data.", CHERRY_PLUGIN_DOMAIN) ?><a href="http://info.template-help.com/help/quick-start-guide/wordpress-themes/master/index_en.html#theme_sample_data" target="_blank" id="info_link"><i class="icon-info-sign"></i></a></span>
				<br>
				<a class="uplupload-files" href="#"><?php _e('Add More Files', CHERRY_PLUGIN_DOMAIN); ?></a>
			</p>
			<a class="button button-primary not_active next_step" href="#"><?php _e('Continue Install', CHERRY_PLUGIN_DOMAIN); ?></a><span id="not_load_file" class="hidden_ell"><?php _e('Missing .XML or .JSON file', CHERRY_PLUGIN_DOMAIN); ?></span>
		</div>
	</div>
</div>
<div id="import_step_3" class="hidden_ell">
	<div class="clearfix">
		<h2 id="import_title"><?php _e('Congratulations', CHERRY_PLUGIN_DOMAIN); ?></h2>
		<span id="import_description"><?php _e('Content has been installed successfully', CHERRY_PLUGIN_DOMAIN); ?></span>
		<div id="import_links">
			<a href="<?php echo home_url(); ?>" target="_blank"><?php _e('View your site', CHERRY_PLUGIN_DOMAIN); ?></a> <?php _e('or', CHERRY_PLUGIN_DOMAIN); ?>
			<a href="<?php echo bloginfo( 'wpurl' ).'/wp-admin/admin.php?page=options-framework'; ?>"><?php _e('go to Cherry Options', CHERRY_PLUGIN_DOMAIN); ?></a>
		</div>
	</div>
</div>