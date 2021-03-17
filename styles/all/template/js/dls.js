$(function() {  // Avoid conflicts with other libraries

'use strict';

$('.tabs .ztab').click(function() {
	var tab_id = $(this).attr('data-tab');

	$('.tabs .ztab').removeClass('current');
	$('.tab-content').removeClass('current');

	$(this).addClass('current');
	$('#'+tab_id).addClass('current');
});

}); // Avoid conflicts with other libraries
