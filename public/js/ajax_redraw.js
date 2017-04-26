var fieldNameArray = new Array();

$(document).ready(function() {

    $("#id_list a").click(function(e) {
        //e.preventDefault();
        id = $(this).attr('id');

        initializeLinks(id, e, data);
    });

});

function variableDefined(name) {
    return typeof this[name] !== 'undefined';
}

function getTargetTab() {
        var url = window.location.search;

        url = url.replace("?", ''); // remove the ?
        var res = url.split("=");
        target_tab = '#' + res[1];
        return target_tab;
}

function initializeLinks(id, e, data) {

    arr = id.split('__');
    field = arr[1];

    //div = div__educationProvider;
    div = "div__" + field;
    divObjString = "#" + div;

    //title__educationProvider
    titleId = "title__" + field;
    titleObjString = "#" + titleId;

    formDivId = "formDiv__" + field;
    formDivObjString = "#" + formDivId;

    formId = "form__" + field;
    formObjString = "#" + formId;

    filter = "filter__" + field;
    filterObjString = "#" + filter;

    if (arr[0] == 'add') {
        e.preventDefault();

        $(titleObjString).html('New ' + fieldNameArray[field]);
        $(divObjString).show('slow');

        redrawForm('add', formDivObjString, field);

    } else if (arr[0] == 'update') {
        e.preventDefault();

        record_id = arr[2];
        $(titleObjString).html('Update ' + fieldNameArray[field]);
        $(divObjString).show('slow');

        redrawForm('update', formDivObjString, field, record_id);

    } else if (arr[0] == 'delete') {

        record_id = arr[1];
        $(titleObjString).html('Delete ' + fieldNameArray[field]);
        $(divObjString).show('slow');
        redrawForm('delete', formDivObjString, field, record_id);

    } else if (arr[0] == 'close') {
        e.preventDefault();

        $(divObjString).hide('slow');
    } else if (arr[0] == 'page') {
        
        // edited by anand
        
        
        e.preventDefault();
        page = arr[2];

        postArray = {
            page: page,
            tab : data.param.tab
        };

        filter = $(filterObjString).val();
        if (filter != "Filter..." && filter != "") {
            listSpecificParams['q'] = filter;
        }

        if (variableDefined('listSpecificParams')) {
            $.each(listSpecificParams, function(key, value) {
                postArray[key] = value;
            });
        }
        
        if (data.param.to_year == false || data.param.to_year == null) {
            to = '';
        } else {
             postArray['to'] = data.param.to_year;
        }
        
        if (data.param.from_year == false || data.param.from_year == null) {
            from = '';
        } else {
              postArray['from'] = data.param.from_year;
        }
        //console.log(tab);
        //console.log(postArray);
        buildDynamicList(field, record_id, postArray);

        q = '';

        if (data.param.q == false || data.param.q == null) {
            q = '';
        } else {
            q = data.param.q;
        }
        
        hashUrl = 'page=' + page + '&q=' + q;

        //if (typeof data.param.tab !='undefined') {
        
        if (!$.isEmptyObject(data.param.tab)) {
            hashUrl += "&tab=" + target_tab;
        }
        
        
        if (data.param.to_year == false || data.param.to_year == null) {
            to = '';
        } else {
            hashUrl += "&to=" + data.param.to_year;
        }
        
        if (data.param.from_year == false || data.param.from_year == null) {
            from = '';
        } else {
             hashUrl += "&from=" + data.param.from_year;
        }

        window.location.hash = '!' + hashUrl;
        
    } else {
        // Form close
    }
}


function redrawForm(action, formDivObjString, field, record_id) {

    html = '';
    if (action == "add") {
        var formAction = '/forms/add/';

        postArray = {type: field};
        $.post(formAction, postArray, function(data) {
            $(formDivObjString).html(data.html);
            //initializeFormAction(field);
        },
                "json");
    } else if (action == "update") {
        var formAction = '/forms/update/';

        postArray = {type: field, record_id: record_id};
        $.post(formAction, postArray, function(data) {
            $(formDivObjString).html(data.html);
            //initializeFormAction(field);
        },
                "json");
    }

}


function buildDynamicList(field, record_id, extraParams, method) {

    //alert(field);

    var controller_name = field.split('_')[0];

    var formAction = '/app/' + controller_name + '/build';

//
//    // to handle search functionality on attorney data
//    if (field == 'attorney_search_list') {
//        var formAction = '/' + controller_name + '/list';
//    }
//
//
//    // to handle search functionality on attorney data
//    if (field == 'attorney_map_list') {
//        var formAction = '/' + controller_name + '/map';
//    }

    //list = list__educationProvider;
    list = "list__" + field;
    listObjString = "#" + list;
    //alert(listObjString);
    postArray = {
        type: field,
        record_id: record_id
    };

    $(listObjString).html('<div style = "border:solid 0px;text-align:center;padding:70px 20px 20px 20px;"><img src = "/img/ajax-loader.gif"></div>');

    if (extraParams == null) {
        // Do Not do anything
    } else {
        $.each(extraParams, function(key, value) {
            postArray[key] = value;
        });
    }

    $.post(formAction, postArray, function(data) {

        //alert(listObjString);
        //console.log(data.param);
        //console.log(data.pagination);
        $('#loading-indicator').hide();
        //alert(listObjString);

        $(listObjString).html(data.html);
        $(listObjString).append(data.pagination);

        if (method) {
            str = method + '(data)';
            eval(str);
        }
        
        // pagination anchor click here
        $(listObjString + " a").click(function(e) {
            id = $(this).attr('id');
            // e.preventDefault();
          // alert(tab);

            if (variableDefined(id)) {
                //console.log(data);
                //alert('Acnhor Clicked');
                initializeLinks(id, e, data);
            }


        });


        // to handle search functionality on attorney data
        if (field == 'attorney_map_list') {
            mapData();
        }
    },
            "json");
}

function buildMessagesOnAction(act, field, response, record_id) {

    messageId = "message__" + field;
    messageObjString = "#" + messageId;

    var message = '';

    if (response == 'yes') {
        if (act == "add") {
            message = fieldNameArray[field] + " Added!";
        } else if (act == "update") {
            message = fieldNameArray[field] + " Updated!";
        } else {
            message = fieldNameArray[field] + " Deleted!";
        }
        $(messageObjString).show().removeClass().addClass("message success").html("<p>" + message + "</p>");

        $(this).oneTime(1000, function() {
            $(divObjString).hide('slow');
            buildDynamicList(field, record_id);
        });

    } else if (response == 'duplicate') {
        message = "Duplicate " + fieldNameArray[field] + "!";
        $(messageObjString).show().removeClass().addClass("message error").html("<p>" + message + "</p>");
    } else {
        if (act == "add") {
            message = fieldNameArray[field] + " cound not be added!";
        } else if (act == "update") {
            message = fieldNameArray[field] + " cound not be updated!";
        } else {
            message = fieldNameArray[field] + " cound not be deleted!";
        }
        $(messageObjString).show().removeClass().addClass("message error").html("<p>" + message + "</p>");
    }
}

