jQuery(document).ready(function($) {
  $('.toggle-display').on('change', function() {
    var termId = $(this).data('term-id');
    var nonce = toggleDisplay.nonce;

    $.ajax({
      url: toggleDisplay.ajax_url,
      type: 'POST',
      data: {
        action: 'toggle_display',
        term_id: termId,
        nonce: nonce
      },
      success: function(response) {
        if (response.success) {
          console.log('Display status updated.');
        } else {
          console.log('Failed to update display status.');
        }
      }
    });
  });
});
