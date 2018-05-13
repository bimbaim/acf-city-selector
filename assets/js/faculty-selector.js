// JS File for Country Field
(function($) {

    jQuery(document).ready(function() {

        var country = $("select[name*='countryCode']");
        var univ = $("select[name*='univCode']");

        if (country.length) {
            country.on('change', function () {

                var $this = $(this);

                get_univ($(this).val(), function (response) {
                    var obj = JSON.parse(response);
                    var len = obj.length;
                    var $univValues = '';

                    $("select[name*='univCode']").empty();
                    $("select[name*='facultyName']").empty();
                    for (i = 0; i < len; i++) {
                        var myuniv = obj[i];

                        $univValues += '<option value="' + myuniv.country_code + '-' + myuniv.univ_code + '">' + myuniv.univ_name + '</option>';

                    }
                    $("select[name*='univCode']").append($univValues);

                });
            });
        }

        if (univ.length) {

            univ.on('change', function () {

                var $this = $(this);

                get_faculty($(this).val(), function (response) {

                    var obj = JSON.parse(response);
                    var len = obj.length;
                    var $facultyValues = '';

                    $("select[name*='facultyName']").empty();
                    for (i = 0; i < len; i++) {
                        var myfaculty = obj[i];
                        $facultyValues += '<option value="' + myfaculty.faculty_name + '">' + myfaculty.faculty_name + '</option>';
                    }
                    $("select[name*='facultyName']").append($facultyValues);

                });
            });
        }

        function get_univ(countryCODE, callback) {

            var data = {
                action: 'get_univ_call',
                country_code: countryCODE
            };

            $.post(ajaxurl, data, function (response) {
                callback(response);
            });
        }

        function get_faculty(univCODE, callback) {

            var data = {
                action: 'get_faculty_call',
                row_code: univCODE
            };

            $.post(ajaxurl, data, function (response) {
                callback(response);
            });
        }

        // Load select univ when editing a post
        function admin_post_edit_load_univ() {
            get_univ(faculty_selector_vars.countryCode, function (response) {

                var stored_univ = faculty_selector_vars.univCode;
                var obj          = JSON.parse(response);
                var len          = obj.length;
                var $univValues = '';

                $("select[name*='univCode']").fadeIn();
                for (i = 0; i < len; i++) {
                    var myuniv = obj[i];
                    var current_univ = myuniv.country_code + '-' + myuniv.univ_code;
                    if (current_univ == stored_univ) {
                        var selected = ' selected="selected"';
                    } else {
                        var selected = false;
                    }
                    $univValues += '<option value="' + myuniv.country_code + '-' + myuniv.univ_code + '"' + selected + '>' + myuniv.univ_name + '</option>';

                }
                $("select[name*='univCode']").append($univValues);

            });
        }

        // Load select faculty when editing a post
        function admin_post_edit_load_faculty() {
            // $("select[name*='facultyName']").hide();
            get_faculty(faculty_selector_vars.univCode, function (response) {

                var stored_faculty = faculty_selector_vars.facultyName;
                var obj         = JSON.parse(response);
                var len         = obj.length;
                var $facultyValues = '';

                $("select[name*='facultyName']").fadeIn();
                for (i = 0; i < len; i++) {
                    var myfaculty = obj[i];
                    if (myfaculty.faculty_name == stored_faculty) {
                        var selected = ' selected="selected"';
                    } else {
                        var selected = false;
                    }
                    $facultyValues += '<option value="' + myfaculty.faculty_name + '"' + selected + '>' + myfaculty.faculty_name + '</option>';
                }
                $("select[name*='facultyName']").append($facultyValues);

            });
        }

        if (typeof faculty_selector_vars !== "undefined") {
            admin_post_edit_load_univ();
            admin_post_edit_load_faculty();
        }
    });

})(jQuery);

