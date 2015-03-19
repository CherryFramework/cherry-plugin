<?php
//include stylesheet files
	if ( !function_exists('cherry_include_stylesheet') ) {
		function cherry_include_stylesheet() {

			wp_deregister_style( 'flexslider' );
			wp_register_style( 'flexslider', CHERRY_PLUGIN_URL . 'lib/js/FlexSlider/flexslider.css', false, '2.2.0', 'all' );
			wp_register_style( 'owl-carousel', CHERRY_PLUGIN_URL . 'lib/js/owl-carousel/owl.carousel.css', false, '1.24', 'all' );
			wp_register_style( 'owl-theme', CHERRY_PLUGIN_URL . 'lib/js/owl-carousel/owl.theme.css', false, '1.24', 'all' );
			wp_enqueue_style( 'flexslider' );
			wp_enqueue_style( 'owl-carousel', CHERRY_PLUGIN_URL . 'lib/js/owl-carousel/owl.carousel.css', false, '1.24', 'all' );
			wp_enqueue_style( 'owl-theme', CHERRY_PLUGIN_URL . 'lib/js/owl-carousel/owl.theme.css', false, '1.24', 'all' );
			wp_enqueue_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css', false, '3.2.1', 'all' );

			if ( is_rtl() ) {
				wp_enqueue_style( 'cherry-plugin', CHERRY_PLUGIN_URL . 'includes/css/cherry-plugin-rtl.css', false, CHERRY_PLUGIN_VERSION, 'all' );
			} else {
				wp_enqueue_style( 'cherry-plugin', CHERRY_PLUGIN_URL . 'includes/css/cherry-plugin.css', false, CHERRY_PLUGIN_VERSION, 'all' );
			}
		}
		add_action( 'wp_enqueue_scripts', 'cherry_include_stylesheet', 9);
	}

//include script files
	if ( !function_exists('cherry_include_script') ) {
		function cherry_include_script(){
			wp_deregister_script( 'flexslider' );
			wp_register_script( 'flexslider', CHERRY_PLUGIN_URL . 'lib/js/FlexSlider/jquery.flexslider-min.js', array('jquery'), '2.2.2', true );
			wp_enqueue_script( 'flexslider' );

			wp_deregister_script( 'easing' );
			wp_register_script( 'easing', CHERRY_PLUGIN_URL . 'lib/js/jquery.easing.1.3.js', array('jquery'), '1.3' );
			wp_enqueue_script( 'easing' );

			wp_deregister_script( 'elastislide' );
			wp_register_script( 'elastislide', CHERRY_PLUGIN_URL . 'lib/js/elasti-carousel/jquery.elastislide.js', array('jquery', 'easing'), CHERRY_PLUGIN_VERSION );
			wp_enqueue_script( 'elastislide' );

			wp_register_script( 'googlemapapis', '//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false', array('jquery'), false, false );
			wp_enqueue_script( 'googlemapapis' );

			wp_enqueue_script( 'cherry-plugin', CHERRY_PLUGIN_URL . 'includes/js/cherry-plugin.js', array('jquery'), CHERRY_PLUGIN_VERSION, true );

			/**
			 * Filters a custom variations of a OWL-carousel items.
			 *
			 * @since 1.2.6
			 * @param array $items_custom
			 */
			$items_custom = apply_filters( 'cherry_plugin_owl_items_custom', array(
					array( 0, 1 ),
					array( 480, 2 ),
					array( 768, 3 ),
					array( 980, 4 ),
					array( 1170, 5 ),
				) );
			wp_localize_script( 'cherry-plugin', 'items_custom', $items_custom );
		}
		add_action( 'wp_enqueue_scripts', 'cherry_include_script', 9 );
	}