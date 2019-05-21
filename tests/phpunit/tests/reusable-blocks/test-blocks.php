<?php

namespace WPML\PB\Gutenberg\ReusableBlocks;

/**
 * @group reusable-blocks
 */
class TestBlocks extends \OTGS_TestCase {

	/**
	 * @test
	 * @dataProvider dp_invalid_reusable_blocks
	 * @group wpmlcore-6565
	 *
	 * @param array $block
	 */
	public function it_should_return_false_if_NOT_reusable_block( $block ) {
		$this->assertFalse( Blocks::isReusable( $block ) );

	}

	public static function dp_invalid_reusable_blocks() {
		return [
			'not reusable block' => [
				[
					'blockName' => 'not-wp/block',
					'attrs'     => [ 'ref' => 987 ],
				],
			],
			'not numerical ref' => [
				[
					'blockName' => 'core/block',
					'attrs'     => [ 'ref' => 'something' ],
				],
			],
			'no ref' => [
				[
					'blockName' => 'core/block',
					'attrs'     => [ 'foo' => 'bar' ],
				],
			],
			'no attrs' => [
				[
					'blockName' => 'core/block',
				],
			],
		];
	}

	/**
	 * @test
	 * @group wpmlcore-6565
	 */
	public function it_should_return_true_if_is_reusable_block() {
		$block = [
			'blockName' => 'core/block',
			'attrs'     => [ 'ref' => 987 ],
		];

		$this->assertTrue( Blocks::isReusable( $block ) );
	}

	/**
	* @test
	* @group wpmlcore-6580
	*/
	public function it_should_get_ids_from_post() {
		$GLOBALS['wp_version'] = '5.1.0';
		$post_id  = 123;
		$block_id = 456;
		$post = $this->getMockBuilder( 'WP_Post' )
		             ->disableOriginalConstructor()->getMock();
		$post->post_content = 'some block content';
		$blocks = [
			// Valid reusable block
			[
				'blockName' => 'core/block',
				'attrs'     => [ 'ref' => (string) $block_id ],
			],
			// Not wp block
			[
				'blockName' => 'not-wp/block',
				'attrs'     => [ 'ref' => 987 ],
			],
			// Not numerical ref
			[
				'blockName' => 'core/block',
				'attrs'     => [ 'ref' => 'something' ],
			],
			// No "ref"
			[
				'blockName' => 'core/block',
				'attrs'     => [ 'foo' => 'bar' ],
			],
			// No "attrs"
			[
				'blockName' => 'core/block',
			],
		];
		\WP_Mock::userFunction( 'get_post', [
			'args'   => [ $post_id ],
			'return' => $post,
		]);
		\WP_Mock::userFunction( 'parse_blocks', [
			'args'   => [ $post->post_content ],
			'return' => $blocks,
		]);
		
		// Second stack
		$block = $this->getMockBuilder( 'WP_Post' )->disableOriginalConstructor()->getMock();
		$block->post_content = 'some content';

		\WP_Mock::userFunction( 'get_post', [
			'args'   => [ $block_id ],
			'return' => $block,
		]);
		\WP_Mock::userFunction( 'parse_blocks', [
			'args'   => [ $block->post_content ],
			'return' => [],
		]);
		
		$subject = new Blocks();
		$this->assertEquals(
			[ $block_id ],
			$subject->getChildrenIdsFromPost( $post_id )
		);
		unset( $GLOBALS['wp_version'] );
	}



	/**
	 * @test
	 * @group wpmlcore-6598
	 */
	public function it_should_get_nested_block_ids_from_post() {
		$GLOBALS['wp_version'] = '5.1.0';

		$post_id        = 123;
		$block_id       = 456;
		$child_block_id = 789;

		// First stack
		$main_post = $this->getMockBuilder( 'WP_Post' )->disableOriginalConstructor()->getMock();
		$main_post->post_content = 'some block content';

		$blocks_in_first_stack = [
			[
				'blockName' => 'core/block',
				'attrs'     => [ 'ref' => (string) $block_id ],
			],
		];
		\WP_Mock::userFunction( 'get_post', [
			'args'   => [ $post_id ],
			'return' => $main_post,
		]);
		\WP_Mock::userFunction( 'parse_blocks', [
			'args'   => [ $main_post->post_content ],
			'return' => $blocks_in_first_stack,
		]);

		// Second stack
		$block_post = $this->getMockBuilder( 'WP_Post' )->disableOriginalConstructor()->getMock();
		$block_post->post_content = 'some child block content';

		$blocks_in_second_stack = [
			[
				'blockName' => 'core/block',
				'attrs'     => [ 'ref' => (string) $child_block_id ],
			],
		];
		\WP_Mock::userFunction( 'get_post', [
			'args'   => [ $block_id ],
			'return' => $block_post,
		]);
		\WP_Mock::userFunction( 'parse_blocks', [
			'args'   => [ $block_post->post_content ],
			'return' => $blocks_in_second_stack,
		]);

		// Last stack, returns an empty array of blocks
		$child_block_post = $this->getMockBuilder( 'WP_Post' )->disableOriginalConstructor()->getMock();
		$child_block_post->post_content = 'some content with no reusable block';

		\WP_Mock::userFunction( 'get_post', [
			'args'   => [ $child_block_id ],
			'return' => $child_block_post,
		]);
		\WP_Mock::userFunction( 'parse_blocks', [
			'args'   => [ $child_block_post->post_content ],
			'return' => [],
		]);

		$subject = new Blocks();
		$this->assertEquals(
			[ $block_id, $child_block_id ],
			$subject->getChildrenIdsFromPost( $post_id )
		);
		unset( $GLOBALS['wp_version'] );
	}

	/**
	 * @test
	 * @group wpmlcore-6565
	 */
	public function it_should_return_an_empty_array_if_post_does_not_exist() {
		$post_id  = 123;

		\WP_Mock::userFunction( 'get_post', [
			'args'   => [ $post_id ],
			'return' => null,
		]);

		$subject = new Blocks();

		$this->assertEmpty( $subject->getChildrenIdsFromPost( $post_id ) );
	}
}
