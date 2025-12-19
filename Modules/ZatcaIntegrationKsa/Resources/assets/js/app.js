$(document).ready(function() {
    // ZATCA address validation - simple and standard
    var addressFields = ['street_name', 'building_number', 'city', 'zip_code', 'additional_number', 'state', 'country'];
    
    function updateValidation() {
        var hasTax = $('#tax_number').val().trim() !== '';
        
        addressFields.forEach(function(field) {
            var input = $('#' + field);
            if (input.length) {
                if (hasTax) {
                    input.attr('required', true);
                } else {
                    input.removeAttr('required');
                }
            }
        });
    }
    
    // Apply on modal show
    $(document).on('shown.bs.modal', '.contact_modal', function() {
        updateValidation();
    });
});
