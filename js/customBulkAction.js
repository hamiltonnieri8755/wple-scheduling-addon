jQuery(document).ready(function() {

	// Add Bulk Schedule option to listings action selectbox, hidden input field for increment interval
	jQuery('<option>').val('bulkschedule').text('Bulk Schedule').appendTo("#bulk-action-selector-");
	jQuery('#listings-filter').append('<input type="hidden" name="bulkschedule_startdate"/>');

	// Open Bulk Schedule dialog when click apply with bulk schedule option
	jQuery('#doaction').click( function () {

		if ( jQuery(this).siblings("select[name='action']").val() == 'bulkschedule' ) {
			dialog.dialog( "open" );
			return false;
		}

	});

	// Date time picker init
	jQuery('#datetimepicker').datetimepicker({
		dayOfWeekStart : 1,
		lang : 'en',
        dateFormat: 'yy-mm-dd', 
        timeFormat: 'HH:mm' 
	});
	jQuery('#datetimepicker').datetimepicker({value:get_today(),step:10});

	// Bulk Schedule dialog init
    dialog = jQuery( "#bulk-schedule-form" ).dialog({
      autoOpen: false,
      height: 300,
      width: 300,
      modal: true,
      buttons: {
        "OK": schedule_bulk_listings,
        Cancel: function() {
          dialog.dialog( "close" );
        }
      },
      close: function() {
      }
    });

    // Bulk schedule handler, 
    function schedule_bulk_listings() {
    	jQuery("#bulk-action-selector-").val( 'bulkschedule');
    	jQuery("input[name='bulkschedule_startdate']").val( jQuery('#datetimepicker').val() );
    	jQuery("#listings-filter").submit();
    }

    // Get today
    function get_today() {

	    var today = new Date();
	    var dd = today.getDate();
	    var mm = today.getMonth()+1; //January is 0!

	    var yyyy = today.getFullYear();
	    if( dd < 10 ) {
	        dd = '0' + dd;
	    } 
	    if( mm< 10 ) {
	        mm = '0' + mm;
	    } 
	    var today = yyyy + '/' + mm + '/' + dd;

	    return today;

    }

});