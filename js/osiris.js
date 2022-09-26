
function _create(data) {
    $('.loader').addClass('show')
    $.ajax({
        type: "POST",
        data: {
            values: data
        },
        dataType: "html",
        url: ROOTPATH + '/create',
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

function _update(id, data) {
    $('.loader').addClass('show')
    $.ajax({
        type: "POST",
        data: {
            values: data
        },
        dataType: "html",
        url: ROOTPATH + '/update/' + id,
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


function _approve(id, approval) {
    $('.loader').addClass('show')
    $.ajax({
        type: "POST",
        data: {
            approval: approval
        },
        dataType: "html",
        url: ROOTPATH + '/approve/' + id,
        success: function (response) {
            $('.loader').removeClass('show')
            var loc = location.pathname.split('/')
            if (loc[loc.length-1] == "issues"){
                $('#tr-' + id).remove()
                return;
            };

            if (approval == 1) {
                $('#approve-'+ id).remove()
                toastSuccess('Approved')
            }
            if (approval == 2) {
                location.reload()
            }
            if (approval == 3) {
                $('#tr-' + id).remove()
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

function _delete(id) {
    $('.loader').addClass('show')
    $.ajax({
        type: "POST",
        dataType: "json",
        url: ROOTPATH + '/delete/' + id,
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
