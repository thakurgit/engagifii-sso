function showClientSecret() {
    let input = document.getElementById("ClientSecret");
    let icon = document.getElementById("showClientSecret");
    let isHidden = input.type === "password";

    input.type = isHidden ? "text" : "password";
    icon.className = isHidden ? "dashicons dashicons-hidden" : "dashicons dashicons-visibility";
}

jQuery(document).ready(function($) {
    if (!$('.set_sso_logo').length || typeof wp === 'undefined' || !wp.media) return;
    $(document).on('click', '.set_sso_logo', function(e) {
        e.preventDefault(); 
        var $button = $(this), $id = $button.siblings('input'), $img = $button.siblings('img'), $remove = $button.siblings('.remove_sso_logo');
        var mediaUploader = wp.media({
            title: 'Select or Upload SSO Logo',
            library: { type: 'image' },
            button: { text: 'Select' },
            multiple: false
        });
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $id.val(attachment.id);
            $img.attr('src', attachment.url);
            $remove.removeClass('hidden');
        });
        mediaUploader.open();
    });
    $(document).on('click', '.remove_sso_logo', function(e) {
        e.preventDefault(); 
        var $button = $(this);
        $button.siblings('input').val('');
        $button.siblings('img').attr('src', '');
        $button.addClass('hidden');
    });
});
    const ssoBtn = document.getElementById("sso-test-btn");
	 if (ssoBtn) {
		ssoBtn.addEventListener("click", function (event) {
			event.preventDefault(); // Prevents page reload
		
			const ajaxUrl = "/wp-admin/admin-ajax.php?action=engagifii_sso_login"; // Update this if needed
		
			fetch(ajaxUrl)
				.then(response => response.json())
				.then(data => {
					if (data.success && data.data.url) {
						window.open(data.data.url, "_blank", "width=600,height=600");
					} else {
						alert(data.data.message || "Unknown error");
					}
				})
				.catch(error => console.error("Error:", error));
		});
	 }

