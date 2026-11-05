/**
 * Closes or hides a modal
 * @param {string} id The html id of the modal.
 */
function close_modal(id) {
    $('#'+id).modal('hide');
}

function dental_show_flash(id, html, autoHide = false, autoHideAfter = 3000){
    $('#'+id).children('span').html(html);
    $('#'+id).removeClass('fade');
    $('#'+id).removeClass('hide');
    if(autoHide){
        setTimeout('dental_hide_flash("'+id+'")', autoHideAfter);   
    }
}

function dental_hide_flash(id){
    id = '#'+id;
    $(id).addClass('fade');
    setTimeout("$('"+id+"').addClass('hide')", 100);
}

/**
 * Display a dialog window with information.
 * @param {string} body_html - The text that will be displayed.
 * @param {string} title - The title for the alert. [Default Alert]
 * @param {string} command - The command type. OK, YesNo, OKCancel. [Default INFO]
 * @param {string} type - The alert type. INFO or WARNING. [Default INFO]
 * @param {string} autoHide - Will set a timer to close the dialog automaticaly after 5000 mil sec. [Default false]
 */
function dental_show_alert(body_html = "Message", command = "OK", alert_type = 'INFO', title = "Alert", autoHide = false){
    if(alert_type == 'INFO'){
        $('#dentalAlertModal').find('.fa-info-circle').css("display", "block");
        $('#dentalAlertModal').find('.fa-warning').css("display", "none");
    }
    else{
        $('#dentalAlertModal').find('.fa-info-circle').css("display", "none");
        $('#dentalAlertModal').find('.fa-warning').css("display", "block");
    }
    
    if(command == "OK"){
        $('#dentalAlertModal').find('#dentalAlertButtonPrimary').html("OK");
        $('#dentalAlertModal').find('#dentalAlertButtonPrimary').css("display", "inline-block");
        $('#dentalAlertModal').find('#dentalAlertButtonSecondary').css("display", "none");
    }
    else if(command == "OKCancel"){
        $('#dentalAlertModal').find('#dentalAlertButtonPrimary').html("Yes");
        $('#dentalAlertModal').find('#dentalAlertButtonSecondary').html("Cancel");
        $('#dentalAlertModal').find('#dentalAlertButtonPrimary').css("display", "inline-block");
        $('#dentalAlertModal').find('#dentalAlertButtonSecondary').css("display", "inline-block");
    }
    else{
        $('#dentalAlertModal').find('#dentalAlertButtonPrimary').html("Yes");
        $('#dentalAlertModal').find('#dentalAlertButtonSecondary').html("No");
        $('#dentalAlertModal').find('#dentalAlertButtonPrimary').css("display", "inline-block");
        $('#dentalAlertModal').find('#dentalAlertButtonSecondary').css("display", "inline-block");
    }

    $('#dentalAlertModal').find('#dentalAlertBody').html(body_html);
    $('#dentalAlertModal').find('#dentalAlertTitle').html(title);
    $('#dentalAlertModal').modal('show');
    if(autoHide)
        setTimeout("$('#dentalAlertModal').modal('hide')", 5000);
}

/**
 * Got this from stackoverflow
 * @param {*} strs 
 * @returns 
 */
var substringMatcher = function(strs){
    return function findMatches(q, cb) {
        var matches, substringRegex;

        // an array that will be populated with substring matches
        matches = [];
    
        // regex used to determine if a string contains the substring `q`
        substrRegex = new RegExp(q, 'i');
    
        // iterate through the pool of strings and for any string that
        // contains the substring `q`, add it to the `matches` array
        $.each(strs, function(i, str) {
            if (substrRegex.test(str)) {
            matches.push(str);
            }
        });
    
        cb(matches);
    };
};