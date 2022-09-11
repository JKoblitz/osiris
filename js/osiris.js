
function _create(collection, data) {
    $('.loader').addClass('show')
    $.ajax({
        type: "POST",
        data: {
            values: data
        },
        dataType: "html",
        url: ROOTPATH + '/create/' + collection,
        success: function (response) {
            $('.loader').removeClass('show')

            toastSuccess(response)
            $('#result').html(response)
        },
        error: function (response) {
            $('.loader').removeClass('show')
            toastError(response.responseText)
        }
    })
}

function _update(collection, id, data) {
    $('.loader').addClass('show')
    $.ajax({
        type: "POST",
        data: {
            values: data
        },
        dataType: "html",
        url: ROOTPATH + '/update/' + collection + '/' + id,
        success: function (response) {
            $('.loader').removeClass('show')

            toastSuccess("Updated " + response.updated + " datasets.")
            // $('#result').html(response)
        },
        error: function (response) {
            $('.loader').removeClass('show')
            toastError(response.responseText)
        }
    })
}


function _approve(collection, id, approval) {
    $('.loader').addClass('show')
    $.ajax({
        type: "POST",
        data: {
            approval: approval
        },
        dataType: "html",
        url: ROOTPATH + '/approve/' + collection + '/' + id,
        success: function (response) {
            $('.loader').removeClass('show')

            if (approval == 1) {
                $('#approve-' + collection + '-' + id).remove()
                toastSuccess('Approved')
            }
            if (approval == 2) {
                location.reload()
            }
            if (approval == 3) {
                $('#tr-' + collection + '-' + id).remove()
                toastSuccess('Removed activity')
            }
            // toastSuccess("Updated " + response.updated + " datasets.")
            // $('#result').html(response)
        },
        error: function (response) {
            $('.loader').removeClass('show')
            toastError(response.responseText)
        }
    })
}

function _delete(collection, id) {
    $('.loader').addClass('show')
    $.ajax({
        type: "POST",
        dataType: "json",
        url: ROOTPATH + '/delete/' + collection + '/' + id,
        success: function (response) {
            $('.loader').removeClass('show')

            console.log(response);
            toastSuccess("Deleted " + response.deleted + " datasets.")
            // $('#'+id).remove();
            $('#' + id).fadeOut();
        },
        error: function (response) {
            $('.loader').removeClass('show')
            toastError(response.responseText)
        }
    })
}


function prependRow(trcontent) {
    var table = $('#activity-table tbody')
    var tr = $('<tr>').css('display', 'none')
    tr.html(trcontent)
    table.prepend(tr)
    tr.fadeIn()
}
