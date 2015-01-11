{% extends "layouts/nonsecure-base.volt" %}

{% block main %}

<div class="row">
    
    <div class="col-xs-12 col-sm-12 col-md-5 col-lg-4" style="margin: 0 auto; float: none;">
        <div class="well no-padding">
            <form method="post" action="/forgot-password" id="forgot-form" class="smart-form client-form">
                <header>
                    Forgot Your Password?
                </header>

                <fieldset>
                    
                    {# Flash Message #}
                    <?php $this->flashSession->output(); ?>
                    
                    <section>
                        <label class="label" style="word-break: break-all; white-space: normal;"><strong>No problem.</strong><br> We will email you your reset instructions.</label>
                        <label class="input"> <i class="icon-append fa fa-user"></i>
                            <input type="email" name="email" placeholder="Email Address">
                            <b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> Please enter email address</b></label>
                    </section>

                </fieldset>
                <footer>
                    <a href="/signin" class="btn btn-default">Cancel</a>
                    <button type="submit" class="btn btn-primary">Send Instructions</button>
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
            $("#forgot-form").validate({
                // Rules for form validation
                rules : {
                    email : {
                        required : true,
                        email : true
                    }
                },

                // Messages for form validation
                messages : {
                    email : {
                        required : 'Please enter your email address.',
                        email : 'Please enter a VALID email address.'
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
