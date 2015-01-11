<!DOCTYPE html>
<html lang="en-us">
    <head>
        <meta charset="utf-8">
        <!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->

        <title>
            {% block title %}{{ appTitle }}
                {% if pageTitle is defined %}
                    &bull; {{ pageTitle }}
                {% endif %}
            {% endblock %}
        </title>
        <meta name="apple-mobile-web-app-title" content="{{ appTitle }}">
        <meta name="description" content="">
        <meta name="author" content="">

        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        {% block head %}
            {# css includes #}
            {% if env == 'PROD' %}
                {{ stylesheet_link("/css/prod/phalcon-boilerplate.min.css") }}
            {% else %}
                {{ stylesheet_link("/css/template/bootstrap.min.css") }}
                {{ stylesheet_link("/css/template/font-awesome.min.css") }}
                {{ stylesheet_link("/css/template/smartadmin-production.min.css") }}
                {{ stylesheet_link("/css/app/app-main.css") }}
            {% endif %}
        {% endblock %}

        <!-- FAVICONS -->
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
        <link rel="icon" href="/favicon.ico" type="image/x-icon">

        <!-- GOOGLE FONT -->
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

    </head>
    <body id="mainBody" class="smart-style-3">

        {# Header #}
        {% include "layouts/header.volt" %}

        {# Left navigation #}
        {% include "layouts/navigation.volt" %}

        <!-- MAIN PANEL -->
        <div id="main" role="main">
            
            <!-- RIBBON -->
            <div id="ribbon">

                <!-- breadcrumb -->
                <ol class="breadcrumb">
                    {% block breadcrumb %}{% endblock %}
                </ol>
                <!-- end breadcrumb -->

            </div>
            <!-- END RIBBON -->

            <!-- MAIN CONTENT -->
            <div id="content">

                <div class="row">
                    <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                        <h1 class="page-title txt-color-blueDark">{{ pageTitleIcon }} {{ pageTitle }} </h1>
                    </div>
                </div>
                
                {# Flash Message #}
                <?php $this->flashSession->output(); ?>
                
                {% block main %}{% endblock %}

            </div>
            <!-- END MAIN CONTENT -->

        </div>
        <!-- END MAIN PANEL -->

        {# Shortcut #}
        {% include "layouts/shortcut.volt" %}

        {# loading icon placholder #}
        <div id="loading" style="display:none;"></div>

        {# global js includes #}
        {% if env == 'PROD' %}
            {{ javascript_include("/js/prod/phalcon-boilerplate.min.js") }}
        {% else %}
            {{ javascript_include("/js/libs/jquery-2.1.0.min.js") }}
            {{ javascript_include("/js/libs/bootstrap.min.js") }}
            {{ javascript_include("/js/libs/jarvis.widget.min.js") }}
            {{ javascript_include("/js/libs/SmartNotification.min.js") }}
            {{ javascript_include("/js/template/app.js") }}
            {{ javascript_include("/js/app/app-main.js") }}
        {% endif %}

        {% block jsfooter %}{% endblock %}

        <script>
            $(document).ready(function() {

                // DO NOT REMOVE : GLOBAL FUNCTIONS!
                pageSetUp();
            });
        </script>

    </body>
</html>