<?php

namespace WPML\PB\Gutenberg;

class Reusable_Blocks {

	/**
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function get_ids( $post_id ) {
		$post = get_post( $post_id );

		if ( $post ) {
			$blocks = \collect( \WPML_Gutenberg_Integration::parse_blocks( $post->post_content ) );

			return $blocks->filter( function( $block ) {
				return 'core/block' === $block['blockName']
				       && isset( $block['attrs']['ref'] )
				       && is_numeric( $block['attrs']['ref'] );
			})->map( function( $block ) {
				return (int) $block['attrs']['ref'];
			})->toArray();
		}

		return [];
	}
}