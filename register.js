

$(document).ready(function() {

    $("#userForm").submit(function (e) {
        removeFeedback();
        var errors = validateForm();
        if (errors == "") {
            return true;
        }
        else {
            provideFeedback(errors);
            e.preventDefault();
            return false;
        }
    });


    function validateForm() {
        var errorFields = new Array();

        //check required fields have something in them
        if ($('#name').val() == "") {
            errorFields.push('name');
        }
        if ($('#email').val() == "") {
            errorFields.push('email');
        }
        if ($('#password1').val() == "") {
            errorFields.push('password1');
        }
        if ($('#student').val() == "" || $('#professor').val() == "") {
            errorFields.push('registrationType');
        }

        //check that passwords match
        if ($('#password2').val() != $('#password1').val()) {
            errorFields.push('password2');
        }

        //very basic email check
        if (!($('#email').val().indexOf(".") > 2) && ($('#email').val().indexOf("@"))) {
            errorFields.push('email');
        }

        return errorFields;
    }

    function provideFeedback(incomingErrors) {
        for (var i = 0; i < incomingErrors.length; i++) {
            $("#" + incomingErrors[i]).addClass("errorClass");
            $("#" + incomingErrors[i] + "Error").removeClass("errorFeedback");
        }
        $("#errorDiv").html("Errors encountered");
    }

    function removeFeedback() {
        $("#errorDiv").html("");
        $('input').each(function () {
            $(this).removeClass("errorClass");
        });
        $('.errorSpan').each(function () {
            $(this).addClass("errorFeedback");
        });
    }

});