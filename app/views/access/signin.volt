{% extends "layouts/nonsecure-base.volt" %}

{% block main %}

<div class="row">
    
    <div class="col-xs-12 col-sm-12 col-md-5 col-lg-4" style="margin: 0 auto; float: none;">
        <div class="well no-padding">
            <form method="post" action="/signin" id="login-form" class="smart-form client-form">
                <header>
                    Sign In
                </header>

                <fieldset>
                    
                    {# Flash Message #}
                    <?php $this->flashSession->output(); ?>
                    
                    <section>
                        <label class="label">E-mail</label>
                        <label class="input"> <i class="icon-append fa fa-user"></i>
                            <input type="email" name="email">
                            <b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> Please enter email address</b></label>
                    </section>

                    <section>
                        <label class="label">Password</label>
                        <label class="input"> <i class="icon-append fa fa-lock"></i>
                            <input type="password" name="password">
                            <b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> Enter your password</b> </label>
                        <div class="note">
                            <a href="/forgot-password">Forgot password?</a>
                        </div>
                    </section>

                </fieldset>
                <footer>
                    <button type="submit" class="btn btn-primary">Sign in</button>
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
            $("#login-form").validate({
                // Rules for form validation
                rules : {
                    email : {
                        required : true,
                        email : true
                    },
                    password : {
                        required : true,
                        minlength : 8,
                        maxlength : 20
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
