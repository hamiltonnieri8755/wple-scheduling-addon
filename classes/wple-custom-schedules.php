<?php

class WPLE_Custom_Schedules {

	static public function init() {

		// add meta fields
		add_action( 'wple_after_basic_ebay_options', 		array( __CLASS__, 'add_custom_meta_fields' 	) );
		// add_action( 'wple_after_advanced_ebay_options', 	array( __CLASS__, 'add_custom_meta_fields' 	) );

		// save meta fields
		add_action( 'woocommerce_process_product_meta', 	array( __CLASS__, 'save_product_meta' 		), 10, 2 );

		// modify listing
		add_action( 'wple_filter_listing_item', 			array( __CLASS__, 'filter_listing_item' 	), 10, 5 );
		//add_action( 'wple_filter_listing_item', 			array( __CLASS__, 'handle_auto_increment' 	), 20, 5 );

		// add custom action to listings table's bulk action select box


	}

	static public function add_custom_meta_fields() {
		global $post;

		// check if stored value is valid date/time
		$ebay_schedule_time = get_post_meta( $post->ID, '_ebay_schedule_time', true );
		if ( DateTime::createFromFormat('Y-m-d H:i:s', $ebay_schedule_time ) ) {

			// convert date/time from UTC to local timezone
			$tz = self::getLocalTimeZone();
			$dt = new DateTime( $ebay_schedule_time, new DateTimeZone( 'UTC' ) );
			$dt->setTimeZone( new DateTimeZone( $tz ) );
			$ebay_schedule_time = $dt->format('Y-m-d H:i');

		} else {
			$ebay_schedule_time = '';
		}

		woocommerce_wp_text_input( array(
			'id' 			=> 'wpl_ebay_schedule_time',
			'label' 		=> __('Schedule time', 'wple-scheduling-addon'),
			// 'placeholder' 	=> __('Leave empty to use profile settings', 'wple-scheduling-addon'),
			'placeholder' 	=> __('Format', 'wple-scheduling-addon') . ': ' . self::getCurrentLocalTime('Y-m-d H:i') . ' or ' . self::getCurrentLocalTime('H:i'),
			// 'description' 	=> __('If you want a custom schedule time to be used, enter it here.','wple-scheduling-addon'),
			'description' 	=> __('Current time', 'wple-scheduling-addon') . ': ' . self::getCurrentLocalTime('Y-m-d H:i T'),
			'value'			=> $ebay_schedule_time
		) );

	} // add_custom_meta_fields()

	static public function save_product_meta( $post_id, $post ) {

		// schedule time has to be Y-m-d H:i
		$ebay_schedule_time = isset( $_POST['wpl_ebay_schedule_time'] ) ? esc_attr( $_POST['wpl_ebay_schedule_time'] ) : '';
		if ( DateTime::createFromFormat('Y-m-d H:i', $ebay_schedule_time ) ) {

			// convert date/time from local timezone to UTC
			$tz = self::getLocalTimeZone();
			$dt = new DateTime( $ebay_schedule_time, new DateTimeZone( $tz ) );
			$dt->setTimeZone( new DateTimeZone( 'UTC' ) );
			$ebay_schedule_time = $dt->format('Y-m-d H:i:s');

		// or H:i
		} elseif ( DateTime::createFromFormat('H:i', $ebay_schedule_time ) ) {

			// convert date/time from local timezone to UTC
			$tz = self::getLocalTimeZone();
			$dt = new DateTime( $ebay_schedule_time, new DateTimeZone( $tz ) );
			$dt->setTimeZone( new DateTimeZone( 'UTC' ) );
			$ebay_schedule_time = $dt->format('Y-m-d H:i:s');

		} else {
			$ebay_schedule_time = '';
		}


		if ( $ebay_schedule_time ) {
			update_post_meta( $post_id, '_ebay_schedule_time', $ebay_schedule_time );
		} else {
			delete_post_meta( $post_id, '_ebay_schedule_time');
		}

	} // save_product_meta()


	// modify listing item - add custom schedule on AddItem / RelistItem requests
	static public function filter_listing_item( $item, $listing, $profile_details, $post_id, $reviseItem ) {

		// do nothing if this is a ReviseItem request
		if ( $reviseItem ) return $item;

		// check if stored value is valid date/time
		$ebay_schedule_time = get_post_meta( $post_id, '_ebay_schedule_time', true );
		if ( ! DateTime::createFromFormat('Y-m-d H:i:s', $ebay_schedule_time ) ) return $item;

		// check if scheduled time has passed already
		if ( self::dateHasPassed( $ebay_schedule_time ) ) return $item;

		// set ScheduleTime - format: 2015-07-27T12:00:00.000Z
		$dt = new DateTime( $ebay_schedule_time, new DateTimeZone( 'UTC' ) );
		$ScheduleTime = $dt->format('Y-m-d\TH:i:s') . '.000Z';
		$item->setScheduleTime( $ScheduleTime );
		WPLE()->logger->info( 'ADDON: Listing was scheduled at ' . $ScheduleTime );

		return $item;
	} // filter_listing_item()


