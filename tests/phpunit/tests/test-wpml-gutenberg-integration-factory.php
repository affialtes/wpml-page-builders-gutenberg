<?php

/**
 * Class Test_WPML_Gutenberg_Integration_Factory
 *
 * @group page-builders
 * @group gutenberg
 */
class Test_WPML_Gutenberg_Integration_Factory extends OTGS_TestCase {

	/**
	 * @test
	 */
	public function it_creates() {
		global $sitepress;

		$sitepress = \Mockery::mock( 'SitePress' );
		\Mockery::mock( 'WPML_ST_String_Factory' );

		$factory = new WPML_Gutenberg_Integration_Factory();

		$this->assertInstanceOf( 'WPML_Gutenberg_Integration', $factory->create() );

		unset( $sitepress );
	}
}
