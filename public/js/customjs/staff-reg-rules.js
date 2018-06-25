$(document).ready(function(){
    jQuery.validator.setDefaults({
        success: "valid",
        rules:
            {
                mobile_number:
                    {
                        required : true,
                        minlength: 10,
                        maxlength: 15,
                        digits: true
                    },
                firstname:
                    {
                        minlength: 2
                    },

                lastname:
                    {
                        minlength: 2
                    }
            },
        messages:
            {
                mobile_number:
                    {
                        minlength: "Mobile number must be between 10 to 15 digits",
                        digits: "Only digits allowed"
                    },
                firstname:
                    {
                        minlength: "A minimum of 2 characters is required"
                    },

                lastname:
                    {
                        minlength: "A minimum of 2 characters is required"
                    }
            }

    });
});

