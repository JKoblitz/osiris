
const SCIENTISTS = Object.values(
    $('#scientist-list option').map(function (index, item) {
        return item.value
    })
)

$('input[name=activity]').on('change', function(){
    $('input[name=activity]').removeClass('btn-primary')
    $(this).addClass('btn-primary')
    
})

function toastError(msg = "") {
    digidive.initStickyAlert({
        content: msg,
        title: "Error",
        alertType: "danger",
        hasDismissButton: true
    })
}
function toastSuccess(msg = "") {
    digidive.initStickyAlert({
        content: msg,
        title: "Success",
        alertType: "success",
        hasDismissButton: true
    })
}


function objectifyForm(formArray) {
    //serialize data function
    var returnArray = {};
    for (var i = 0; i < formArray.length; i++) {
        returnArray[formArray[i]['name']] = formArray[i]['value'];
    }
    return returnArray;
}

function resetInput(el) {
    $(el).addClass('hidden')
    var el = $(el).prev()
    var old = el.attr("data-value").trim()
    el.val(old)
    el.removeClass("is-valid")
}


$('[data-value]').on("update blur", function () {
    var el = $(this)
    var old = el.attr("data-value").trim()
    var name = el.attr('name')
    if (old !== undefined) {
        if (old != el.val().trim() && !el.hasClass("is-valid")) {
            el.addClass("is-valid")
            el.next().removeClass('hidden')
        } else if (old == el.val().trim() && el.hasClass("is-valid")) {
            el.removeClass("is-valid")
            el.next().addClass('hidden')
        }
    }
})


$('#edit-form').on('submit', function (event) {
    event.preventDefault()
    var values = {}
    $('#edit-form [data-value]').each(function (i, el) {
        var el = $(el)
        var name = el.attr('name')
        var old = el.attr("data-value").trim()
        if (old != el.val().trim()) {
            values[name] = el.val()
        }
    })
    if (Object.entries(values).length === 0) {
        toastError("Nothing to change. Only highlighted fields will be submitted to the database.")
        return
    }
    $('#edit-form input[type="hidden"]').each(function (i, el) {
        var el = $(el)
        var name = el.attr('name')
        values[name] = el.val()
    })
    values['comment'] = $('#editor-comment').val()
    console.log(values);
    $.ajax({
        type: "POST",
        data: values,
        dataType: "html",
        url: ROOTPATH + "/update",
        success: function (data) {
            console.log(data);
            toastSuccess(data)
            location.reload()
        },
        error: function (response) {
            console.log(response.responseText)
            toastError(response.responseText)
        }
    })
})


$('.highlight-badge').on("mouseenter", function () {
    var row = this.innerHTML;
    $("#row-" + row).addClass('table-primary')
})
    .on("mouseleave", function () {
        var row = this.innerHTML;
        $("#row-" + row).removeClass('table-primary')
    })


function tableToCSV() {

    // Variable to store the final csv data
    var csv_data = [];

    // Get each row data
    var rows = document.getElementsByTagName('tr');
    for (var i = 0; i < rows.length; i++) {

        // Get each column data
        var cols = rows[i].querySelectorAll('td,th');

        // Stores each csv row data
        var csvrow = [];
        for (var j = 0; j < cols.length; j++) {

            // Get the text data of each cell of
            // a row and push it to csvrow
            csvrow.push(cols[j].innerHTML);
        }

        // Combine each column value with comma
        csv_data.push(csvrow.join(";"));
    }
    // combine each row data with new line character
    csv_data = csv_data.join('\n');

    downloadCSVFile(csv_data);
}
function downloadCSVFile(csv_data) {

    // Create CSV file object and feed our
    // csv_data into it
    CSVFile = new Blob([csv_data], { type: "text/csv" });

    // Create to temporary link to initiate
    // download process
    var temp_link = document.createElement('a');

    // Download csv file
    temp_link.download = "itool.csv";
    var url = window.URL.createObjectURL(CSVFile);
    temp_link.href = url;

    // This link should not be displayed
    temp_link.style.display = "none";
    document.body.appendChild(temp_link);

    // Automatically click the link to trigger download
    temp_link.click();
    document.body.removeChild(temp_link);
}

