function form_type_kfi_upload(data) {
    function executeFunctionByName(functionName /*, args */) {
        var context = window;
        var args = Array.prototype.slice.call(arguments).splice(1);
        var namespaces = functionName.split(".");
        var func = namespaces.pop();
        for (var i = 0; i < namespaces.length; i++) {
            context = context[namespaces[i]];
        }
        return context[func].apply(this, args);
    }

    var fnSettings = {
        libUrl:          null,
        sessionID:       null,
        fieldName:      null,
        name:            null,
        uploader:        null,
        multi:           null,
        fileExt:         null,
        fileDesc:        null,
        simUploadLimit:  null,
        ajaxFill:        '',
        ajaxDeleteUpload:'',
        onComplete:      '',
        onCancel:        '',
        sortable:        false
    };
    $.extend(true, fnSettings, data);

    var queueID = fnSettings.name + '-queue';
    if (fnSettings.multi && fnSettings.sortable)
        multi_initSortable(queueID);

    $('#' + fnSettings.name + '_uploader').uploadify({
        //scriptAccess:   'always',
        formData:       {'PHPSESSID':fnSettings.sessionID},
        swf:            fnSettings.libUrl + '/uploadify.swf',
        //buttonImg:   	  fnSettings.libUrl+'rollover-button3.png',
        uploader:       fnSettings.uploader,
        auto:           true,
        multi:          fnSettings.multi,
        fileTypeExts:   fnSettings.fileExt,
        fileTypeDesc:   fnSettings.fileDesc,
        queueID:        queueID,
        //massimo numero di files da uploadare
        //simUploadLimit: fnSettings.simUploadLimit,
        removeCompleted:false,
        wmode:          'transparent',
        rollover:       true,
        onUploadSuccess:function (file, response) {
            response = $.parseJSON(response);
            if (fnSettings.ajaxFill.length) {
                executeFunctionByName(
                    fnSettings.onComplete,
                    fnSettings.ajaxFill,
                    fnSettings.name,
                    fnSettings.fieldName,
                    file, response
                );
            } else {
                executeFunctionByName(
                    fnSettings.onComplete,
                    fnSettings.name,
                    fnSettings.fieldName,
                    file, response
                );
            }
        },

        onCancel:       function (file) {
            executeFunctionByName(
                fnSettings.onCancel,
                fnSettings.ajaxDeleteUpload,
                file,
                fnSettings.name,
                fnSettings.fieldName
            );
        },
        onUploadError:  function () {
            console.log('onUploadError');
            console.log(arguments);
//
//            if (d.status == 404)
//                alert('Could not find upload script. Use a relative path');
//            else if (d.type === "HTTP")
//                alert('error ' + d.type + ": " + d.status);
//            else if (d.type === "File Size")
//                alert(c.name + ' ' + d.type + ' Limit: ' + Math.round(d.sizeLimit / 1024) + 'KB');
//            else
//                alert('error ' + d.type + ": " + d.info);
        }
    });

    $('#' + fnSettings.name + '_uploader a.kfiup_add_editor').click(function(){
        var url = $(this).parent().find('a.kfi-upload-link').attr('href');
        tinyMCE.execCommand(
            'mceInsertContent',
            false,
            '<img src="'+url+'"/>'
        );
        return false;
    });
    
}

/**
 * Funzioni di callback della event edit action per il plugin jQuery.uploadify
 */


/**
 * ajaxForm
 * url della chiamata ajax per il caricamento del sottoform per ogni immagine
 *
 * event
 * The event object.
 *
 * ID
 * The unique ID of the file queue item.
 *
 * fileObj
 * An object containing the file information.
 *
 *     [name] The name of the uploaded file
 *  [filePath] The path on the server to the uploaded file
 *  [size] The size in bytes of the file
 *  [creationDate] The date the file was created
 *  [modificationDate] The last date the file was modified
 *  [type] The file extension beginning with a �.�
 *
 * response
 * The text response sent back from the back-end upload script.
 * NB: la facade Uploads restituisce un json nel parametro response
 *     con i seguenti parametri
 *     [file]  - percorso del file appena uploadato
 *     [thumb] - percorso del file autogenerato o selezionato per la thumbnial/icon
 *
 * data
 * An object containing general data about the upload and the queue.
 *
 *  [fileCount] - The number of files remaining in the queue
 *  [speed] - The average speed pf the file upload in KB/s
 */



