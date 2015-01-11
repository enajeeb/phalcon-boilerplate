<!-- Left panel : Navigation area -->
<!-- Note: This width of the aside area can be adjusted through LESS variables -->
<aside id="left-panel">

    <!-- User info -->
    <div class="login-info">
        <span>
            
            <a href="javascript:void(0);" id="show-shortcut">
                <img src="/img/template/avatar.jpg" alt="me" class="online" /> 
                <span>
                    {{ sessionUserName }}
                </span>
                <i class="fa fa-angle-down"></i>
            </a> 
            
        </span>
    </div>
    <!-- end user info -->

    <nav>
        <ul>
            <li {% if selLeftNav == "Dashboard" %} class="active" {% endif %}>
                <a href="/" title="Dashboard"><i class="fa fa-lg fa-fw fa-home"></i> <span class="menu-item-parent">Dashboard</span></a>
            </li>
            
            <li {% if selLeftNav == "country" %} class="active" {% endif %}>
                <a href="/country" title="Countries"><i class="fa fa-lg fa-fw fa-flag"></i> <span class="menu-item-parent">Countries</span></a>
            </li>

            {% if sessionUserRole == 'admin' %}
                <li {% if selLeftNav == "user" %} class="active" {% endif %}>
                    <a href="/user" title="Users"><i class="fa fa-lg fa-fw fa-user"></i> <span class="menu-item-parent">Users</span></a>
                </li>
            {% endif %}
        </ul>
    </nav>
    <span class="minifyme"> <i class="fa fa-arrow-circle-left hit"></i> </span>

</aside>
<!-- END NAVIGATION -->