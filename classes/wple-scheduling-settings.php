<?php

class WPLE_Scheduling_Settings {

	static public function init() {

		// add meta box
		add_action( 'wple_before_advanced_settings', 	array( __CLASS__, 'add_custom_meta_box' 	) );

		// save settings
		add_action( 'wple_save_settings', 				array( __CLASS__, 'save_settings' 			) );

	}

	static public function add_custom_meta_box() {

		$data = array(
			'scheduling_auto_increment'     => get_option( 'wple_scheduling_auto_increment', '' ),
			'scheduling_max_increment'      => get_option( 'wple_scheduling_max_increment',  60 ),
		);

		self::display( 'settings_advanced', $data );
	}


	static public function save_settings() {
		// echo "<pre>";print_r($_POST);echo"</pre>";die();

		if ( ! isset($_POST['wple_scheduling_max_increment']) ) return;

		update_option( 'wple_scheduling_auto_increment', absint( $_POST['wple_scheduling_auto_increment'] ) );
		update_option( 'wple_scheduling_max_increment',	 absint( $_POST['wple_scheduling_max_increment']  ) );

	}


	// display view
	static function display( $insView, $inaData = array(), $echo = true ) {
		$sFile = dirname(__FILE__).'/../views/' . $insView . '.php';
		
		if ( !is_file( $sFile ) ) {
			echo "View not found: ".$sFile;
			return false;
		}
		
		if ( count( $inaData ) > 0 ) {
			extract( $inaData, EXTR_PREFIX_ALL, 'wpl' );
		}
		
		ob_start();
			include( $sFile );
			$sContents = ob_get_contents();
		ob_end_clean();

		// filter content before output
		$sContents = apply_filters( 'wplister_admin_page_content', $sContents );

		if ($echo) {
			echo $sContents;
			return true;
		} else {
			return $sContents;
		}
	
	}

} // class WPLE_Scheduling_Settings
