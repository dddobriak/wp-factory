<?php

class Cron {
	private $idgu    = [ '02:05' ];
	private $baligam = [ '02:05' ];
	private $bigdeal = [ '02:05' ];
	private $couponi = [ '02:05' ];
	private $groo    = [ '02:05' ];
	private $isrotel = [ '02:05' ];
	private $ksp     = [ '02:05' ];

	public function __construct() {
		global $api_control;
		add_action( 'after_switch_theme', [ $this, 'create_schedule' ] );
		add_action( 'switch_theme', [ $this, 'delete_schedule' ] );
		$this->create_coupon_functions();
	}

	/**
	 * Create schedule
	 */
	public function create_schedule() {
		global $api_control;
		$schedule   = get_object_vars( $this );
		$event_list = [];

		foreach ( $api_control->active_api() as $active_api ) {
			foreach ( $schedule as $name => $timings ) {
				if ( $name === strtolower( $active_api ) ) {
					$event_list[ $name ] = $timings;
				}
			}
		}

		foreach ( $event_list as $api => $time ) {
			$this->create_cron_event( $time, $api );
		}
	}

	/**
	 * Delete schedule
	 */
	public function delete_schedule() {
		$events_array = $this->get_coupon_hooks();

		// delete all found events
		foreach ( $events_array as $event ) {
			wp_unschedule_hook( $event );
		}
	}

	/**
	 * Create cron event
	 *
	 * @param $array
	 * @param $api
	 */
	public function create_cron_event( $array, $api ) {
		$coupon_load = 'coupon_load_' . strtolower( $api );

		// convert array times to object
		$array = array_map(
			function ( $a ) {
				return DateTime::createFromFormat( 'H:i', $a );
			},
			$array
		);

		$current_time = new DateTime( 'now' );

		// set time as event
		foreach ( $array as $time ) {
			if ( $time < $current_time ) {
				wp_schedule_event( $time->modify( dailyd_offset( false ) . ' + 1 day' )->getTimestamp(), 'daily', $coupon_load );
			} else {
				wp_schedule_event( $time->modify( dailyd_offset( false ) )->getTimestamp(), 'daily', $coupon_load );
			}
		}
	}

	/**
	 * Create coupon functions
	 */
	public function create_coupon_functions() {
		$coupon_hooks = $this->get_coupon_hooks();

		// Create action and function in the same loop
		foreach ( $coupon_hooks as $hook ) {
			$api = explode( '_', $hook );
			add_action(
				$hook,
				function () use ( $api ) {
					start_insert( ucfirst( end( $api ) ) );
				}
			);
		}
	}

	/**
	 * Get coupon hooks
	 */
	public function get_coupon_hooks(): array {
		$cron_array   = _get_cron_array();
		$events_array = [];

		foreach ( $cron_array as $events ) {
			foreach ( $events as $key => $event ) {
				if ( is_int( strpos( $key, 'coupon_load_' ) ) ) {
					$events_array[] = $key;
				}
			}
		}

		return array_unique( $events_array );
	}

	/**
	 * Check time intervals
	 *
	 * @param $array
	 * @param $gap
	 *
	 * @return bool
	 */
	public function time_intervals( $array, $gap ): bool {
		// set array with current time and gap
		$array = array_map(
			function ( $a ) use ( $gap ) {
				$result            = [];
				$result['start']   = DateTime::createFromFormat( 'H:i', $a );
				$result['end']     = DateTime::createFromFormat( 'H:i', gmdate( 'H:i', strtotime( "{$a} + {$gap} minutes" ) ) );
				$result['current'] = DateTime::createFromFormat( 'H:i', gmdate( 'H:i' ) );

				return $result;
			},
			$array
		);

		// return true, if the current range is actual
		foreach ( $array as $item ) {
			if ( $item['current'] > $item['start'] && $item['current'] < $item['end'] ) {
				return true;
			}
		}

		return false;
	}
}
