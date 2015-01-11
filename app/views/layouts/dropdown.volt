{% if not (links is empty) %}
    <div class="btn-group display-inline pull-right text-align-left">
        <button class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-cog fa-lg"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-xs pull-right">
            {% for name, link in links %}
                {% if name == "edit" %}
                    <li>
                        <a href="{{ link }}/{{ id }}"><i class="fa fa-edit fa-lg fa-fw txt-color-greenLight"></i> Edit</a>
                    </li>
                {% elseif name == "delete" %}
                    <li>
                        <a href="javascript:void(0);" data-link="{{ link }}/{{ id }}" onclick="mainSmartAdminDelete(this); return false;"><i class="fa fa-times fa-lg fa-fw txt-color-red"></i> Delete</a>
                    </li>
                {% endif %}
            {% endfor %}
            <li class="divider"></li>
            <li class="text-align-center">
                <a href="javascript:void(0);">Cancel</a>
            </li>
        </ul>
    </div>
{% endif %}
