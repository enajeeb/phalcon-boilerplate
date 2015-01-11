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
                {{ stylesheet_link("/css/prod/phalcon-boilerplate.login.min.css") }}
            {% else %}
                {{ stylesheet_link("/css/template/bootstrap.min.css") }}
                {{ stylesheet_link("/css/template/font-awesome.min.css") }}
                {{ stylesheet_link("/css/template/smartadmin-production.min.css") }}
            {% endif %}
        {% endblock %}

        <!-- FAVICONS -->
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
        <link rel="icon" href="/favicon.ico" type="image/x-icon">

        <!-- GOOGLE FONT -->
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

    </head>
    <body id="login" class="animated fadeInDown">

        <header id="header">
            <div id="logo-group">
                <span id="logo" style="font-size: 20px; width: 300px;">{{ appTitle }}</span>
            </div>
        </header>

        <div id="main" role="main">
            
            <div id="content" class="container">
                {% block main %}{% endblock %}
            </div>

        </div>

        {# global js includes #}
        {% if env == 'PROD' %}
            {{ javascript_include("/js/prod/phalcon-boilerplate.login.min.js") }}
        {% else %}
            {{ javascript_include("/js/libs/jquery-2.1.0.min.js") }}
            {{ javascript_include("/js/libs/bootstrap.min.js") }}
            {{ javascript_include("/js/template/app.js") }}
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