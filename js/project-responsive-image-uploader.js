// indago-v2/js/project-responsive-image-uploader.js

jQuery(document).ready(function ($) {

    // Common function to handle media uploader for a single image field
    function handleImageSelection(fieldPrefix) {
        var mediaUploader;
        var $imageIdField = $('#' + fieldPrefix + '_project_image_id');
        var $imagePreview = $('#' + fieldPrefix + '_image_preview');
        var $removeButton = $('button[data-field="' + fieldPrefix + '"].remove-image-button');

        // Event listener for the "Select Image" button
        $('button[data-field="' + fieldPrefix + '"].select-image-button').on('click', function (e) {
            e.preventDefault();

            // If the uploader already exists, open it again
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            // Create the WordPress media uploader frame
            mediaUploader = wp.media({
                title: 'Select ' + (fieldPrefix === 'mobile' ? 'Mobile' : 'Desktop') + ' Project Image',
                button: {
                    text: 'Select Image'
                },
                multiple: false // Only allow selection of a single image
            });

            // When a file is selected, grab the URL and ID
            mediaUploader.on('select', function () {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $imageIdField.val(attachment.id); // Store the image ID

                // Update the preview
                var imageUrl = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                $imagePreview.attr('src', imageUrl).removeClass('empty').text(''); // Set image source and clear empty text
                $removeButton.show(); // Show the remove button
            });

            // Open the uploader
            mediaUploader.open();
        });

        // Event listener for the "Remove Image" button
        $removeButton.on('click', function (e) {
            e.preventDefault();
            $imageIdField.val(''); // Clear the image ID
            $imagePreview.attr('src', '').addClass('empty').text('No Image Selected'); // Reset preview
            $(this).hide(); // Hide the remove button
        });
    }

    // Initialize handlers for both fields
    handleImageSelection('mobile');
    handleImageSelection('desktop');

});