{% extends "layouts/base.volt" %}

{% block head %}

    {# render the contents of the parent block #}
    {{ super() }}

{% endblock %}

{% block breadcrumb %}
    {% for breadcrumb in pageBreadcrumbs %}
        <li>{{ breadcrumb }}</li>
    {% endfor %}
{% endblock %}

{% block main %}
    
    <div class="jarviswidget" id="wid-id-1" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-custombutton="false">
        <header>
            <span class="widget-icon"> <i class="fa fa-key"></i> </span>
            <h2>Change user's password</h2>
        </header>
                
        <!-- widget div-->
        <div>
            <!-- widget edit box -->
            <div class="jarviswidget-editbox">
                <!-- This area used as dropdown edit box -->
            </div>
            <!-- end widget edit box -->

            <!-- widget content -->
            <div class="widget-body no-padding">

                <form id="add-form" class="smart-form" action="/user/resetPassword/{{ id }}" method="post">
                    <fieldset>
                        <section>
                            <label class="label">New Password</label>
                            <label class="input">
                                <input id="new_password" type="password" name="new_password">
                            </label>
                        </section>
                        <section>
                            <label class="label">Confirm New Password</label>
                            <label class="input">
                                <input id="confirm_new_password" type="password" name="confirm_new_password">
                            </label>
                        </section>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="javascript:mainRedirect('/user');">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i>
                            Change password
                        </button>
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
            $("#add-form").validate({
                // Rules for form validation
                rules : {
                    new_password : {
                        required : true,
                        minlength : 8,
                        maxlength : 20
                    },
                    confirm_new_password: {
                        required : true,
                        minlength : 8,
                        maxlength : 20,
                        equalTo: "#new_password"
                    }
                },

                // Messages for form validation
                messages : {
                    new_password : {
                        required : 'Please choose a new password.'
                    },
                    confirm_new_password : {
                        required : 'Please re-enter new password.',
                        equalTo : 'Please enter the same value as the new password.',
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
