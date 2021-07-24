<?php
/**
 * @author  HeyMehedi
 * @since   1.0
 * @version 1.0
 */

namespace HeyMehedi\Content_Restriction;

use HeyMehedi\Content_Restriction\Settings;

class Hooks {

	protected static $instance = null;
	private $settings;

	public function __construct() {
		$this->settings = Settings::get();

		add_filter( 'the_title', array( $this, 'the_title' ), 10, 2 );
		add_filter( 'the_content', array( $this, 'the_content' ) );

		// add_filter( 'get_the_excerpt', array( $this, 'filter_the_excerpt' ), 11, 2 );
	}

	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function the_title( $title, $id ) {
		$modified_title = 'HHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHH';

		return $this->show_content( $title, $id, $modified_title );
	}

	public function the_content( $the_content ) {
		return $this->show_content( $the_content, get_the_ID(), $this->settings['the_content'] );
	}

	private function show_content( $content, $id, $modified_content ) {

		if ( $this->users_can_see() ) {
			return $content;
		}

		if ( 'category' === $this->settings['restriction_wise'] ) {

			if ( has_category( $this->settings['category_ids'], $id ) ) {
				return $modified_content;
			}

		}

		if ( 'single_post' === $this->settings['restriction_wise'] ) {

			if ( in_array( $id, $this->settings['single_post_ids'] ) ) {
				return $modified_content;
			}

		}

		return $content;
	}

	private function users_can_see() {

		if ( is_blog_admin() ) {
			return true;
		}

		$current_user = wp_get_current_user();

		if ( in_array( 'not_logged_in', $this->settings['role_names'] ) && ! is_user_logged_in() ) {
			return true;
		}

		foreach ( $this->settings['role_names'] as $role ) {

			if ( in_array( $role, $current_user->roles ) ) {
				return true;
			}

		}

		return false;
	}
}

Hooks::instance();