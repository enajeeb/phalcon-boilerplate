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
            <h2>Create new country record</h2>
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

                <form id="add-form" class="smart-form" action="/country/add/" method="post">
                    <fieldset>
                        <section>
                            <label class="label">Name <span class="txt-color-red">*</span></label>
                            <label class="input">
                                <input type="text" name="name" value="{{ name }}">
                            </label>
                        </section>
                        <section>
                            <label class="label">Abbreviation <span class="txt-color-red">*</span></label>
                            <label class="input">
                                <input type="text" name="abbreviation" value="{{ abbreviation }}">
                            </label>
                        </section>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="javascript:mainRedirect('/country');">
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
                    name : {
                        required : true
                    },
                    abbreviation : {
                        required : true
                    }
                },

                // Messages for form validation
                messages : {
                    name : {
                        required : 'Please enter a name.'
                    },
                    abbreviation : {
                        required : 'Please enter an abbreviation.'
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
