
function _create(collection, data) {
    $.ajax({
        type: "POST",
        data: {
            values: data
        },
        dataType: "html",
        url: ROOTPATH + '/create/'+collection,
        success: function (response) {
            toastSuccess(response)
            $('#result').html(response)
        },
        error: function (response) {
            toastError(response.responseText)
            $('#loader').hide()
        }
    })
}

function _update(collection, id, data) {
    $.ajax({
        type: "POST",
        data: {
            values: data
        },
        dataType: "html",
        url: ROOTPATH + '/update/'+collection+'/' + id,
        success: function (response) {
            toastSuccess("Updated "+response.updated+" datasets.")
            // $('#result').html(response)
        },
        error: function (response) {
            toastError(response.responseText)
            $('#loader').hide()
        }
    })
}

function _delete(collection, id) {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: ROOTPATH + '/delete/'+collection+'/' + id,
        success: function (response) {
            console.log(response);
            toastSuccess("Deleted "+response.deleted+" datasets.")
            // $('#'+id).remove();
            $('#'+id).fadeOut();
        },
        error: function (response) {
            toastError(response.responseText)
            $('#loader').hide()
        }
    })
}


function prependRow(trcontent){
    var table = $('#activity-table tbody')
    var tr = $('<tr>').css('display', 'none')
    tr.html(trcontent)
    table.prepend(tr)
    tr.fadeIn()
}