/** 
* Application global javascript
*/

"use strict";

$(document).ready(function () {
    
});


/**
* Delete Action
*/
function mainSmartAdminDelete( obj ) {

    $.SmartMessageBox({
        title   : "Delete",
        content : "Are you sure?",
        buttons : '[No][Yes]'
    }, function(ButtonPressed) {
        if (ButtonPressed === "Yes") {
            mainRedirect($(obj).attr("data-link"));
        }
        if (ButtonPressed === "No") {
            // do nothing
        }
    });
    return false;
    
}

/**
* Redirect
*/
function mainRedirect( link ) {
    window.location = link;
}