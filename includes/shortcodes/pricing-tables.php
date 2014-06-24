<?php
/**
 * Pricing Tables
 *
 */
if ( !function_exists('chp_pricing_table_shortcode') ) {
	function chp_pricing_table_shortcode( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(array(
			'columns'      => '',
			'labelled'     => '',
			'custom_class' => ''
		), $atts));

		$output = '<div class="price-plans price-plans-' . $columns . ' ' . $custom_class . '">' . do_shortcode( $content ) . '</div>';

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;

	}
	add_shortcode('chp_pricing_table', 'chp_pricing_table_shortcode');
}

if ( !function_exists('chp_pricing_column_shortcode') ) {
	function chp_pricing_column_shortcode( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(array(
			'title'            => 'Column title',
			'highlight'        => 'false',
			'highlight_reason' => '',
			'price'            => "99",
			'currency_symbol'  => '$',
			'interval'         => 'Sign Up'
		), $atts));

		$highlight_class        = null;
		$hightlight_reason_html = null;

		if ( $highlight == 'true' ) {
			$highlight_class        = 'highlight';
			$hightlight_reason_html = '<span class="highlight-reason">' . $highlight_reason . '</span>';
		}

		$output = "<div class='plan $highlight_class'>
				<h3>$title $hightlight_reason_html</h3>
				<h4><span class='currency_symbol'>$currency_symbol</span>$price <span class='interval'>$interval</span></h4>
				<div class='plan-container'>" . do_shortcode($content) . "</div>
			</div>";

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('chp_pricing_column', 'chp_pricing_column_shortcode');
}

if ( !function_exists('chp_pricing_row_shortcode') ) {
	function chp_pricing_row_shortcode( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(array(
			'odd' => ''
		), $atts));

		if ( $odd == 'true' ) {
			$odd = ' odd';
		} else {
			$odd = '';
		}

		$output = "<div class='plan-features-row" . $odd . "'>" . do_shortcode($content) . "</div>";

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('chp_pricing_row', 'chp_pricing_row_shortcode');
}

if ( !function_exists('chp_pricing_column_label_shortcode') ) {
	function chp_pricing_column_label_shortcode( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(array(
			'title' => 'Features'
		), $atts));

		$output = "<div class='plan plan-labelled'><h4>$title</h4>" . do_shortcode($content) . "</div>";

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('chp_pricing_column_label', 'chp_pricing_column_label_shortcode');
}

if ( !function_exists('chp_pricing_row_label_shortcode') ) {
	function chp_pricing_row_label_shortcode( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(array(
			'odd' => ''
		), $atts));

		if ( $odd == 'true' ) {
			$odd = ' odd';
		} else {
			$odd = '';
		}

		$output = "<div class='plan-labelled-row $odd'>" . do_shortcode($content) . "</div>";

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('chp_pricing_row_label', 'chp_pricing_row_label_shortcode');
} ?>