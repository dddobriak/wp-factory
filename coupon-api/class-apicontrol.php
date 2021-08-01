<?php

class ApiControl {
	private $api = API_ARRAY;
	private $post;

	public function __construct() {
		add_action( 'wp_ajax_apiControl', [ $this, 'ajax_jequest' ] );
	}

	public function get_api_list() {
		return $this->api;
	}

	/**
	 * Get option with disabled api array
	 *
	 * @return array
	 */
	public function get_disabled_api(): array {
		$disabled_api = get_option( 'disabled_api' );

		return $disabled_api ? json_decode( $disabled_api, true ) : [];
	}

	/**
	 * Set option with disabled api array
	 */
	public function set_disabled_api() {
		$unchecked_list = $this->get_disabled_api();

		if ( filter_var( $this->post['api_checked'], FILTER_VALIDATE_BOOLEAN ) ) {
			// If api_checked is true - remove api from disabled list
			$unchecked_list = array_diff( $unchecked_list, [ $this->post['api_name'] ] );
			update_option( 'disabled_api', wp_json_encode( $unchecked_list ) );
		} else {
			// Or save disabled api_name
			$unchecked_list[] = $this->post['api_name'];
			update_option( 'disabled_api', wp_json_encode( $unchecked_list ) );
		}
	}

	/**
	 * Active api array
	 *
	 * @return array
	 */
	public function active_api(): array {
		// Through the difference between api list and disabled api list
		return array_diff( $this->get_api_list(), $this->get_disabled_api() );
	}

	/*
	 * Show checked attribute for checkboxes
	 */
	public function get_checked( $name ) {
		if ( ! array_intersect( $this->get_disabled_api(), [ $name ] ) ) {
			echo esc_html( 'checked' );
		}
	}

	public function ajax_jequest() {
		global $cron;
		check_ajax_referer( 'apiLoader-nonce', 'nonce_code' );
		$this->post = $_POST;
		$cron->delete_schedule();
		$this->set_disabled_api();
		$cron->create_schedule();
		wp_die();
	}
}
