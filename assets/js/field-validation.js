(function($) {

    $(document).on('acf/validate_field', function (e, field) {

        // vars
        $field = $(field);

        if ($field.find('select#countryCode').val() === '0') {
            $field.data('validation', false);
        }
        if ($field.find('select#univCode').val() === '-') {
            $field.data('validation', false);
        }
        if ($field.find('select#facultyName').val() === 'Select a faculty') {
            $field.data('validation', false);
        }

    });
})(jQuery);