function getPubData(event, form) {
    event.preventDefault();
    $('#loader').show()
    console.log(form);
    if (form !== null) {
        param = $(form).serializeArray()
        param = objectifyForm(param)
    }
    console.log(param);
    // TODO: check if doi is valid

    if (param.doi !== null && param.doi !== "") {
        url = "https://api.crossref.org/works/" + param.doi
    }
    // data = {}
    $.ajax({
        type: "GET",
        // data: data,
        dataType: "json",
        url: url,
        success: function (data) {
            $('#loader').hide()
            console.log(data);
            var pub = data.message
            date = ""
            var date = getPublishingDate(pub)

            $('#publication-form').find('input').val('').removeClass('is-valid')
            $('#title').val(pub.title[0]).addClass('is-valid')
            $('#journal').val(pub['container-title'][0]).addClass('is-valid')

            if (pub['journal-issue'] !== undefined && pub['journal-issue'].length !== 0) {
                $('#issue').val(pub['journal-issue'].issue).addClass('is-valid')
                console.log(pub['journal-issue']);
                var date2 = getPublishingDate(pub['journal-issue'])
                if (date2 !== "") {
                    date = date2
                }
            } else {
                $('#epub').attr('checked', true).addClass('is-valid')
            }
            $('#volume').val(pub.volume).addClass('is-valid')
            $('#pages').val(pub.page).addClass('is-valid')
            $('#doi').val(pub.DOI).addClass('is-valid')
            // $('#pubmed').val()
            $('#date_publication').val(date).addClass('is-valid')
            $('#type').val(pub.type).addClass('is-valid')
            // $('#book_title').val()

            $('.author-list').addClass('is-valid').find('.author').remove()

            pub.author.forEach(function (d, i) {
                var dsmz = false
                d.affiliation.forEach(e => {
                    if (e.name.includes("DSMZ")) {
                        dsmz = true
                    }
                })
                var first = false
                if (d.sequence == "first") {
                    first = true
                }
                addAuthorDiv(d.family, d.given, dsmz)

            })


            toastSuccess('Bibliographic data were updated.')
        },
        error: function (response) {
            toastError(response.responseText)
            $('#loader').hide()
        }
    })
}


function addAuthorDiv(lastname, firstname, dsmz = false) {
    var author = $('<div class="author">')
        .on('click', function () {
            toggleAffiliation(this)
        })
        .html(lastname + ', ' + firstname);
    var val = 0
    if (dsmz) {
        val = 1
        author.addClass('author-dsmz')
    }
    val = lastname.trim() + ';' + firstname.trim() + ';' + val

    author.append('<input type="hidden" name="author[]" value="' + val + '">')
    author.append('<a onclick="removeAuthor(event, this)">&times;</a>')

    author.insertBefore($('#add-author'))
}

function getPublishingDate(pub) {
    var date = "";
    if (pub['published-print']) {
        date = getDate(pub['published-print'])
    } else if (pub['published']) {
        date = getDate(pub['published'])
    } else if (pub['published-online']) {
        date = getDate(pub['published-online'])
    }
    return date
}

function getDate(element) {
    if (element['date-parts'] !== undefined) {
        element = element['date-parts'][0];
    }
    var date = ""
    // 2022-07-06
    if (element[0]) date = element[0]

    if (element[1]) date += "-" + ("0" + element[1]).slice(-2)
    else date += "-01"

    if (element[2]) date += "-" + ("0" + element[2]).slice(-2)
    else date += "-01"

    console.log(date);
    // if (element[1]) date = ("0" + element[1]).slice(-2) + "." + date
    // if (element[2]) date = ("0" + element[2]).slice(-2) + "." + date
    return date
}

function toggleAffiliation(item) {
    var old = $(item).find('input').val().split(';')
    if ($(item).hasClass('author-dsmz')) {
        old[2] = 0
    }
    else {
        old[2] = 1
    }
    console.log(old);
    $(item).find('input').val(old.join(';'))
    $(item).toggleClass('author-dsmz')
}

function addAuthor(event, el) {
    if (event.keyCode == '13') {
        event.preventDefault();
        const match = (SCIENTISTS.indexOf(el.value) != -1)
        var value = el.value.split(',')
        if (value.length !== 2) {
            toastError('Author name must be formatted like this: Lastname, Firstname')
            return;
        }
        addAuthorDiv(value[0], value[1], match)

        $(el).val('')
        return false;
    }
}
function removeAuthor(event, el) {
        event.preventDefault();
        $(el).parent().remove()
}