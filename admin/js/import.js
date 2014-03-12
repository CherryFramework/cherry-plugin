jQuery(document).ready(function() {
	if (typeof(window.FileReader) == 'undefined' && window.location.search.indexOf('not_supported=true')==-1) {
		window.location.search = '?page=import-page&not_supported=true';
	}else{
		var upload_files_html5 = jQuery("#upload_files_html5"),
			upload_button = jQuery('.uplupload-files'),
			drop_zone = jQuery('#area-drag-drop, #file_list_holder'),
			drop_zone_inside = jQuery('.drag-drop-inside'),
			upload_table = jQuery('#file_list'),
			continue_install = jQuery('.next_step'),
			files_array = new Array(),
			row_class='alternate',
			last_add_file,
			drop_file_list,
			loaded_XML= false,
			loaded_JSON= false;

		drop_zone.on('dragover', function() {
			drop_zone.parent().addClass('hover');
			return false;
		}).on('dragleave', function() {
			drop_zone.parent().removeClass('hover');
			return false;
		}).on('drop', function(event) {
			get_file_list(event.originalEvent.dataTransfer.files);
			return false;
		});
		upload_button.on('click', add_more_files);
		upload_files_html5.on('change', function(){
			get_file_list(jQuery(this)[0].files);
		})
		jQuery("form#upload_files").on('mouseenter', function(){
			drop_zone.removeClass('pointer_events');
		})
	}
	function add_more_files(){
		drop_zone.addClass('pointer_events');
		upload_files_html5.click();
		return !1;
	}
	function get_file_list(file_list){
		upload_button.off();

		drop_file_list = file_list;
		last_add_file=0;

		jQuery('#upload_status .loader_bar span b').css({'width':'0%'});
		drop_zone.parent().removeClass('hover');
		jQuery("form#upload_files").removeClass('add_files');
		upload_table.parents('#import_step_2').removeClass('hidden_ell');

		add_file(drop_file_list[last_add_file]);
	}

	function add_file(file){
		var file_name = file.name,
			file_type = file.type;
		file_type = file_type.replace(' ', '');

		last_add_file++;
		
		if(!in_array(files_array, file_name)){
			var upload_file_num = files_array.length-1,
				file_size = file.size,
				file_size_type = ['B', 'KB', 'MB', 'GB'];

			files_array.push(file_name);
			row_class = row_class == 'alternate' ? '' : 'alternate' ;

			if(file.type == 'text/xml') loaded_XML = file_name ;
			if(file_name.indexOf('.json') !=-1) loaded_JSON = file_name ;

			for (var i = 0; file_size > 1024 && i < file_size_type.length - 1; i++ ) {
				file_size /= 1024;
			};
			jQuery('#file_list_body', upload_table).prepend('<div id="file_status_'+upload_file_num+'" class="row '+row_class+'" ><div class="column_1">'+file_name+'</div><div class="column_2">'+file_size.toFixed(2)+' '+file_size_type[i]+'</div><div class="column_3"><span class="file_progress_bar"></span><span class="file_progress_text">'+import_text['upload']+' <span class="load_percent">0</span> %</span></div></div>');

			if (file.size > max_file_size) {
				jQuery('#file_status_'+upload_file_num).addClass('error_file').find('.file_progress_text').html(import_text['error_size']);
				jQuery('#error_counter b').html(parseInt(jQuery('#error_counter b').text())+1);
				switch_file(last_add_file);
			}else if(file.name.indexOf('.')==-1 && file.type == ""){
				jQuery('#file_status_'+upload_file_num).addClass('error_file').find('.file_progress_text').html(import_text['error_folder']);
				jQuery('#error_counter b').html(parseInt(jQuery('#error_counter b').text())+1);
				switch_file(last_add_file);
			}else{
				var form_data = new FormData();
				form_data.append('file', file);
				send_file(form_data, upload_file_num);
			}
		}else{
			switch_file(last_add_file);
		}
	}
	
	function send_file(file_to_send, file_num){
		var xhr = new XMLHttpRequest();
		xhr.onload = function(data){
			var file_status_row =  jQuery('#file_status_'+file_num),
				loader_bar = jQuery('.file_progress_bar', file_status_row);

			jQuery('.load_percent', file_status_row).text('100');
			loader_bar.css({'width':'100%'});
			setTimeout(function(){
				loader_bar.addClass('transition').css({'opacity':0});
			},500);

			switch_file(last_add_file);
		};
		xhr.upload.onprogress = function(event){
			upload_progress(event, file_num);
		};
		xhr.open('POST', action_url);
		xhr.setRequestHeader('X-FILE-NAME', file_num);
		xhr.send(file_to_send);
	}
	function upload_progress(event, file_num) {
		var percent = parseInt(event.loaded / event.total * 100);
		jQuery('.load_percent', '#file_status_'+file_num).text(percent);
		jQuery('.file_progress_bar', '#file_status_'+file_num).css({'width':percent+'%'});
	}
	function switch_file(file_num){
		var percent = parseInt(file_num / drop_file_list.length * 100);
		jQuery('#upload_status .loader_bar span').css({'width':percent+'%'});
		jQuery('#upload_counter b').html(parseInt(jQuery('#upload_counter b').text())+1);

		if(drop_file_list[file_num]){
			add_file(drop_file_list[file_num]);
		}else{
			setTimeout(function(){
				load_all_content();
			}, 1000);
		}
	}
	function load_all_content(){
		jQuery('#info_holder').removeClass('hidden_ell');
		upload_button.on('click', add_more_files);
		continue_install.off();
		if(loaded_XML && loaded_JSON){
			jQuery('#info_holder p .upload_status_text').html(import_text['uploaded_status_text_1']);
			jQuery("#not_load_file").addClass('hidden_ell');
			jQuery('#upload_status ').addClass('upload_done');
			
			continue_install.removeClass('not_active').on('click', function(){
				drop_zone.off();
				upload_button.off();
				upload_files_html5.off();

				if(loaded_XML){
					ajax_post('import_xml', loaded_XML);
				}else{
					jQuery('#import_data .loader_bar span').css({'width':'50%'});
					ajax_post('import_json', loaded_JSON);
				}
				
				jQuery('#info_holder').find('.upload_status_text').html(import_text['uploaded_status_text']);
				jQuery('#info_holder').find('.uplupload-files').addClass('hidden_ell');
				jQuery('#import_xml_status').removeClass('hidden_ell');
				jQuery('#file_list_holder').addClass('hidden_ell');
				jQuery('#importing_warning').addClass('hidden_ell');
				jQuery(this).off('click').addClass('hidden_ell');
				return false;
			});
		}else{
			continue_install.on('click', function(){
				jQuery("#not_load_file").removeClass('hidden_ell');
			})
		}
	}
	function ajax_post(action, file){
		var data = {
			action: action,
			file:file!=0 ? file : 0,
			nonce : import_ajax.nonce
		};

		if(import_text[action]!=undefined){
			add_text_status(action);
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type:'POST',
				success:function(response) {
					if(response=="error"){
						error_status();
					}else if(loaded_XML){
						switch_ajax_post(response);
					}else{
						//import complete
						add_text_status('import_complete');
					}
				},
				error:function(response) {
					error_status();
				},
				timeout: 900000
			});
		}else{
			error_status();
		}
	}
	function switch_ajax_post(response){
		switch (response) {
			case '0':
				error_status();
			break;
			case 'error':
				error_status();
			break;
			case 'undefined':
				error_status();
			break;
			case 'import_end':
				add_text_status('import_complete');
			break;
			case 'import_json':
				if(loaded_JSON){
					ajax_post(response, loaded_JSON);
				}else{
					add_text_status('import_complete');
				}
			break
			default:
				var load_bar_percent = jQuery('#import_data .loader_bar span').width()/jQuery('#import_data .loader_bar').width()*100;
				jQuery('#import_data .loader_bar span').css({'width':(load_bar_percent+8.4)+"%"});
				ajax_post(response, 0);
			break;
		}
	}
	function add_text_status(text_index){
		jQuery('#status_log p:last-child').removeClass().addClass('done_import').find('i').removeClass().addClass('icon-ok');
		if(text_index == 'import_complete'){
			jQuery('#status_log').append('<p class ="done_import"><i class ="icon-ok"></i>'+import_text['import_complete']+'</p>');
			instal_content_done();
		}else{
			jQuery('#status_log').append('<p><i class ="spinner"></i>'+import_text[text_index]+'</p>');
		}
	}
	function instal_content_done(){
		jQuery('#import_data .loader_bar span').css({'width':'100%'});

		setTimeout(function(){
			jQuery('#import_step_2').addClass('hidden_ell');
			jQuery('#import_step_3').removeClass('hidden_ell');
			jQuery("form#upload_files").addClass('hidden_ell');
		}, 2000);
	}
	function error_status(){
		jQuery('#status_log p:last-child').removeClass().addClass('error_import').find('i').removeClass().addClass('icon-remove');
		jQuery('#status_log').append('<p class="error_import"><i class ="icon-warning-sign"></i>'+import_text['instal_error']+'</p>');
		jQuery('#import_data .loader_bar span').css({'width':'100%', 'background':'red'});
	}
	function in_array(array, value) {
		for(var i=0; i<array.length; i++) {
			if (array[i] == value) return true;
		}
		return false;
	}
});