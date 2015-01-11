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
            <h2>Update user</h2>
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

                <form id="add-form" class="smart-form" action="/user/edit/{{ id }}" method="post">
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
                                <input type="text" name="username" value="{{ username }}" autocorrect="off" autocapitalize="off" autocomplete="off">
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
