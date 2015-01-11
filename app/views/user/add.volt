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
            <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
            <h2>Create new user</h2>
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

                <form id="add-form" class="smart-form" action="/user/add/" method="post">
                    <fieldset>
                        <section>
                            <label class="label">Type <span class="txt-color-red">*</span></label>
                            <label class="select">
                                <select name="group_id">
                                    <option value="">Choose type</option>
                                    {% for group in groups %}
                                        <option value="{{ group.id }}" {% if groupId == group.id %}selected="selected"{% endif %}>{{ group.label }}</option>
                                    {% endfor %}
                                </select> <i></i> </label>
                        </section>
                        <section>
                            <label class="label">First Name <span class="txt-color-red">*</span></label>
                            <label class="input">
                                <input type="text" name="first_name" value="{{ firstName }}">
                            </label>
                        </section>
                        <section>
                            <label class="label">Last Name <span class="txt-color-red">*</span></label>
                            <label class="input">
                                <input type="text" name="last_name" value="{{ lastName }}">
                            </label>
                        </section>
                        <section>
                            <label class="label">Email <span class="txt-color-red">*</span></label>
                            <label class="input">
                                <input type="email" name="username" value="{{ username }}" autocorrect="off" autocapitalize="off" autocomplete="off">
                            </label>
                        </section>
                        <section>
                            <label class="label">Password <span class="txt-color-red">*</span></label>
                            <label class="input">
                                <input id="new_password" type="password" name="new_password" value="{{ newPassword }}" autocorrect="off" autocapitalize="off" autocomplete="off">
                            </label>
                        </section>
                        <section>
                            <label class="label">Confirm Password <span class="txt-color-red">*</span></label>
                            <label class="input">
                                <input id="confirm_new_password" type="password" name="confirm_new_password" value="{{ confirmPassword }}">
                            </label>
                        </section>
                        <section>
                            <label class="toggle">
                                <input type="hidden" name="status" value="off">
                                <input type="checkbox" name="status" {% if status == 'on' %}checked="checked"{% endif %}>
                                <i data-swchon-text="ON" data-swchoff-text="OFF"></i>Status
                            </label>
                        </section>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="javascript:mainRedirect('/user');">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i>
                            Save
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
                    group_id : {
                        required : true
                    },
                    first_name : {
                        required : true
                    },
                    last_name : {
                        required : true
                    },
                    username : {
                        required : true,
                        email: true
                    },
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
                    group_id : {
                        required : 'Please select user type.'
                    },
                    first_name : {
                        required : 'Please enter your first name.'
                    },
                    last_name : {
                        required : 'Please enter your last name.'
                    },
                    username : {
                        required : 'Please enter your email address.',
                        email : 'Please enter a VALID email address.'
                    },
                    new_password : {
                        required : 'Please choose a password.'
                    },
                    confirm_new_password : {
                        required : 'Please re-enter password.',
                        equalTo : 'Please enter the same value as the password.',
                    },
                },

                // Do not change code below
                errorPlacement : function(error, element) {
                    error.insertAfter(element.parent());
                }
            });
        });
    </script>
{% endblock %}
