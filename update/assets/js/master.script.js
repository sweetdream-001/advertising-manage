jQuery(document).ready(function($) {
	$('[data-section-block="script-update"]').find('button[data-section-node="update"]').on('click', function(event) {
		event.preventDefault();
		if ($(this).data('status') == 'updating') {
			alert('Please wait for the update process to complete!');
			return false;
		}
		else {
			$(this).text('Updating, Please wait....');
			$(this).data('status','updating');
			$(this).parents().find('form[data-section-node="submit-form"]').trigger('submit');
		}
	});
});