function single_onComplete(name, fieldName, fileObj, response) {
    var event_id = $('#' + name + '_id').attr('value');
    var ID = fileObj.id;
    var temp = fileObj.name.split(".");
    var filename = "";
    for (var i = 0; i < temp.length - 1; i++) {
        filename += temp[i];
        if (i < temp.length - 2) filename += ".";
    }
    var type = "";
    if (fileObj.type == '.jpg') type = "Image";
    if (fileObj.type == '.gif') type = "Image";
    if (fileObj.type == '.png') type = "Image";
    if (fileObj.type == '.flv') type = "Video";

    $('#'+ID).siblings().remove();
    setHiddenValue(name, fieldName, ID, filename, response, 0, type);
}

/**
 *
 * event
 * The event object.
 *
 * ID
 * The unique identifier of the file that was cancelled.
 *
 * fileObj
 * An object containing details about the file that was selected.
 *
 *     [name] The name of the file
 *     [size] The size in bytes of the file
 *     [creationDate] The date the file was created
 *     [modificationDate] The last date the file was modified
 *     [type] The file extension beginning with a '.'
 *
 * data
 * Details about the file queue.
 *
 *     [fileCount] The total number of files left in the queue
 *     [allBytesTotal] The total number of bytes left for all files in the queue
 *
 */
function single_onCancel(ajaxDeleteUpload, file, name, fieldName) {
    var ID = file.id;
    var url = $('#' + name + '_url_' + ID).attr('value');
    var remote = $('#' + name + '_remote_' + ID).attr('value');
    if (url.indexOf("uploads/temp/") != -1) {
        $.post(ajaxDeleteUpload, { source:url, is_remote:remote });
    }
}

/**
 * Funzioni di callback della event edit action per il plugin jQuery.uploadify
 */

function multi_initSortable(queueId) {
    console.log([multi_initSortable, queueId]);
    $('#' + queueId).sortable({'axis':'y', 'containment':$('#' + queueId)});
    $('#' + queueId).disableSelection();
}

/**
 * ajaxForm
 * url della chiamata ajax per il caricamento del sottoform per ogni immagine
 *
 * event
 * The event object.
 *
 * ID
 * The unique ID of the file queue item.
 *
 * fileObj
 * An object containing the file information.
 *
 *     [name] The name of the uploaded file
 *  [filePath] The path on the server to the uploaded file
 *  [size] The size in bytes of the file
 *  [creationDate] The date the file was created
 *  [modificationDate] The last date the file was modified
 *  [type] The file extension beginning with a ï¿½.ï¿½
 *
 * response
 * The text response sent back from the back-end upload script.
 * NB: la facade Uploads restituisce un json nel parametro response
 *     con i seguenti parametri
 *     [file]  - percorso del file appena uploadato
 *     [thumb] - percorso del file autogenerato o selezionato per la thumbnial/icon
 *
 * data
 * An object containing general data about the upload and the queue.
 *
 *  [fileCount] - The number of files remaining in the queue
 *  [speed] - The average speed pf the file upload in KB/s
 */



function multi_onComplete(name, fieldName, fileObj, response) {
    var ID = fileObj.id;
    var temp = fileObj.name.split(".");
    var filename = "";
    for (var i = 0; i < temp.length - 1; i++) {
        filename += temp[i];
        if (i < temp.length - 2) filename += ".";
    }
    var type = "";
    if (fileObj.type == '.jpg') type = "Image";
    if (fileObj.type == '.gif') type = "Image";
    if (fileObj.type == '.png') type = "Image";
    if (fileObj.type == '.flv') type = "Video";

    setHiddenValue(name, fieldName, ID, filename, response, 0, type);
}

