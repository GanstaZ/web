$(function() {  // Avoid conflicts with other libraries

'use strict';

$('dl.tabs dd.ztab').click(function() {
	var tab_id = $(this).attr('data-tab');

	$('dl.tabs dd.ztab').removeClass('current');
	$('.tab-content').removeClass('current');

	$(this).addClass('current');
	$('#'+tab_id).addClass('current');
})

}); // Avoid conflicts with other libraries
