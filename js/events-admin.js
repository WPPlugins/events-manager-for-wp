jQuery(function($){
	'use strict';
	$('.em4wp-events-calendar-date').datepicker();
	$('#em4wp-events-calendar-allday').change(function(event) {
		if ( $(this).is(":checked") ) {
			$('#em4wp-events-calendar-start-time').val('12:01AM').hide();
			$('#em4wp-events-calendar-end-time').val('11:59PM').hide();
		} else {
			$('#em4wp-events-calendar-start-time').val('').show();
			$('#em4wp-events-calendar-end-time').val('').show();
		}
	});
	if ( $('#em4wp-events-calendar-allday').is(":checked") ) {
		$('#em4wp-events-calendar-start-time').hide();
		$('#em4wp-events-calendar-end-time').hide();
	}
});