function setHiddenValue(name, fieldName, ID, filename, response, remote, type) {
    console.log(response);
    $('#'+ID).prepend(response.html);

    var html = "<div id='" + name + "_hidden_" + ID + "'>";
    html += "<input type='hidden' id='" + name + "_title_" + ID + "' name='" + fieldName + "[" + ID + "][title]' value='" + filename + "'/>";
    html += "<input type='hidden' id='" + name + "_url_" + ID + "' name='" + fieldName + "[" + ID + "][url]' value='" + response.file + "'/>";
    html += "<input type='hidden' id='" + name + "_remote_" + ID + "' name='" + fieldName + "[" + ID + "][remote]' value='" + remote + "'/>";
    html += "<input type='hidden' id='" + name + "_type_" + ID + "' name='" + fieldName + "[" + ID + "][type]' value='" + type + "'/>";
    html += "</div>";
    $('#'+ID).append(html);
}

function showMediaFrom(ID) {
    $("#mf_" + ID).toggle();
    if ($("#md_" + ID).hasClass('open')) $("#md_" + ID).removeClass('open');
    else $("#md_" + ID).addClass('open');
}

/**
 *
 * event
 * The event object.
 *
 * ID
 * The unique identifier of the file that was cancelled.
 *
 * fileObj
 * An object containing details about the file that was selected.
 *
 *     [name] The name of the file
 *     [size] The size in bytes of the file
 *     [creationDate] The date the file was created
 *     [modificationDate] The last date the file was modified
 *     [type] The file extension beginning with a '.'
 *
 * data
 * Details about the file queue.
 *
 *     [fileCount] The total number of files left in the queue
 *     [allBytesTotal] The total number of bytes left for all files in the queue
 *
 */
function multi_onCancel(ajaxDeleteUpload, file, name, fieldName) {
    var id = file.id;
    var url = $('#' + name + '_url_' + ID).attr('value');
    var remote = $('#' + name + '_remote_' + ID).attr('value');
    if (url.indexOf("uploads/temp/") != -1) {
        $.post(
            ajaxDeleteUpload,
            { source:url, is_remote:remote }
        );
    }
}

/**
 * Callback invocato dall'Uploader al caricamento della dialog per
 * l'embed dei link remoti
 *
 * @param dlg - oggetto dialog generato con jQueryUI
 * @param dlgID - id della div in cui caricare il form in HTML
 * @param dlgFormName - nome del form da generare dinamicamente via ajax
 * @param ajaxFormUrl - url per la chiamata ajax di caricamento dinamico del form
 */
function loadEmbedForm(dlg, dlgID, dlgFormName, ajaxFormUrl) {
    $.post(ajaxFormUrl, {form_name:dlgFormName}, function (form) {
        $(dlgID).html(form);
        dlg.dialog('open');
    });
}

/**
 * Callback invocato dall'Uploader per il validamento della dialog per
 * l'embed dei link remoti
 *
 * @param dlg - oggetto dialog generato con jQueryUI
 * @param dlgID - id della div in cui caricare il form in HTML
 * @param dlgFormName - nome del form da generare dinamicamente via ajax
 * @param ajaxValidateUrl - url per la chiamata ajax di validamento dinamico del form
 */
function validateEmbedForm(dlg, dlgID, dlgFormName, ajaxValidateUrl) {
    $.post(ajaxValidateUrl, $('#' + dlgFormName).serialize(), function (json) {
        var data = jQuery.parseJSON(json);
        if (data.errors == 0) {
            $('#' + name + '-queue').append(data.html);
            dlg.dialog('close');
        } else $(dlgID).html(data.html);
    });
}