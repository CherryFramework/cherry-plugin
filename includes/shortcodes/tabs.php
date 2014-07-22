<?php
/**
 * Tabs
 */
if ( !function_exists('tabs_shortcode') ) {
	function tabs_shortcode( $atts, $content = null, $shortcodename = '' ) {
		if ( !array_key_exists('direction', $atts) ) {
			$direct = array(
				'direction' => 'top'
				);
			$atts = array_merge( $direct, $atts );
		}
		$output = '<div class="tabs-wrapper tabbable tabs-' . $atts["direction"] . '">';

			// Build tab menu
			$nav_tabs = '<ul class="nav nav-tabs">';
				$id       = rand(); // Create unique ID for this tab set
				$tab_menu = array();
				$tab_menu = $atts;
				array_shift( $tab_menu );
				$num_tabs = count( $tab_menu );

				for ( $i = 1; $i <= $num_tabs; $i++ ) {
					$addclass = ($i == 1) ? 'active tab-' . $i : 'tab-' . $i ;
					$nav_tabs .= '<li class="' . $addclass . '"><a href="#tab-' . $i . '-' . $id . '" data-toggle="tab">' . $tab_menu['tab' . $i] . '</a></li>';
				}
			$nav_tabs .= '</ul>';

			// Build content of tabs
			$tab_content = '<div class="tab-content">';
				$i          = 1;
				$tabContent = do_shortcode( $content );
				$find       = array();
				$replace    = array();

				foreach ( $tab_menu as $key => $value ) {
					$addclass  = ($i == 1) ? 'in active' : '' ;
					$find[]    = '[' . $key . ']';
					$find[]    = '[/' . $key . ']';
					$replace[] = '<div id="tab-' . $i . '-' . $id . '" class="tab-pane fade ' . $addclass . '">';
					$replace[] = '</div><!-- .tab (end) -->';
					$i++;
				}
				$tabContent = str_replace( $find, $replace, $tabContent );
				$tab_content .= $tabContent;
			$tab_content .= '</div><!-- .tab-content (end) -->';

			if ( $atts['direction'] == 'below' ) {
				$output .= $tab_content . $nav_tabs;
			} else {
				$output .= $nav_tabs . $tab_content;
			}

		$output .= '</div><!-- .tabs-wrapper (end) -->';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode( 'tabs', 'tabs_shortcode' );
} ?>