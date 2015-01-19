<?php
	add_action('wp_ajax_import_json', 'cherry_plugin_import_json');
	function cherry_plugin_import_json() {
		do_action( 'cherry_plugin_import_json' );

		$json_file = isset($_POST['file']) ? $_POST['file'] : 'false' ;

		$json = file_get_contents(UPLOAD_DIR.$json_file);

		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir['url'];
		$cut_upload_dir = substr($upload_dir, strpos($upload_dir, 'wp-content/uploads'), strlen($upload_dir)-1);
		$cut_upload_dir = str_replace('/', '\/', $cut_upload_dir);

		$pattern = "#wp-content\\\/uploads\\\/\d{4}\\\/\d{2}#i";
		$json = preg_replace($pattern, $cut_upload_dir, $json);

		if( is_wp_error($json) ) {
			exit('error');
		};

		$json_data    = json_decode( $json, true );
		$sidebar_data = $json_data[0];
		$widget_data  = $json_data[1];
		$rules_data   = isset($json_data[2])? $json_data[2] : array();

		// get option value
		$themename    = 'cherry';
		$options_type = get_option($themename . '_widget_rules_type');
		$options      = get_option($themename . '_widget_rules');
		$custom_class = get_option($themename . '_widget_custom_class');
		$responsive   = get_option($themename . '_widget_responsive');
		$users        = get_option($themename . '_widget_users');

		// if this option is set at first time
		if ( !is_array($options_type) ) {
			$options_type = array();
		}
		// if this option is set at first time
		if ( !is_array($options) ) {
			$options = array();
		}
		// if this responsive is set at first time
		if ( !is_array($responsive) ) {
			$responsive = array();
		}
		// if this users is set at first time
		if ( !is_array($users) ) {
			$users = array();
		}

		if(!empty($rules_data)) {
			$new = array('widget_responsive');
			foreach($rules_data as $key => $value) {
				foreach ($value as $val) {
					$new[$key] = $val;
				}
			}
			$new['widget_responsive'] = isset($new['widget_responsive']) ? $new['widget_responsive'] : array() ;

			$options_type_new = array_merge($options_type, $new['widget_rules_type']);
			$options_new      = array_merge($options, $new['widget_rules']);
			$custom_class_new = $new['widget_custom_class'];
			$responsive_new   = array_merge($responsive, $new['widget_responsive']);
			$users_new        = array_merge($users, $new['widget_users']);

			// update option
			update_option($themename.'_widget_rules_type', $options_type_new);
			update_option($themename.'_widget_rules', $options_new);
			update_option($themename.'_widget_custom_class', $custom_class_new);
			update_option($themename.'_widget_responsive'.$key, $responsive_new);
			update_option($themename.'_widget_users', $users_new);
		}
		foreach ( $widget_data as $widget_title => $widget_value ) {
			foreach ( $widget_value as $widget_key => $widget_value ) {
				// fix for nav_menu widget
				if ( $widget_title == 'nav_menu' ) {
					if(is_array($widget_data[$widget_title][$widget_key])){
						if ( array_key_exists('nav_menu_slug', $widget_data[$widget_title][$widget_key]) ) {
							$nav_menu_slug = $widget_data[$widget_title][$widget_key]['nav_menu_slug'];

							$term_id = term_exists( $nav_menu_slug, 'nav_menu' );
							if ( $term_id ) {
								if ( is_array($term_id) ) $term_id = $term_id['term_id'];
								$widget_data['nav_menu'][$widget_key]['nav_menu'] = $term_id;
							}
						}
					}
				}
			}
		}

		$sidebar_data = array( array_filter( $sidebar_data ), $widget_data );

		if( cherry_plugin_parse_import_data( $sidebar_data ) ) {
			exit('import_end');
		}else{
			exit('error');
		};
	}
	function cherry_plugin_parse_import_data( $import_array ) {
		$sidebars_data = $import_array[0];
		$widget_data = $import_array[1];
		$current_sidebars = get_option( 'sidebars_widgets' );
		$new_widgets = array();
		$inactive_widgets  = array();
		foreach ( $current_sidebars as $import_sidebars => $import_sidebar ){
			if(is_array($import_sidebar)){
				array_push($import_sidebar, array());
			}
		}
		foreach ( $sidebars_data as $import_sidebar => $import_widgets ) :
			$current_sidebars[$import_sidebar] = array();
			foreach ( $import_widgets as $import_widget ) :
					$title = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
					$index = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
					$current_widget_data = get_option( 'widget_' . $title );
					$new_widget_name = get_new_widget_name( $title, $index );
					$new_index = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );

					if ( !empty( $new_widgets[ $title ] ) && is_array( $new_widgets[$title] ) ) {
						while ( array_key_exists( $new_index, $new_widgets[$title] ) ) {
							$new_index++;
						}
					}

					$current_sidebars[$import_sidebar][] = $title . '-' . $new_index;
					if ( array_key_exists( $title, $new_widgets ) ) {
						$new_widgets[$title][$new_index] = $widget_data[$title][$index];
						$multiwidget = $new_widgets[$title]['_multiwidget'];
						unset( $new_widgets[$title]['_multiwidget'] );
						$new_widgets[$title]['_multiwidget'] = $multiwidget;
					} else {
						$current_widget_data[$new_index] = $widget_data[$title][$index];
						$current_multiwidget = array_key_exists('_multiwidget', $current_widget_data ) ? $current_widget_data['_multiwidget'] : null ;
						$new_multiwidget = $widget_data[$title]['_multiwidget'];
						$multiwidget = ($current_multiwidget != $new_multiwidget) ? $current_multiwidget : 1;
						unset( $current_widget_data['_multiwidget'] );
						$current_widget_data['_multiwidget'] = $multiwidget;
						$new_widgets[$title] = $current_widget_data;
					}
			endforeach;
		endforeach;
		if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
			if(!empty($inactive_widgets)){
				$current_sidebars['wp_inactive_widgets'] = $inactive_widgets;
			}
			update_option( 'sidebars_widgets', $current_sidebars );

			foreach ( $new_widgets as $title => $content )
				update_option( 'widget_' . $title, $content );

			return true;
		}
		return false;
	}
	function get_new_widget_name( $widget_name, $widget_index ) {
		$current_sidebars = get_option( 'sidebars_widgets' );
		$all_widget_array = array( );
		foreach ( $current_sidebars as $sidebar => $widgets ) {
			if ( !empty( $widgets ) && is_array( $widgets ) && $sidebar != 'wp_inactive_widgets' ) {
				foreach ( $widgets as $widget ) {
					$all_widget_array[] = $widget;
				}
			}
		}
		/*while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
			$widget_index++;
		}*/
		$new_widget_name = $widget_name . '-' . $widget_index;
		return $new_widget_name;
	}


	add_action('wp_ajax_import_xml', 'cherry_plugin_import_xml');
	function cherry_plugin_import_xml(){
		$nonce = $_POST['nonce'];
		if ( !wp_verify_nonce( $nonce, 'import_ajax-nonce' ) )
			exit ( 'instal_error');

		if(session_id()!="import_xml"){
			session_name("import_xml");
			session_start();
		}

		do_action( 'cherry_plugin_start_import' );

		$_SESSION = array();
		do_action( 'cherry_plugin_import_xml' );

		add_filter( 'import_post_meta_key', 'cherry_plugin_is_valid_meta_key' );

		$xml_file = isset($_POST['file']) ? $_POST['file'] : 'false' ;
		$import_data = cherry_plugin_parse_xml( UPLOAD_DIR.$xml_file );

		if ( is_wp_error( $import_data ) ) {
			exit('error');
		}

		$_SESSION['categories'] = $import_data['categories'];
		$_SESSION['terms'] = $import_data['terms'];
		$_SESSION['tags'] = $import_data['tags'];
		$_SESSION['posts'] = $import_data['posts'];
		$_SESSION['site_settings'] = $import_data['site_settings'];

		cherry_plugin_import_start();

		exit('import_categories');
	}
	function cherry_plugin_import_start(){
		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );
		wp_suspend_cache_invalidation( true );
	}
	function cherry_plugin_import_end(){
		wp_cache_flush();
		foreach ( get_taxonomies() as $tax ) {
			delete_option( "{$tax}_children" );
			_get_term_hierarchy( $tax );
		}

		wp_defer_term_counting( false);
		wp_defer_comment_counting( false );

		update_option('cherry_sample_data', 1);

		cherry_plugin_set_to_draft('hello-world');
		cherry_plugin_set_to_draft('sample-page');

		settings();

		do_action( 'cherry_plugin_import_end' );
		session_name("import_xml");
		session_destroy();
		exit('import_json');
	}
	add_action('wp_ajax_import_categories', 'cherry_plugin_import_categories');
	function cherry_plugin_import_categories() {
		$nonce = $_POST['nonce'];
		if ( !wp_verify_nonce( $nonce, 'import_ajax-nonce' ) )
			exit ( 'instal_error');

		if(session_id()!="import_xml"){
			session_name("import_xml");
			session_start();
		}

		do_action( 'cherry_plugin_import_categories' );

		$categories_array = $_SESSION['categories'];
		$categories_array = apply_filters( 'wp_import_categories', $categories_array );

		if ( empty( $categories_array ) )
			exit('import_tags');

		foreach ( $categories_array as $cat ) {
			// if the category already exists leave it alone
			$term_id = term_exists( $cat['category_nicename'], 'category' );
			if ( $term_id ) {
				if ( is_array($term_id) ) $term_id = $term_id['term_id'];
				if ( isset($cat['term_id']) )
					$_SESSION['processed_terms'][intval($cat['term_id'])] = (int) $term_id;
				continue;
			}

			$category_parent = empty( $cat['category_parent'] ) ? 0 : category_exists( $cat['category_parent'] );
			$category_description = isset( $cat['category_description'] ) ? $cat['category_description'] : '';
			$catarr = array(
				'category_nicename' => $cat['category_nicename'],
				'category_parent' => $category_parent,
				'cat_name' => $cat['cat_name'],
				'category_description' => $category_description
			);

			$id = wp_insert_category( $catarr );
			if ( ! is_wp_error( $id ) ) {
				if ( isset($cat['term_id']) )
					$_SESSION['processed_terms'][intval($cat['term_id'])] = $id;
			} else {
				continue;
			}
		}
		unset($_SESSION['categories']);
		exit('import_tags');
	}
	add_action('wp_ajax_import_tags', 'cherry_plugin_import_tags');
	function cherry_plugin_import_tags() {
		$nonce = $_POST['nonce'];
		if ( !wp_verify_nonce( $nonce, 'import_ajax-nonce' ) )
			exit ( 'instal_error');

		if(session_id()!="import_xml"){
			session_name("import_xml");
			session_start();
		}

		do_action( 'cherry_plugin_import_tags' );

		$tag_array = $_SESSION['tags'];
		$tag_array = apply_filters( 'wp_import_tags', $tag_array );

		if ( empty( $tag_array ) )
			exit('process_terms');

		foreach ( $tag_array as $tag ) {
			// if the tag already exists leave it alone
			$term_id = term_exists( $tag['tag_slug'], 'post_tag' );
			if ( $term_id ) {
				if ( is_array($term_id) ) $term_id = $term_id['term_id'];
				if ( isset($tag['term_id']) )
					$_SESSION['processed_terms'][intval($tag['term_id'])] = (int) $term_id;
				continue;
			}

			$tag_desc = isset( $tag['tag_description'] ) ? $tag['tag_description'] : '';
			$tagarr = array( 'slug' => $tag['tag_slug'], 'description' => $tag_desc );

			$id = wp_insert_term( $tag['tag_name'], 'post_tag', $tagarr );
			if ( ! is_wp_error( $id ) ) {
				if ( isset($tag['term_id']) )
					$_SESSION['processed_terms'][intval($tag['term_id'])] = $id['term_id'];
			} else {
				continue;
			}
		}
		unset($_SESSION['tags']);
		exit('process_terms');
	}
	add_action('wp_ajax_process_terms', 'cherry_plugin_process_terms');
	function cherry_plugin_process_terms() {
		$nonce = $_POST['nonce'];
		if ( !wp_verify_nonce( $nonce, 'import_ajax-nonce' ) )
			exit ( 'instal_error');

		if(session_id()!="import_xml"){
			session_name("import_xml");
			session_start();
		}

		do_action( 'cherry_plugin_process_terms' );

		$terms = $_SESSION['terms'];
		$terms = apply_filters( 'wp_import_terms', $terms );

		if ( empty( $terms ) )
			exit('import_posts');

		foreach ( $terms as $term ) {
			// if the term already exists in the correct taxonomy leave it alone

			$term_id = term_exists( $term['slug'], $term['term_taxonomy'] );
			if ( $term_id ) {
				if ( is_array($term_id) ) {
					$term_id = $term_id['term_id'];
				}
				if ( isset($term['term_id']) ){
					$_SESSION['processed_terms'][intval($term['term_id'])] = (int) $term_id;
					continue;
				}
			}
			if ( empty( $term['term_parent'] ) ) {
				$parent = 0;
			} else {
				$parent = term_exists( $term['term_parent'], $term['term_taxonomy'] );
				if ( is_array( $parent ) ) $parent = $parent['term_id'];
			}
			$description = isset( $term['term_description'] ) ? $term['term_description'] : '';
			$termarr = array( 'slug' => $term['slug'], 'description' => $description, 'parent' => intval($parent) );

			$id = wp_insert_term( $term['term_name'], $term['term_taxonomy'], $termarr );

			if ( ! is_wp_error( $id ) ) {
				if ( isset($term['term_id']) )
					$_SESSION['processed_terms'][intval($term['term_id'])] = $id['term_id'];
			} else {
				// printf( theme_locals('failed_to_import'), esc_html($term['term_taxonomy']), esc_html($term['term_name']) );
				// echo '<br />';
				continue;
			}
		}

		unset($_SESSION['terms']);
		exit('import_posts');
	}
	add_action('wp_ajax_import_posts', 'cherry_plugin_import_posts');
	function cherry_plugin_import_posts() {
		$nonce = $_POST['nonce'];
		if ( !wp_verify_nonce( $nonce, 'import_ajax-nonce' ) )
			exit ( 'instal_error');

		if(session_id()!="import_xml"){
			session_name("import_xml");
			session_start();
		}

		do_action( 'cherry_plugin_import_posts' );

		$_SESSION['url_remap'] = array();
		$_SESSION['featured_images'] = array();
		$_SESSION['attachment_posts'] = array();
		$_SESSION['processed_posts'] = array();
		$posts_array = $_SESSION['posts'];
		$posts_array = apply_filters( 'wp_import_posts', $posts_array );
		$attachment_posts = array();

		foreach ( $posts_array as $post ) {
			$post = apply_filters( 'wp_import_post_data_raw', $post );

			if ( ! post_type_exists( $post['post_type'] ) ) {
				// Failed to import
				do_action( 'wp_import_post_exists', $post );
				continue;
			}

			if ( isset( $_SESSION['processed_posts'][$post['post_id']] ) && ! empty( $post['post_id'] ) )
				continue;

			if ( $post['status'] == 'auto-draft' )
				continue;

			if ( 'nav_menu_item' == $post['post_type'] ) {
				continue;
			}

			//!!!!$post_type_object = get_post_type_object( $post['post_type'] );

			$post_exists = post_exists( $post['post_title'], '', $post['post_date'] );
			if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
				// already exists
				$comment_post_ID = $post_id = $post_exists;
			} else {
				$post_parent = (int) $post['post_parent'];
				if ( $post_parent ) {
					// if we already know the parent, map it to the new local ID
					if ( isset( $_SESSION['processed_posts'][$post_parent] ) ) {
						$post_parent = $_SESSION['processed_posts'][$post_parent];
					// otherwise record the parent for later
					} else {
						$_SESSION['post_orphans'][intval($post['post_id'])] = $post_parent;
						$post_parent = 0;
					}
				}

				$author = (int) get_current_user_id();

				$postdata = array(
					'import_id' => $post['post_id'], 'post_author' => $author, 'post_date' => $post['post_date'],
					'post_date_gmt' => $post['post_date_gmt'], 'post_content' => $post['post_content'],
					'post_excerpt' => $post['post_excerpt'], 'post_title' => $post['post_title'],
					'post_status' => $post['status'], 'post_name' => $post['post_name'],
					'comment_status' => $post['comment_status'], 'ping_status' => $post['ping_status'],
					'guid' => $post['guid'], 'post_parent' => $post_parent, 'menu_order' => $post['menu_order'],
					'post_type' => $post['post_type'], 'post_password' => $post['post_password']
				);

				$original_post_ID = $post['post_id'];
				$postdata = apply_filters( 'wp_import_post_data_processed', $postdata, $post );

				if ( 'attachment' == $postdata['post_type'] ) {
					array_push($attachment_posts, $post);
				}
				if ( 'attachment' != $postdata['post_type'] ) {
					ini_set('max_execution_time', -1);
					set_time_limit(0);
					$comment_post_ID = $post_id = wp_insert_post( $postdata, true );
					do_action( 'wp_import_insert_post', $post_id, $original_post_ID, $postdata, $post );

					if ( is_wp_error( $post_id ) ) {
						// Failed to import
						continue;
					}

					if ( $post['is_sticky'] == 1 ) stick_post( $post_id );

					// map pre-import ID to local ID
					$_SESSION['processed_posts'][intval($post['post_id'])] = intval($post_id);

					if ( ! isset( $post['terms'] ) )
						$post['terms'] = array();

					$post['terms'] = apply_filters( 'wp_import_post_terms', $post['terms'], $post_id, $post );

					// add categories, tags and other terms
					if ( ! empty( $post['terms'] ) ) {
						$terms_to_set = array();
						foreach ( $post['terms'] as $term ) {
							// back compat with WXR 1.0 map 'tag' to 'post_tag'
							$taxonomy = ( 'tag' == $term['domain'] ) ? 'post_tag' : $term['domain'];
							$term_exists = term_exists( $term['slug'], $taxonomy );
							$term_id = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
							if ( ! $term_id ) {
								$t = wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
								if ( ! is_wp_error( $t ) ) {
									$term_id = $t['term_id'];
									do_action( 'cherry_plugin_import_insert_term', $t, $term, $post_id, $post );
								} else {
									// Failed to import
									do_action( 'cherry_plugin_import_insert_term_failed', $t, $term, $post_id, $post );
									continue;
								}
							}
							$terms_to_set[$taxonomy][] = intval( $term_id );
						}

						foreach ( $terms_to_set as $tax => $ids ) {
							$tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
							do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $post );
						}
						unset( $post['terms'], $terms_to_set );
					}

					if ( ! isset( $post['comments'] ) )
						$post['comments'] = array();

					$post['comments'] = apply_filters( 'wp_import_post_comments', $post['comments'], $post_id, $post );

					// add/update comments
					if ( ! empty( $post['comments'] ) ) {
						$num_comments = 0;
						$inserted_comments = array();
						foreach ( $post['comments'] as $comment ) {
							$comment_id	= $comment['comment_id'];
							$newcomments[$comment_id]['comment_post_ID']      = $comment_post_ID;
							$newcomments[$comment_id]['comment_author']       = $comment['comment_author'];
							$newcomments[$comment_id]['comment_author_email'] = $comment['comment_author_email'];
							$newcomments[$comment_id]['comment_author_IP']    = $comment['comment_author_IP'];
							$newcomments[$comment_id]['comment_author_url']   = $comment['comment_author_url'];
							$newcomments[$comment_id]['comment_date']         = $comment['comment_date'];
							$newcomments[$comment_id]['comment_date_gmt']     = $comment['comment_date_gmt'];
							$newcomments[$comment_id]['comment_content']      = $comment['comment_content'];
							$newcomments[$comment_id]['comment_approved']     = $comment['comment_approved'];
							$newcomments[$comment_id]['comment_type']         = $comment['comment_type'];
							$newcomments[$comment_id]['comment_parent'] 	  = $comment['comment_parent'];
							$newcomments[$comment_id]['commentmeta']          = isset( $comment['commentmeta'] ) ? $comment['commentmeta'] : array();
							if ( isset( $processed_authors[$comment['comment_user_id']] ) )
								$newcomments[$comment_id]['user_id'] = $processed_authors[$comment['comment_user_id']];
						}
						ksort( $newcomments );

						foreach ( $newcomments as $key => $comment ) {
							// if this is a new post we can skip the comment_exists() check
							if ( ! $post_exists || ! comment_exists( $comment['comment_author'], $comment['comment_date'] ) ) {
								if ( isset( $inserted_comments[$comment['comment_parent']] ) )
									$comment['comment_parent'] = $inserted_comments[$comment['comment_parent']];
								$comment = wp_filter_comment( $comment );
								$inserted_comments[$key] = wp_insert_comment( $comment );
								do_action( 'cherry_plugin_import_insert_comment', $inserted_comments[$key], $comment, $comment_post_ID, $post );

								foreach( $comment['commentmeta'] as $meta ) {
									$value = maybe_unserialize( $meta['value'] );
									add_comment_meta( $inserted_comments[$key], $meta['key'], $value );
								}

								$num_comments++;
							}
						}
						unset( $newcomments, $inserted_comments, $post['comments'] );
					}

					if ( ! isset( $post['postmeta'] ) )
						$post['postmeta'] = array();

					$post['postmeta'] = apply_filters( 'wp_import_post_meta', $post['postmeta'], $post_id, $post );

					// add/update post meta
					if ( isset( $post['postmeta'] ) ) {
						foreach ( $post['postmeta'] as $meta ) {
							$key = apply_filters( 'import_post_meta_key', $meta['key'] );
							$value = false;

							if ( '_edit_last' == $key ) {
								if ( isset( $processed_authors[intval($meta['value'])] ) )
									$value = $processed_authors[intval($meta['value'])];
								else
									$key = false;
							}

							if ( $key ) {
								// export gets meta straight from the DB so could have a serialized string
								if ( ! $value )
									$value = maybe_unserialize( $meta['value'] );
								ini_set('max_execution_time', -1);
								set_time_limit(0);
								add_post_meta( $post_id, $key, $value );
								do_action( 'cherry_plugin_import_post_meta', $post_id, $key, $value );

								// if the post has a featured image, take note of this in case of remap
								if ( '_thumbnail_id' == $key ){
									$_SESSION['featured_images'][$post_id] = (int) $value;
								}
							}
						}
					}
				}
			}
		}
		$_SESSION['attachment_posts'] = $attachment_posts;
		exit('import_menu_item');
	}
	add_action('wp_ajax_import_menu_item', 'cherry_plugin_import_menu_item');
	function cherry_plugin_import_menu_item() {
		$nonce = $_POST['nonce'];
		if ( !wp_verify_nonce( $nonce, 'import_ajax-nonce' ) )
			exit ( 'instal_error');

		if(session_id()!="import_xml"){
			session_name("import_xml");
			session_start();
		}

		do_action( 'cherry_plugin_import_menu_item' );

		$_SESSION['missing_menu_items'] = array();
		$posts_array = $_SESSION['posts'];
		$posts_array = apply_filters( 'wp_import_posts', $posts_array );

		unregister_nav_menu('Header Menu');
		unregister_nav_menu('Footer Menu');

		foreach ( $posts_array as $post ) {
			$post = apply_filters( 'wp_import_post_data_raw', $post );
			if ( 'nav_menu_item' == $post['post_type'] ) {
				cherry_plugin_add_item( $post );
			}else{
				continue;
			}
		}
		unset( $_SESSION['posts'] );
		exit('import_attachment');
	}
	function cherry_plugin_add_item( $item ) {
		// skip draft, orphaned menu items
		if ( 'draft' == $item['status'] )
			return;

		$menu_slug = false;
		if ( isset($item['terms']) ) {
			// loop through terms, assume first nav_menu term is correct menu
			foreach ( $item['terms'] as $term ) {
				if ( 'nav_menu' == $term['domain'] ) {
					$menu_slug = $term['slug'];
					//$menu_name = $term['slug'];
					break;
				}
			}
		}

		// no nav_menu term associated with this menu item
		if ( ! $menu_slug ) {
			// echo theme_locals('menu_item');
			return;
		}

		$menu_id = term_exists( $menu_slug, 'nav_menu' );
		if ( ! $menu_id ) {
			return;
		} else {
			$menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;
		}

		foreach ( $item['postmeta'] as $meta )
			$$meta['key'] = $meta['value'];

		if ( 'taxonomy' == $_menu_item_type && isset( $_SESSION['processed_terms'][intval($_menu_item_object_id)] ) ) {
			$_menu_item_object_id = $_SESSION['processed_terms'][intval($_menu_item_object_id)];
		} else if ( 'post_type' == $_menu_item_type && isset( $_SESSION['processed_posts'][intval($_menu_item_object_id)] ) ) {
			$_menu_item_object_id = $_SESSION['processed_posts'][intval($_menu_item_object_id)];
		} else if ( 'custom' != $_menu_item_type ) {
			// associated object is missing or not imported yet, we'll retry later
			$_SESSION['missing_menu_items'][] = $item;
			return;
		}

		if ( isset( $_SESSION['processed_menu_items'][intval($_menu_item_menu_item_parent)] ) ) {
			$_menu_item_menu_item_parent = $_SESSION['processed_menu_items'][intval($_menu_item_menu_item_parent)];
		} else if ( $_menu_item_menu_item_parent ) {
			$_SESSION['menu_item_orphans'][intval($item['post_id'])] = (int) $_menu_item_menu_item_parent;
			$_menu_item_menu_item_parent = 0;
		}

		// wp_update_nav_menu_item expects CSS classes as a space separated string
		$_menu_item_classes = maybe_unserialize( $_menu_item_classes );
		if ( is_array( $_menu_item_classes ) )
			$_menu_item_classes = implode( ' ', $_menu_item_classes );

		$args = array(
			'menu-item-object-id'   => $_menu_item_object_id,
			'menu-item-object'      => $_menu_item_object,
			'menu-item-parent-id'   => $_menu_item_menu_item_parent,
			'menu-item-position'    => intval( $item['menu_order'] ),
			'menu-item-type'        => $_menu_item_type,
			'menu-item-title'       => $item['post_title'],
			'menu-item-url'         => $_menu_item_url,
			'menu-item-description' => $item['post_content'],
			'menu-item-attr-title'  => $item['post_excerpt'],
			'menu-item-target'      => $_menu_item_target,
			'menu-item-classes'     => $_menu_item_classes,
			'menu-item-xfn'         => $_menu_item_xfn,
			'menu-item-status'      => $item['status']
		);
		$id = wp_update_nav_menu_item( $menu_id, 0, $args );
		if ( $id && ! is_wp_error( $id ) ) {
			$_SESSION['processed_menu_items'][intval($item['post_id'])] = (int) $id;

			/**
			 * Save menu badges meta data if is WooCommerce template
			 */
			if ( isset($_cherry_woo_badge_text) ) {
				update_post_meta( $id, '_cherry_woo_badge_text', $_cherry_woo_badge_text );
			}
			if ( isset($_cherry_woo_badge_type) ) {
				update_post_meta( $id, '_cherry_woo_badge_type', $_cherry_woo_badge_type );
			}
		}
	}
	add_action('wp_ajax_import_attachment', 'cherry_plugin_import_attachment');
	function cherry_plugin_import_attachment() {
		$nonce = $_POST['nonce'];
		if ( !wp_verify_nonce( $nonce, 'import_ajax-nonce' ) )
			exit ( 'instal_error');

		if(session_id()!="import_xml"){
			session_name("import_xml");
			session_start();
		}

		if(!empty($_SESSION['attachment_posts'])){
			do_action( 'cherry_plugin_import_attachment' );

			$_SESSION['missing_menu_items'] = array();
			$_SESSION['attachment_metapost'] = array();
			$posts_array = $_SESSION['attachment_posts'];
			$posts_array = apply_filters( 'wp_import_posts', $posts_array );
			$author = (int) get_current_user_id();

			foreach ( $posts_array as $post ) {
				$post = apply_filters( 'wp_import_post_data_raw', $post );

				$postdata = array(
					'import_id' => $post['post_id'],
					'post_author' => $author,
					'post_date' => $post['post_date'],
					'post_date_gmt' => $post['post_date_gmt'],
					'post_content' => $post['post_content'],
					'post_excerpt' => $post['post_excerpt'],
					'post_title' => $post['post_title'],
					'post_status' => $post['status'],
					'post_name' => $post['post_name'],
					'comment_status' => $post['comment_status'],
					'ping_status' => $post['ping_status'],
					'guid' => $post['guid'],
					/*'post_parent' => $post_parent,*/
					'menu_order' => $post['menu_order'],
					'post_type' => $post['post_type'],
					'post_password' => $post['post_password']
				);

				$postdata = apply_filters( 'wp_import_post_data_processed', $postdata, $post );

				$remote_url = ! empty($post['attachment_url']) ? $post['attachment_url'] : $post['guid'];
				// try to use _wp_attached file for upload folder placement to ensure the same location as the export site
				// e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload()
				$postdata['upload_date'] = $post['post_date'];
				$file_url = UPLOAD_DIR.basename( $remote_url );
				if(file_exists($file_url)){
					cherry_plugin_add_attachment( $postdata, $remote_url );
				}
			}
		}
		wp_suspend_cache_invalidation( false );
		exit('generate_attachment_metadata');
	}
	function cherry_plugin_add_attachment( $post, $url ) {
		$file_name = basename( $url );
		$upload['url'] = $url;
		$upload['file'] = UPLOAD_DIR.$file_name;
		if ( is_wp_error( $upload ) )
			return $upload;

		if ( $info = wp_check_filetype( $upload['file'] ) )
			$post['post_mime_type'] = $info['type'];
		else
			return new WP_Error( 'attachment_processing_error', theme_locals('Invalid file type'));

		$post['guid'] = $upload['url'];
		$_SESSION['url_remap'][$url] = $upload['url'];
		$_SESSION['url_remap'][$post['guid']] = $upload['url'];


		// as per wp-admin/includes/upload.php
		ini_set('max_execution_time', -1);
		set_time_limit(0);
		$post_id = wp_insert_attachment( $post, $upload['file'] );
		array_push($_SESSION['attachment_metapost'], array('post_id'=>$post_id, 'file'=>$upload['file']));

		$_SESSION['processed_posts'][intval($post['import_id'])] = intval($post_id);

		// remap resized image URLs, works by stripping the extension and remapping the URL stub.
		if ( preg_match( '!^image/!', $info['type'] ) ) {
			$parts = pathinfo( $url );
			$name = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

			$parts_new = pathinfo( $upload['url'] );
			$name_new = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

			$_SESSION['url_remap'][$parts['dirname'] . '/' . $name] = $parts_new['dirname'] . '/' . $name_new;
		}
		return $post_id;
	}
	add_action('wp_ajax_generate_attachment_metadata', 'cherry_plugin_generate_attachment_metadata');
	function cherry_plugin_generate_attachment_metadata() {
		$nonce = $_POST['nonce'];
		if ( !wp_verify_nonce( $nonce, 'import_ajax-nonce' ) )
			exit ( 'instal_error');

		if(session_id()!="import_xml"){
			session_name("import_xml");
			session_start();
		}
		do_action( 'cherry_plugin_generate_attachment_metadata' );
		if(!empty($_SESSION['attachment_posts'])){
			$metadata = $_SESSION['attachment_metapost'];

			foreach ($metadata as $key => $value) {
				ini_set('max_execution_time', -1);
				set_time_limit(0);
				$_SESSION['attachment_metapost'][$key]['file'] = wp_generate_attachment_metadata($value['post_id'], $value['file']);
			}
		}
		exit('import_attachment_metadata');
	}

	add_action('wp_ajax_import_attachment_metadata', 'cherry_plugin_import_attachment_metadata');
	function cherry_plugin_import_attachment_metadata() {
		$nonce = $_POST['nonce'];
		if ( !wp_verify_nonce( $nonce, 'import_ajax-nonce' ) )
			exit ( 'instal_error');

		if(session_id()!="import_xml"){
			session_name("import_xml");
			session_start();
		}
		if(!empty($_SESSION['attachment_posts'])){
			$generate_metadata = $_SESSION['attachment_metapost'];
			foreach ($generate_metadata as $key => $value) {
				ini_set('max_execution_time', -1);
				set_time_limit(0);
				wp_update_attachment_metadata($value['post_id'], $value['file']);
			}
		}
		unset($_SESSION['attachment_metapost']);
		exit('import_parents');
	}
	add_action('wp_ajax_import_parents', 'cherry_plugin_import_parents');
	function cherry_plugin_import_parents() {
		$nonce = $_POST['nonce'];
		if ( !wp_verify_nonce( $nonce, 'import_ajax-nonce' ) )
			exit ( 'instal_error');

		if(session_id()!="import_xml"){
			session_name("import_xml");
			session_start();
		}
		global $wpdb;

		do_action( 'cherry_plugin_import_parents' );

		// find parents for post orphans
		$post_orphans = array_key_exists('post_orphans', $_SESSION) ? $_SESSION['post_orphans'] : array() ;
		foreach ( $post_orphans as $child_id => $parent_id ) {
			$local_child_id = $local_parent_id = false;
			if ( isset( $_SESSION['processed_posts'][$child_id] ) )
				$local_child_id = $_SESSION['processed_posts'][$child_id];
			if ( isset( $_SESSION['processed_posts'][$parent_id] ) )
				$local_parent_id = $_SESSION['processed_posts'][$parent_id];

			if ( $local_child_id && $local_parent_id )
				$wpdb->update( $wpdb->posts, array( 'post_parent' => $local_parent_id ), array( 'ID' => $local_child_id ), '%d', '%d' );
		}

		// all other posts/terms are imported, retry menu items with missing associated object
		$missing_menu_items_arrary = $_SESSION['missing_menu_items'];
		foreach ($missing_menu_items_arrary as $item )
			cherry_plugin_add_item( $item );

		// find parents for menu item orphans
		$menu_item_orphans = array_key_exists('menu_item_orphans', $_SESSION) ? $_SESSION['menu_item_orphans'] : array() ;
		foreach ( $menu_item_orphans as $child_id => $parent_id ) {
			$local_child_id = $local_parent_id = 0;
			if ( isset( $_SESSION['processed_menu_items'][$child_id] ) )
				$local_child_id = $_SESSION['processed_menu_items'][$child_id];
			if ( isset( $_SESSION['processed_menu_items'][$parent_id] ) )
				$local_parent_id = $_SESSION['processed_menu_items'][$parent_id];

			if ( $local_child_id && $local_parent_id )
				update_post_meta( $local_child_id, '_menu_item_menu_item_parent', (int) $local_parent_id );
		}
		exit('update_featured_images');
	}

	add_action('wp_ajax_update_attachment', 'cherry_plugin_update_attachment');
	function cherry_plugin_update_attachment() {
		$nonce = $_POST['nonce'];
		if ( !wp_verify_nonce( $nonce, 'import_ajax-nonce' ) )
			exit ( 'instal_error');

		if(session_id()!="import_xml"){
			session_name("import_xml");
			session_start();
		}
		global $wpdb;

		do_action( 'cherry_plugin_update_attachment' );
		$url_remap=$_SESSION['url_remap'];
		// make sure we do the longest urls first, in case one is a substring of another
		uksort( $url_remap, 'sort_array');

		foreach ( $url_remap as $from_url => $to_url ) {
			// remap urls in post_content
			$wpdb->query( $wpdb->prepare("UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url, $to_url) );
			// remap enclosure urls
			$result = $wpdb->query( $wpdb->prepare("UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key='enclosure'", $from_url, $to_url) );
		}
		cherry_plugin_import_end();
	}
	function sort_array( $a, $b ) {
		return strlen($b) - strlen($a);
	}
	add_action('wp_ajax_update_featured_images', 'cherry_plugin_update_featured_images');
	function cherry_plugin_update_featured_images() {
		$nonce = $_POST['nonce'];
		if ( !wp_verify_nonce( $nonce, 'import_ajax-nonce' ) )
			exit ( 'instal_error');

		if(session_id()!="import_xml"){
			session_name("import_xml");
			session_start();
		}

		do_action( 'cherry_plugin_update_featured_images' );
		$featured_images = $_SESSION['featured_images'];
		// cycle through posts that have a featured image
		foreach ( $featured_images as $post_id => $value ) {
			if ( isset( $_SESSION['processed_posts'][$value] ) ) {
				$new_id = $_SESSION['processed_posts'][$value];
				// only update if there's a difference
				if ( $new_id != $value )
					update_post_meta( $post_id, '_thumbnail_id', $new_id );
			}
		}
		exit('update_attachment');
	}
	function cherry_plugin_parse_xml( $file ) {
		$file_content = file_get_contents($file);
		$file_content = iconv('utf-8', 'utf-8//IGNORE', $file_content);
		$file_content = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $file_content);

		if($file_content != null){
			$dom = new DOMDocument('1.0');
			$dom->loadXML( $file_content );

			$xml = simplexml_import_dom( $dom );
			$old_upload_url = $xml->xpath('/rss/channel/wp:base_site_url');
			$old_upload_url = $old_upload_url[0];
			$upload_dir = wp_upload_dir();
			$upload_dir = $upload_dir['url'];
			$cut_upload_dir = substr($upload_dir, strpos($upload_dir, 'wp-content/uploads'), strlen($upload_dir)-1);
			$cut_date_upload_dir = '<![CDATA['.substr($upload_dir, strpos($upload_dir, 'wp-content/uploads')+19, strlen($upload_dir)-1);
			$cut_date_upload_dir_2 = "\"".substr($upload_dir, strpos($upload_dir, 'wp-content/uploads')+19, strlen($upload_dir)-1);

			$pattern = '/wp-content\/uploads\/\d{4}\/\d{2}/i';
			$patternCDATA = '/<!\[CDATA\[\d{4}\/\d{2}/i';
			$pattern_meta_value = '/("|\')\d{4}\/\d{2}/i';

			$file_content = str_replace($old_upload_url, site_url(), $file_content);
			$file_content = preg_replace($patternCDATA, $cut_date_upload_dir, $file_content);
			$file_content = preg_replace($pattern_meta_value, $cut_date_upload_dir_2, $file_content);
			$file_content = preg_replace($pattern, $cut_upload_dir, $file_content);

			$parser = new WXR_Parser();
			$parser_array = $parser->parse( $file_content, $file );

			$parser_array['site_settings'] = array(
				'blogname' => 			implode ($xml->xpath('/rss/channel/title')),
				'blogdescription' => 	implode ($xml->xpath('/rss/channel/description'))/*,
				'pubDate' => 		implode ($xml->xpath('/rss/channel/pubDate')),
				'language' => 		implode ($xml->xpath('/rss/channel/language'))*/
			);

			return $parser_array;
		}else{
			exit('error');
		}
	}
	function settings() {
		global $wp_rewrite;

		do_action( 'cherry_plugin_set_settings' );

		foreach ($_SESSION['site_settings'] as $settings_name => $settings_value) {
			update_option($settings_name, $settings_value);
		}
		// Set Appearance -> Menu
		$menus = get_terms('nav_menu');
		$save = array();
		foreach($menus as $menu){
			if($menu->name == 'Header Menu'){
				$save['header_menu'] = $menu->term_id;
			}else if($menu->name == 'Footer Menu'){
				$save['footer_menu'] = $menu->term_id;
			}
		}
		if($save){
			set_theme_mod( 'nav_menu_locations', array_map( 'absint', $save ) );
		}

		// Set the front page
		update_option( 'show_on_front', 'page' );
		$home_pages = get_pages(
			array(
				'meta_key'   => '_wp_page_template',
				'meta_value' => 'page-home.php'
			)
		);
		if (!empty($home_pages)) {
			$home = $home_pages[0]->ID;
			update_option( 'page_on_front', $home );
		}

		// Set the blog page
		$default_pages = get_pages(
			array(
				'meta_key'   => '_wp_page_template',
				'meta_value' => 'default'
			)
		);
		if (!empty($default_pages)) {
			$blog = $default_pages[0]->ID;
			update_option( 'page_for_posts', $blog );
		}

		// Set post count for blog
		update_option( 'posts_per_page', 4 );

		// Set permalink custom structure
		$permalink_structure = '/%category%/%postname%/';
		update_option( 'permalink_structure', $permalink_structure );
		$wp_rewrite->set_permalink_structure( $permalink_structure );
		$wp_rewrite->flush_rules();


		//activate plugin form
		$plugin_path = WP_PLUGIN_DIR.'/contact-form-7/wp-contact-form-7.php';
		if(file_exists($plugin_path)){
			activate_plugin($plugin_path);
		}

		// write to .htaccess MIME Type
		$htaccess = ABSPATH .'/.htaccess';
		if (file_exists($htaccess)) {
			$fp = fopen($htaccess, 'a+');
			if ($fp) {
				$contents = fread($fp, filesize($htaccess));
				$pos = strpos('# AddType TYPE/SUBTYPE EXTENSION', $contents);
				if ( $pos!==false ) {
					fwrite($fp, "\r\n# AddType TYPE/SUBTYPE EXTENSION\r\n");
					fwrite($fp, "AddType audio/mpeg mp3\r\n");
					fwrite($fp, "AddType audio/mp4 m4a\r\n");
					fwrite($fp, "AddType audio/ogg ogg\r\n");
					fwrite($fp, "AddType audio/ogg oga\r\n");
					fwrite($fp, "AddType audio/webm webma\r\n");
					fwrite($fp, "AddType audio/wav wav\r\n");
					fwrite($fp, "AddType video/mp4 mp4\r\n");
					fwrite($fp, "AddType video/mp4 m4v\r\n");
					fwrite($fp, "AddType video/ogg ogv\r\n");
					fwrite($fp, "AddType video/webm webm\r\n");
					fwrite($fp, "AddType video/webm webmv\r\n");
					fclose($fp);
				}
			}
		}
	}
	/**
	 * Set post_status for default WP posts (post_status = draft)
	*/
	function cherry_plugin_set_to_draft($title) {
		global $wpdb;

		$id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '$title'");
		if ($id) {
			$default_post = array(
				'ID'           => $id,
				'post_status' => 'draft'
			);
			// Update the post into the database
			wp_update_post( $default_post );
		}
		$comment_id = $wpdb->get_var("SELECT comment_ID FROM $wpdb->comments WHERE comment_author = 'Mr WordPress'");
		if ($comment_id) wp_delete_comment($comment_id, false);
	}
	function cherry_plugin_is_valid_meta_key( $key ) {
		// skip attachment metadata since we'll regenerate it from scratch
		// skip _edit_lock as not relevant for import
		if ( in_array( $key, array( '_wp_attached_file', '_wp_attachment_metadata', '_edit_lock' ) ) )
			return false;
		return $key;
	}
//remove inactive widgets
	function cherry_plugin_remove_inactive_widgets(){
		$widgets = get_option( 'sidebars_widgets' );
		$widgets['wp_inactive_widgets'] = array();
		update_option( 'sidebars_widgets', $widgets );
		exit('done');
	}
	add_action('wp_ajax_remove_widgets', 'cherry_plugin_remove_inactive_widgets');
?>