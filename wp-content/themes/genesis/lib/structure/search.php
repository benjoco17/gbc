<?php
/**
 * Genesis Framework.
 *
 * WARNING: This file is part of the core Genesis Framework. DO NOT edit this file under any circumstances.
 * Please do all modifications in the form of a child theme.
 *
 * @package Genesis\Search
 * @author  StudioPress
 * @license GPL-2.0+
 * @link    http://my.studiopress.com/themes/genesis/
 */

add_filter( 'get_search_form', 'genesis_search_form' );
/**
 * Replace the default search form with a Genesis-specific form.
 *
 * The exact output depends on whether the child theme supports HTML5 or not.
 *
 * Applies the `genesis_search_text`, `genesis_search_button_text`, `genesis_search_form_label` and
 * `genesis_search_form` filters.
 *
 * @since 0.2.0
 *
 * @return string HTML markup for search form.
 */
function genesis_search_form() {
	$search_text = get_search_query() ? apply_filters( 'the_search_query', get_search_query() ) : apply_filters( 'genesis_search_text', __( 'Search this website', 'genesis' ) . ' &#x02026;' );

	$button_text = apply_filters( 'genesis_search_button_text', esc_attr__( 'Search', 'genesis' ) );

	$onfocus = "if ('" . esc_js( $search_text ) . "' === this.value) {this.value = '';}";
	$onblur  = "if ('' === this.value) {this.value = '" . esc_js( $search_text ) . "';}";

	// Empty label, by default. Filterable.
	$label = apply_filters( 'genesis_search_form_label', '' );

	$value_or_placeholder = ( get_search_query() == '' ) ? 'placeholder' : 'value';

	if ( genesis_html5() ) {

		$form  = sprintf( '<form %s>', genesis_attr( 'search-form' ) );

		if ( genesis_a11y( 'search-form' ) ) {

			if ( '' == $label )  {
				$label = apply_filters( 'genesis_search_text', __( 'Search this website', 'genesis' ) );
			}

			$form_id = uniqid( 'searchform-', true );

			$form .= sprintf(
				'<meta itemprop="target" content="%s"/><label class="search-form-label screen-reader-text" for="%s">%s</label><input itemprop="query-input" type="search" name="s" id="%s" %s="%s" /><input type="submit" value="%s" /></form>',
				home_url( '/?s={s}' ),
				esc_attr( $form_id ),
				esc_html( $label ),
				esc_attr( $form_id ),
				$value_or_placeholder,
				esc_attr( $search_text ),
				esc_attr( $button_text )
			);

		} else {

			$form .= sprintf(
				'%s<meta itemprop="target" content="%s"/><input itemprop="query-input" type="search" name="s" %s="%s" /><input type="submit" value="%s"  /></form>',
				esc_html( $label ),
				home_url( '/?s={s}' ),
				$value_or_placeholder,
				esc_attr( $search_text ),
				esc_attr( $button_text )
			);
		}


	} else {

		$form = sprintf(
			'<form method="get" class="searchform search-form" action="%s" role="search" >%s<input type="text" value="%s" name="s" class="s search-input" onfocus="%s" onblur="%s" /><input type="submit" class="searchsubmit search-submit" value="%s" /></form>',
			home_url( '/' ),
			esc_html( $label ),
			esc_attr( $search_text ),
			esc_attr( $onfocus ),
			esc_attr( $onblur ),
			esc_attr( $button_text )
		);

	}

	return apply_filters( 'genesis_search_form', $form, $search_text, $button_text, $label );

}
