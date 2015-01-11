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
<section id="widget-grid">
    <div class="jarviswidget" id="wid-id-0" 
        data-widget-colorbutton="false" 
        data-widget-editbutton="false"
        data-widget-togglebutton="false"
        data-widget-deletebutton="false"
        data-widget-custombutton="false"
        data-widget-collapsed="false" 
        data-widget-sortable="false">

        <header>
            <span class="widget-icon"> <i class="fa fa-table"></i> </span>
            <h2>List of System Users &nbsp;<a href="/user/add" title="Add Another"><i class="fa fa-plus"></i></a></h2>
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
                
                <div class="widget-body-toolbar bg-filter-box">
                    <form id="logs-form-toolbar" action="/user/" method="get">
                    <div class="dataTables_length" style="top: 4px;">
                        <span class="smart-form">
                            <label class="select" style="width:60px">
                                <select size="1" name="limit" aria-controls="dt_basic" onchange="javascript: this.form.submit();">
                                    <option value="10" {% if itemsPerPage == "10" %}selected="selected"{% endif %}>10</option>
                                    <option value="25" {% if itemsPerPage == "25" %}selected="selected"{% endif %}>25</option>
                                    <option value="50" {% if itemsPerPage == "50" %}selected="selected"{% endif %}>50</option>
                                    <option value="100" {% if itemsPerPage == "100" %}selected="selected"{% endif %}>100</option>
                                </select>
                            <i></i></label>
                        </span>
                    </div>
                    <div class="dataTables_filter" style="top: 4px;">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                            <input name="filter" class="form-control" placeholder="Filter" type="text" aria-controls="dt_basic" value="<?php echo (!empty($filter))? $filter : '' ?>">
                        </div>
                    </div>
                    </form>
                </div>
                <div class="dataTables_wrapper">
                    <table id="sort-table" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <?php

                                    // calculate sorting
                                    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                                    $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
                                    parse_str($query, $queryString);
                                    unset($queryString['sort']);
                                    unset($queryString['direction']);
                                    $queryString = http_build_query($queryString);

                                    $concatChar = ( empty($queryString) )? '?' : '?' . $queryString;
                                    $directionReverse = ( $direction == 'asc' )? 'desc' : 'asc';
                                    $sName = $sType = $sEmail = $sDate = "sorting";
                                    $sNameLink = $sTypeLink = $sEmailLink = $sDateLink = "";

                                    if ( !empty($sort) ) {
                                        
                                        switch ($sort) {
                                            case 'name':
                                                $sName = "sorting_" . $direction;
                                                $sNameLink = $path . $concatChar . '&sort=name&direction=' . $directionReverse;
                                                $sTypeLink = $path . $concatChar . '&sort=type&direction=asc';
                                                $sEmailLink = $path . $concatChar . '&sort=email&direction=asc';
                                                $sDateLink = $path . $concatChar . '&sort=date&direction=asc';
                                            break;
                                            case 'type':
                                                $sType = "sorting_" . $direction;
                                                $sNameLink = $path . $concatChar . '&sort=name&direction=asc';
                                                $sTypeLink = $path . $concatChar . '&sort=type&direction=' . $directionReverse;
                                                $sEmailLink = $path . $concatChar . '&sort=email&direction=asc';
                                                $sDateLink = $path . $concatChar . '&sort=date&direction=asc';
                                            break;
                                            case 'email':
                                                $sEmail = "sorting_" . $direction;
                                                $sNameLink = $path . $concatChar . '&sort=name&direction=asc';
                                                $sTypeLink = $path . $concatChar . '&sort=type&direction=asc';
                                                $sEmailLink = $path . $concatChar . '&sort=email&direction=' . $directionReverse;
                                                $sDateLink = $path . $concatChar . '&sort=date&direction=asc';
                                            break;
                                            case 'date':
                                                $sDate = "sorting_" . $direction;
                                                $sNameLink = $path . $concatChar . '&sort=name&direction=asc';
                                                $sTypeLink = $path . $concatChar . '&sort=type&direction=asc';
                                                $sEmailLink = $path . $concatChar . '&sort=email&direction=asc';
                                                $sDateLink = $path . $concatChar . '&sort=date&direction=' . $directionReverse;
                                            break;
                                        }
                                    } else {
                                        $sNameLink = $path . $concatChar . '&sort=name&direction=asc';
                                        $sTypeLink = $path . $concatChar . '&sort=type&direction=asc';
                                        $sEmailLink = $path . $concatChar . '&sort=email&direction=asc';
                                        $sDateLink = $path . $concatChar . '&sort=date&direction=asc';
                                    }

                                    echo <<<EOT
                                <th width="1%">&nbsp;</th>
                                <th class="{$sName}" role="columnheader" data-link="{$sNameLink}">Name</th>
                                <th class="{$sType}" role="columnheader" data-link="{$sTypeLink}" width="15%">Type</th>
                                <th class="{$sEmail} hidden-small-screen" role="columnheader" data-link="{$sEmailLink}" width="25%">Email</th>
                                <th class="{$sDate} hidden-small-screen" role="columnheader" data-link="{$sDateLink}" width="18%">Last Modified</th>
                                <th width="1%">&nbsp;</th>
EOT;
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($page->items as $item) { ?>
                            <tr>
                                <td>
                                    {% if item.status == 'active' %}
                                        <i rel="tooltip" data-placement="right" data-original-title="Active" class="fa fa-lg fa-circle txt-color-greenLight"></i>
                                    {% else %}
                                        <i rel="tooltip" data-placement="right" data-original-title="Inactive" class="fa fa-lg fa-circle txt-color-red"></i>
                                    {% endif %}
                                </td>
                                <td class="ellipsis-small-screen"><?php echo $item->first_name . ' ' . $item->last_name; ?></td>
                                <td><?php echo $groups[$item->group_id]; ?></td>
                                <td class="hidden-small-screen"><?php echo $item->username; ?></td>
                                <td class="hidden-small-screen"><?php echo $item->modified; ?></td>
                                <td>
                                    {% include "layouts/dropdown" with ['links': dropdownLinks, 'id': item.id ] %}
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                
                {# Shortcut #}
                {% include "layouts/pagination.volt" %}

            </div><!-- end widget content -->
        </div><!-- end widget div -->
    </div><!-- end widget -->

</section>
{% endblock %}

{% block jsfooter %}
{% endblock %}