	// handle auto increment options
	/*static public function handle_auto_increment( $item, $listing, $profile_details, $post_id, $reviseItem ) {

		// do nothing if this is a ReviseItem request
		if ( $reviseItem ) return $item;

		// get options
		$scheduling_auto_increment = get_option( 'wple_scheduling_auto_increment', '' );
		$scheduling_max_increment  = get_option( 'wple_scheduling_max_increment',  60 );
		$scheduling_last_increment = get_option( 'wple_scheduling_last_increment',  0 );

		// do nothing if disabled
		if ( ! $scheduling_auto_increment ) return $item;

		// get ScheduleTime
		$ScheduleTime = $item->getScheduleTime();
		if ( ! $ScheduleTime ) return $item;

		// parse ScheduleTime
		$ScheduleTime = str_replace( '.000Z', '', $ScheduleTime );
		$dt = DateTime::createFromFormat('Y-m-d\TH:i:s', $ScheduleTime );

		// add minutes to ScheduleTime
		$dt->add( new DateInterval( 'PT' . $scheduling_last_increment . 'M') );
		$ScheduleTime = $dt->format('Y-m-d\TH:i:s') . '.000Z';
		$item->setScheduleTime( $ScheduleTime );

		// update last increment counter
		if ( $scheduling_last_increment >= $scheduling_max_increment ) $scheduling_last_increment = 0;
		update_option( 'wple_scheduling_last_increment', $scheduling_last_increment + $scheduling_auto_increment );

		return $item;
	}*/ // handle_auto_increment()

	// bulk schedule option callback
	static public function bulk_schedule( $id ) {

		// get options
		$scheduling_auto_increment = get_option( 'wple_scheduling_auto_increment', '' );
		$scheduling_max_increment  = get_option( 'wple_scheduling_max_increment',  60 );
		$scheduling_last_increment = 0;
		
		//
		if ( ! $scheduling_auto_increment ) return;
		
		$ebay_schedule_time = isset( $_POST['bulkschedule_startdate'] ) ? esc_attr( $_POST['bulkschedule_startdate'] ) : '';
		
		if ( is_array( $id ) ) {

			foreach ( $id as $single_id ) {

				if ( DateTime::createFromFormat('Y-m-d H:i', $ebay_schedule_time ) ) {

					// convert date/time from local timezone to UTC
					$tz = self::getLocalTimeZone();
					$dt = new DateTime( $ebay_schedule_time, new DateTimeZone( $tz ) );
					$dt->setTimeZone( new DateTimeZone( 'UTC' ) );

					$dt->add( new DateInterval( 'PT' . $scheduling_last_increment . 'M') );
					$ebay_schedule_time = $dt->format('Y-m-d H:i:s');
					
					if ( $scheduling_last_increment >= $scheduling_max_increment ) $scheduling_last_increment = 0;
					$scheduling_last_increment += $scheduling_auto_increment;

				} else {
					$ebay_schedule_time = '';
				}

				if ( $ebay_schedule_time ) {
					update_post_meta( $single_id, '_ebay_schedule_time', $ebay_schedule_time );
				} else {
					delete_post_meta( $single_id, '_ebay_schedule_time');
				}

			}
		} else {
			if ( DateTime::createFromFormat('Y-m-d H:i', $ebay_schedule_time ) ) {

				// convert date/time from local timezone to UTC
				$tz = self::getLocalTimeZone();
				$dt = new DateTime( $ebay_schedule_time, new DateTimeZone( $tz ) );
				$dt->setTimeZone( new DateTimeZone( 'UTC' ) );

				$ebay_schedule_time = $dt->format('Y-m-d H:i:s');
				
			} else {
				$ebay_schedule_time = '';
			}

			if ( $ebay_schedule_time ) {
				update_post_meta( $id, '_ebay_schedule_time', $ebay_schedule_time );
			} else {
				delete_post_meta( $id, '_ebay_schedule_time');
			}
		}

	} // bulk_schedule()

    static public function getLocalTimeZone() {

        // get the local timezone from WP
        $tz = get_option('timezone_string');
        if ( ! $tz ) $tz = wc_timezone_string(); // 'Europe/London'

        return $tz;
    }

    static public function getCurrentLocalTime( $format = 'H:i' ) {

        // create the DateTimeZone object using local timezone from WP
        $dtime = new DateTime( 'now', new DateTimeZone( self::getLocalTimeZone() ) );

        // return the time using the preferred format
        $time = $dtime->format( $format );

        return $time;
    }

    // check if a parseable date is in the past (assuming it timezone is UTC)
    static public function dateHasPassed( $date ) {

		// get GMT timestamp of schedule time
		$scheduled_datetime_gmt = gmdate('U', strtotime( $date ) );
		$current_datetime_gmt   = gmdate('U', time() );

		// check if scheduled time has already passed
		if ( $scheduled_datetime_gmt < $current_datetime_gmt ) return true;

		return false;
    }


} // class WPLE_Custom_Schedules
