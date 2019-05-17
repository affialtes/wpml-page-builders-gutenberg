<?php

class WPML_Gutenberg_Integration_Factory {

	public function create() {
		/**
		 * @var SitePress $sitepress
		 * @var wpdb      $wpdb
		 */
		global $sitepress, $wpdb;

		$config_option = new WPML_Gutenberg_Config_Option();

		$string_parsers = [
			new WPML\PB\Gutenberg\StringsInBlock\HTML( $config_option ),
			new WPML\PB\Gutenberg\StringsInBlock\Attributes( $config_option ),
		];

		$strings_in_block     = new WPML\PB\Gutenberg\StringsInBlock\Compose( $string_parsers );
		$string_factory       = new WPML_ST_String_Factory( $wpdb );
		$strings_registration = new WPML_Gutenberg_Strings_Registration(
			$strings_in_block,
			$string_factory,
			new WPML_PB_Reuse_Translations( $string_factory ),
			new WPML_PB_String_Translation( $wpdb )
		);

		return new WPML_Gutenberg_Integration(
			$strings_in_block,
			$config_option,
			$sitepress,
			$strings_registration
		);
	}
}
