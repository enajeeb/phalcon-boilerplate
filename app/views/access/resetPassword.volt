{% extends "layouts/nonsecure-base.volt" %}

{% block main %}

<div class="row">

    <div class="col-xs-12 col-sm-12 col-md-5 col-lg-4" style="margin: 0 auto; float: none;">
        <div class="well no-padding">
            <form method="post" action="/reset-password/{{ resetHashToken }}" id="reset-form" class="smart-form client-form">
                <header>
                    Change your password
                </header>

                <fieldset>
                    
                    {# Flash Message #}
                    <?php $this->flashSession->output(); ?>
                    
                    <section>
                        <label class="input">
                            <i class="icon-append fa fa-lock"></i>
                            <input id="new_password" type="password" name="new_password" placeholder="New Password">
                            <b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> Passwords must be at least 8 characters.</b>
                        </label>
                    </section>
                    <section>
                        <label class="input">
                            <input id="confirm_password" type="password" name="confirm_password" placeholder="Confirm New Password">
                        </label>
                    </section>

                </fieldset>
                <footer>
                    <a href="/signin" class="btn btn-default">Cancel</a>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </footer>
            </form>
        </div>
        
    </div>
</div>
{% endblock %}

{% block jsfooter %}
    {{ javascript_include("/js/libs/jquery.validate.min.js") }}
    <script type="text/javascript">
        runAllForms();

        $(function() {
            // Validation
            $("#reset-form").validate({
                // Rules for form validation
                rules : {
                    new_password : {
                        required : true,
                        minlength : 8,
                        maxlength : 20
                    },
                    confirm_password: {
                        required : true,
                        minlength : 8,
                        maxlength : 20,
                        equalTo: "#new_password"
                    }
                },

                // Messages for form validation
                messages : {
                    email : {
                        required : 'Please enter your email address.',
                        email : 'Please enter a VALID email address.'
                    },
                    password : {
                        required : 'Please enter your password'
                    }
                },

                // Do not change code below
                errorPlacement : function(error, element) {
                    error.insertAfter(element.parent());
                }
            });
        });
    </script>
{% endblock %}
