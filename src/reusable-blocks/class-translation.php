<?php

namespace WPML\PB\Gutenberg\ReusableBlocks;

class Translation {

	const POST_TYPE = 'wp_block';

	/** @var \SitePress $sitepress */
	private $sitepress;

	/**
	 * @param \SitePress $sitepress
	 */
	public function __construct( \SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	/**
	 * @param array        $block
	 * @param null|string  $lang
	 *
	 * @return array
	 */
	public function convertBlock( array $block, $lang = null ) {
		if ( Blocks::isReusable( $block ) ) {
			$block['attrs']['ref'] = $this->convertBlockId( $block['attrs']['ref'], $lang );
		}

		return $block;
	}

	/**
	 * @param int         $block_id
	 * @param string|null $lang
	 *
	 * @return
	 */
	public function convertBlockId( $block_id, $lang = null ) {
		return $this->sitepress->get_object_id( $block_id, self::POST_TYPE, true, $lang );
	}
}