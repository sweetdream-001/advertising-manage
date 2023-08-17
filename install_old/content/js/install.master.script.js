jQuery(document).ready(function($) {
	"use strict";

	$('[data-onchange]').on('change', function(event) {
		var this_el    = $(this);
		var event_trig = this_el.data('onchange');

		if (event_trig = 'license_agreement') {
			var btn = $("div#license-agreements-page").find('button#continue');
			if (this_el.prop('checked')  == true) {
				btn.removeAttr('disabled');
			}

			else {
				btn.attr('disabled',true);
			}
		}
	});

	$("[data-anchor]").on('click', function(event) {
		var this_el     = $(this);
		window.location = (location.origin + location.pathname + this_el.data('anchor'));
	});

	$("form#install-site").on('submit', function(event) {
		$(this).find('button[type="submit"]').addClass('d-none');
		$(this).find('button[type="button"]').removeClass('d-none');
	});
});