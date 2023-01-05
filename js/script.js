

var SCIENTISTS;
$(document).ready(function () {
    var scientists = $('#scientist-list option').map(function (index, item) {
        return item.value
    })
    SCIENTISTS = Object.values(scientists)

    var authordiv = $('.author-list')
    if (authordiv.length > 0) {

        authordiv.sortable({
            // handle: ".author",
            // change: function( event, ui ) {}
        });
    }

    $('.title-editor').each(function (el) {
        var element = this;

        var quill = new Quill(element, {
            modules: {
                toolbar: [
                    ['italic', 'underline'],
                    [{ script: 'super' }, { script: 'sub' }]
                ]
            },
            formats: ['italic', 'underline', 'script', 'symbol'],
            placeholder: '',
            theme: 'snow' // or 'bubble'
        });

        quill.on('text-change', function (delta, oldDelta, source) {
            var delta = quill.getContents()
            console.log(delta);
            var str = $(element).find('.ql-editor p').html()
            console.log(str);
            // var str = ""
            // delta.ops.forEach(el => {
            //     if (el.attributes !== undefined) {
            //         // if (el.attributes.bold) str += "<b>";
            //         if (el.attributes.italic) str += "<i>";
            //         if (el.attributes.underline) str += "<u>";
            //     }
            //     str += el.insert;
            //     if (el.attributes !== undefined) {
            //         if (el.attributes.underline) str += "</u>";
            //         if (el.attributes.italic) str += "</i>";
            //         // if (el.attributes.bold) str += "</b>";
            //     }
            // });
            // $('.add-form #title').val(str)
            $(element).next().val(str)
        });

        // add additional symbol toolbar for greek letters
        var additional = $('<span class="ql-formats">')
        var symbols = ['α', 'β', 'π', 'Δ']
        symbols.forEach(symbol => {
            var btn = $('<button type="button" class="ql-symbol">')
            btn.html(symbol)
            btn.on('click', function () {
                // $('.symbols').click(function(){
                quill.focus();
                var symbol = $(this).html();
                var caretPosition = quill.getSelection(true);
                quill.insertText(caretPosition, symbol);
                // });
            })
            additional.append(btn)
        });

        $('.ql-toolbar').append(additional)
    })
    // if ($('.add-form #title-editor').length !== 0 ){

    // }

})

function readHash() {
    var hash = window.location.hash.substr(1);
    console.log(hash);
    if (hash === undefined || hash == "") return {}
    return hash.split('&').reduce(function (res, item) {
        var parts = item.split('=');
        res[parts[0]] = parts[1];
        return res;
    }, {});
}

function writeHash(data) {
    var hash = readHash()
    for (const key in data) {
        if (data[key] === null)
            delete hash[key]
        else
            hash[key] = data[key];
    }
    hash = Object.entries(hash)
    var arr = hash.map(function (a) {
        return a[0] + "=" + a[1]
    })
    window.location.hash = arr.join("&")
}

$('input[name=activity]').on('change', function () {
    $('input[name=activity]').removeClass('btn-primary')
    $(this).addClass('btn-primary')

})

function toastError(msg = "", title = 'Error') {
    digidive.initStickyAlert({
        content: msg,
        title: title,
        alertType: "danger",
        hasDismissButton: true,
        timeShown: 10000
    })
}
function toastSuccess(msg = "", title = 'Success') {
    digidive.initStickyAlert({
        content: msg,
        title: title,
        alertType: "success",
        hasDismissButton: true,
        timeShown: 10000
    })
}
function toastWarning(msg = "", title = 'Warning') {
    digidive.initStickyAlert({
        content: msg,
        title: title,
        alertType: "signal",
        hasDismissButton: true,
        timeShown: 10000
    })
}
function getCookie(cname) {
    let decodedCookie = decodeURIComponent(document.cookie);
    if (cname === null) {
        return decodedCookie
    }
    let name = cname + "=";
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
function lang(en, de = null) {
    var language = getCookie('osiris-language');
    if (de === null) return en;
    if (language === undefined) return de;
    if (language == "en") return en;
    if (language == "de") return de;
    return de;
}

function objectifyForm(formArray) {
    //serialize data function
    var returnArray = {};
    for (var i = 0; i < formArray.length; i++) {
        returnArray[formArray[i]['name']] = formArray[i]['value'];
    }
    return returnArray;
}

function isEmpty(value) {
    switch (typeof (value)) {
        case "string": return (value.length === 0);
        case "number":
        case "boolean": return false;
        case "undefined": return true;
        case "object": return !value ? true : false; // handling for null.
        default: return !value ? true : false
    }
}

function resetInput(el) {
    $(el).addClass('hidden')
    var el = $(el).prev()
    var old = el.attr("data-value").trim()
    el.val(old)
    el.removeClass("is-valid")
}



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

function getPublication(id) {
    $('.loader').addClass('show')
    if (/(10\.\d{4,5}\/[\S]+[^;,.\s])$/.test(id)) {
        id = id.match(/(10\.\d{4,5}\/[\S]+[^;,.\s])$/)[0]
        getDOI(id)
    } else if (/^(\d{7,8})$/.test(id)) {
        getPubmed(id)
    } else {
        toastError('This is neither DOI nor Pubmed-ID. Sorry.');
        $('.loader').removeClass('show')
        return
    }
}

function getJournal(name) {
    console.log(name);
    const SUGGEST = $('#journal-suggest')
    SUGGEST.empty()
    var url = ROOTPATH + '/api/journal'
    // https://api.clarivate.com/apis/wos-journals/v1/journals?q=matrix biology
    var data = {
        search: name,
        limit: 10
    }
    $.ajax({
        type: "GET",
        data: data,
        dataType: "json",

        url: url,
        success: function (response) {
            var journals = [];
            response.data.forEach(j => {

                journals.push({
                    journal: j.journal,
                    issn: j.issn,
                    abbr: j.abbr,
                    publisher: j.publisher,
                    id: j._id['$oid']
                })
            });
            if (journals.length === 1) {
                selectJournal(journals[0])
                toastSuccess(lang('Journal ID added.', 'Journal-ID wurde hinzugefügt.'), lang('Journal found', 'Journal gefunden'))
            } else if (journals.length === 0) {
                SUGGEST.append('<tr><td>' + lang('Journal not found in OSIRIS', 'Journal nicht in OSIRIS gefunden') + '</tr></td>')
                window.location.replace('#journal-select')
            } else {
                journals.forEach((j) => {
                    console.log(j);
                    var row = $('<tr>')

                    var button = $('<button class="btn" title="select">')
                    button.html('<i class="fas fa-lg fa-check"></i>')
                    button.on('click', function () {
                        selectJournal(j);
                    })
                    row.append($('<td class="w-50">').append(button))

                    var data = $('<td>')
                    data.append(`<h5 class="m-0">${j.journal}</h5>`)
                    data.append(`<span class="float-right">${j.publisher}</span>`)
                    data.append(`<span class="text-muted">${j.issn.join(', ')}</span>`)
                    row.append(data)

                    SUGGEST.append(row)
                })
                window.location.replace('#journal-select')
            }
            var row = $('<tr>')
            var button = $('<button class="btn">')
            button.html(lang('Search in NLM Catalog', 'Suche im NLM-Katalog'))
            button.on('click', function () {
                getJournalNLM(name)
            })
            row.append($('<td>').append(button))
            SUGGEST.append(row)
            // window.location.replace('#journal-select')


            console.log(journals);
        },
        error: function (response) {
            toastError(response.responseText)
            $('.loader').removeClass('show')
        }
    })
}

function selectJournal(j, n = false) {

    if (n) {
        console.log(j);
        $.ajax({
            type: "POST",
            data: {
                values: j
            },
            dataType: "json",
            url: ROOTPATH + '/create-journal',
            success: function (response) {
                // $('.loader').removeClass('show')
                console.log(response);
                if (response.msg) {
                    toastWarning(response.msg)
                    selectJournal(response, false)
                    return;
                } else {
                    $('#journal_id').val(response.id['$oid'])
                    $('#journal_rev_id').val(response.id['$oid'])
                    $('#journal').val(j.journal)
                    $('#journal-input').val(j.journal)
                    $('#issn').val(j.issn.join(' '))
                }
            },
            error: function (response) {
                $('.loader').removeClass('show')
                toastError(response.responseText)
            }
        })
    } else {
        $('#journal_id').val(j.id['$oid'] ?? j.id)
        $('#journal_rev_id').val(j.id['$oid'] ?? j.id)
        $('#journal').val(j.journal)
        $('#journal-input').val(j.journal)
        $('#issn').val(j.issn.join(' '))
    }
    window.location.replace('#')
}

function getJournalNLM(name) {
    var url = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi'
    const SUGGEST = $('#journal-suggest')
    SUGGEST.empty()
    // https://api.clarivate.com/apis/wos-journals/v1/journals?q=matrix biology
    var data = {
        db: 'nlmcatalog',
        term: '("' + name + '"[title]) AND (ncbijournals[filter])',
        retmode: 'json',
        usehistory: 'y'
    }
    if (name.match(/\d{4}-?\d{4}/)){
        // issn search
        data.term = name  + ' AND (ncbijournals[filter])'
    }
    $.ajax({
        type: "GET",
        data: data,
        dataType: "json",

        url: url,
        success: function (response) {
            var env = response.esearchresult.webenv
            var key = response.esearchresult.querykey

            var data = {
                retmode: 'json',
                db: 'nlmcatalog',
                query_key: key,
                WebEnv: env
            }
            var url = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi'

            $.ajax({
                type: "GET",
                data: data,
                dataType: "json",
                url: url,
                success: function (result) {
                    console.log(result);
                    var journals = [];

                    for (const id in result.result) {
                        if (id == 'uids') continue;
                        const j = result.result[id];
                        console.log(j);
                        var issn = [];
                        j.issnlist.forEach(item => {
                            issn.push(item.issn)
                        });
                        if (issn.length === 0) continue;
                        var name = j.titlemainlist[0].title
                        journals.push({
                            journal: name.slice(0, name.length - 1),
                            issn: issn,
                            abbr: j.medlineta,
                            publisher: j.publicationinfolist[0].publisher,
                            nlmid: id
                        })
                    }
                    journals.forEach((j) => {
                        var row = $('<tr>')

                        var button = $('<button class="btn" title="select">')
                        button.html('<i class="fas fa-lg fa-check"></i>')
                        button.on('click', function () {
                            selectJournal(j, true);
                        })
                        row.append($('<td class="w-50">').append(button))

                        var data = $('<td>')
                        data.append(`<h5 class="m-0">${j.journal}</h5>`)
                        data.append(`<span class="float-right">${j.publisher}</span>`)
                        data.append(`<span class="text-muted">${j.issn.join(', ')}</span>`)
                        row.append(data)

                        SUGGEST.append(row)
                    })
                    if (journals.length === 0) {
                        SUGGEST.append('<tr><td>' + lang('Journal not found in NLM. Maybe you want to add a magazine article?', 'Journal nicht in NLM gefunden. Wolltest du vielleicht einen Magazin-Artikel hinzufügen?') + '</tr></td>')
                    }

                    console.log(journals);
                },
                error: function (response) {
                    toastError(response.responseText)
                    $('.loader').removeClass('show')
                }
            })


        },
        error: function (response) {
            toastError(response.responseText)
            $('.loader').removeClass('show')
        }
    })
}

function getPubmed(id) {
    var url = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi'
    var data = {
        db: 'pubmed',
        id: id,
        retmode: 'json'
    }
    $.ajax({
        type: "GET",
        data: data,
        dataType: "json",

        url: url,
        success: function (data) {
            console.log(data);
            var pmid = data.result.uids[0]
            var pub = data.result[pmid]

            // var date = pub.pubdate
            var doi = ""
            pub.articleids.forEach(el => {
                if (el.idtype == 'doi') doi = el.value
            });

            var date = new Date(pub.sortpubdate)

            var authors = [];
            var editors = [];
            pub.authors.forEach((a, i) => {
                var name = a.name.split(' ', 2)
                name = {
                    family: name[0],
                    given: name[1].split('').join(' ')
                }
                if (a.authtype == "Author") {
                    authors.push(name)
                } else if (a.authtype == "Editor") {
                    editors.push(name)
                }
            });


            var pubdata = {
                title: pub.title,
                // first_authors: pub.first_authors,
                authors: authors,
                year: date.getFullYear(),
                month: date.getMonth() + 1,
                day: date.getDate(),
                type: pub.doctype == 'chapter' ? 'book' : pub.pubtype[0],
                journal: pub.fulljournalname,
                // issn: (pub.ISSN ?? []).join(' '),
                issue: pub.issue,
                volume: pub.volume,
                pages: pub.pages,
                doi: doi,
                pubmed: pmid,
                book: pub.booktitle,
                edition: pub.edition,
                publisher: pub.publishername,
                city: pub.publisherlocation,
                editors: editors,
                // open_access: pub.open_access,
                epub: pub.pubstatus == 10,
            }
            fillForm(pubdata)
            $('.loader').removeClass('show')
        },
        error: function (response) {
            toastError(response.responseText)
            $('.loader').removeClass('show')
        }
    })
}


function getDOI(doi) {
    url = "https://api.crossref.org/works/" + doi + '?mailto=juk20@dsmz.de'
    $.ajax({
        type: "GET",
        // data: data,
        dataType: "json",
        // cors: true ,
        //   contentType:'application/json',
        //   secure: true,
        //   headers: {
        //     'Access-Control-Allow-Origin': '*',
        //   },
        url: url,
        success: function (data) {
            var pub = data.message
            console.log(pub);


            var date = getPublishingDate(pub)
            if (pub['journal-issue'] !== undefined && pub['journal-issue'].length !== 0) {
                var date2 = getPublishingDate(pub['journal-issue'])
            }

            var authors = [];
            // var editors = [];
            var first = 1
            pub.author.forEach((a, i) => {
                var aoi = false
                a.affiliation.forEach(e => {
                    if (e.name.includes(AFFILIATION)) {
                        aoi = true
                    }
                })
                if (a.sequence == "first") {
                    first = i + 1
                }
                var name = {
                    family: a.family,
                    given: a.given,
                    affiliation: aoi
                }
                authors.push(name)
            });
            var issue = null
            if (pub['journal-issue'] !== undefined) issue = pub['journal-issue'].issue

            var pubdata = {
                title: pub.title[0],
                first_authors: first,
                authors: authors,
                year: date[0],
                month: date[1],
                day: date[2],
                type: pub.type,
                journal: pub['container-title'][0],
                issn: (pub.ISSN ?? []).join(' '),
                issue: issue,
                volume: pub.volume ?? '',
                pages: pub.page,
                doi: pub.DOI,
                // pubmed: null,
                book: pub['container-title'][0],
                // edition: pub.edition,
                publisher: pub['publisher-name'],
                city: pub['publisher-location'],
                // open_access: pub.open_access,
                epub: pub['published-print'] === undefined,
            }
            fillForm(pubdata)
            $('.loader').removeClass('show')
        },
        error: function (response) {
            // toastError(response.responseText)
            $('.loader').removeClass('show')
            toastWarning('DOI was not found in CrossRef. I am looking in DataCite now.')
            getDataciteDOI(doi)
        }
    })
}

function getDataciteDOI(doi) {
    url = "https://api.datacite.org/dois/" + doi //+ '?mailto=juk20@dsmz.de'
    $('.loader').addClass('show')

    var dataCiteTypes = {
        'book': 'book',
        'bookchapter': 'bookchapter',
        'journal': 'article',
        'journalarticle': 'article',
        'conferencepaper': 'article',
        'conferenceproceeding': 'article',
        'dissertation': 'dissertation',
        'preprint': 'preprint',
        'software': 'software',
        'computationalnotebook': 'software',
        'model': 'software',
        'datapaper': 'dataset',
        'dataset': 'dataset',
        'peerreview': 'review',
        'audiovisual': 'misc',
        'collection': 'misc',
        'event': 'misc',
        'image': 'misc',
        'report': 'others',
        'interactiveresource': 'misc',
        'outputmanagementplan': 'misc',
        'physicalobject': 'misc',
        'service': 'misc',
        'sound': 'misc',
        'standard': 'misc',
        'text': 'misc',
        'workflow': 'misc',
        'other': 'misc',
        'presentation': 'lecture',
        'poster': 'poster'
    }

    $.ajax({
        type: "GET",
        // data: data,
        dataType: "json",
        // cors: true ,
        //   contentType:'application/json',
        //   secure: true,
        //   headers: {
        //     'Access-Control-Allow-Origin': '*',
        //   },
        url: url,
        success: function (data) {
            var pub = data.data.attributes
            console.log(pub);

            var date = pub.dates[0].date
            if (date !== undefined) {
                dateSplit = getDate(date.split('-'))
                date = strDate(dateSplit)
            } else {
                dateSplit = [pub.publicationYear, 1, null]
                date = pub.publicationYear + "-01-01"
            }
            console.log(dateSplit, date);

            var authors = [];
            // var editors = [];
            var first = 1
            pub.creators.forEach((a, i) => {
                var aoi = false
                a.affiliation.forEach(e => {
                    if (e.includes(AFFILIATION)) {
                        aoi = true
                    }
                })
                if (a.sequence == "first") {
                    first = i + 1
                }
                var name = {
                    family: a.familyName,
                    given: a.givenName,
                    affiliation: aoi
                }
                authors.push(name)
            });
            var type = pub.types.resourceTypeGeneral.toLowerCase()
            type = dataCiteTypes[type]

            var resType = pub.types.resourceType
            if (resType !== undefined && dataCiteTypes[resType.toLowerCase()] !== undefined) {
                type = dataCiteTypes[resType.toLowerCase()]
            }
            console.info(type);

            var pubdata = {
                type: type,
                title: pub.titles[0].title,
                first_authors: first,
                authors: authors,
                doi: pub.doi,
                date_start: date
            }

            if (type == 'software' || type == 'dataset') {
                pubdata['date_start'] = date
                pubdata['software_version'] = pub.version
                pubdata['software_doi'] = pub.doi
                pubdata['software_venue'] = pub.publisher
            } else {
                pubdata['year'] = dateSplit[0] ?? null
                pubdata['month'] = dateSplit[1] ?? null
                pubdata['day'] = dateSplit[2] ?? null
            }

            fillForm(pubdata)
            $('.loader').removeClass('show')
        },
        error: function (response) {
            toastError(response.responseText.errors.title)
            $('.loader').removeClass('show')
        }
    })
}
function fillForm(pub) {
    console.log(pub);

    if (UPDATE) {
        $('#publication-form').find('input:not(.hidden)').removeClass('is-valid')
    } else {
        $('#publication-form').find('input:not(.hidden):not([type=radio]):not([type=checkbox])').val('').removeClass('is-valid')
    }
    // $('.affiliation-warning').show()

    switch (pub.type.toLowerCase()) {
        case 'journal-article':
            togglePubType('article')
            break;
        case 'magazine-article':
            togglePubType('magazine')
            break;
        case 'book-chapter':
            togglePubType('chapter')
            break;
        case 'book':
            if (pub.editors.length > 0 && pub.authors.length > 0) {
                togglePubType('chapter')
            } else if (pub.editors.length > 0) {
                togglePubType('editor')
            } else {
                togglePubType('book')
            }
            break;
        case 'software':
        case 'dataset':
        case 'report':
            togglePubType('software')
            $('#software_type option[value="' + pub.type + '"]').prop("selected", true);
            break;
        // case 'book-chapter':
        //     togglePubType('chapter')
        //     break;

        default:
            togglePubType(pub.type)
            break;
    }
    // $('#type').addClass('is-valid')
    $('#pubtype').addClass('is-valid')

    if (pub.title !== undefined) {
        $('#title').val(pub.title).addClass('is-valid')
        // quill.setText(pub.title);
        $('.title-editor .ql-editor').html("<p>" + pub.title + "</p>").addClass('is-valid')
    }

    var elements = [
        'first_authors',
        'year',
        'month',
        'day',
        'journal',
        'issn',
        'issue',
        'volume',
        'pages',
        'doi',
        'pubmed',
        'book',
        'edition',
        'publisher', ,
        'city',
        'software_venue',
        'software_version',
        'date_start',
        'software_doi'
    ]

    elements.forEach(element => {
        if (pub[element] !== undefined && !UPDATE || !isEmpty(pub[element]))
            $('#' + element).val(pub[element]).addClass('is-valid')
        // console.log($('#' + element));
    });

    if (pub.epub !== undefined && (!UPDATE || !pub.epub || !pub.epub.length))
        $('#epub').attr('checked', pub.epub).addClass('is-valid')

    if (pub.journal) {
        // TODO: prefer ISSN
        // var val = pub.issn
        $('#journal-search').val(pub.journal)
        getJournal(pub.journal)
    }

    $('.author-list').addClass('is-valid').find('.author').remove()

    var aff_undef = false
    pub.authors.forEach(function (d, i) {
        if (d.affiliation === undefined) {
            aff_undef = true
        }
        addAuthorDiv(d.family, d.given, d.affiliation ?? false)
    })
    if (pub.editors !== undefined) {
        pub.editors.forEach(function (d, i) {
            addAuthorDiv(d.family, d.given, d.affiliation ?? false, true)
        })
    }
    if (aff_undef)
        toastWarning('Not all affiliations could be parsed automatically. Please click on every ' + AFFILIATION + ' author to mark them.')

    affiliationCheck();

    toastSuccess('Bibliographic data were updated.')

}


function getPubData(event, form) {
    event.preventDefault();
    // if (form !== null) {
    //     param = $(form).serializeArray()
    //     param = objectifyForm(param)
    // }
    if ($('#search-doi').length !== 0) {
        var doi = $('#search-doi').val()
    } else {
        var doi = $('#doi').val()
    }
    getPublication(doi)
}

function getPublishingDate(pub) {
    var date = ["", "", ""];
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
    var date = ["", "", ""]
    // 2022-07-06
    if (element[0]) date[0] = element[0]

    if (element[1]) date[1] = element[1] //+= "-" + ("0" + element[1]).slice(-2)
    //else date += "-01"

    if (element[2]) date[2] = element[2] //+= "-" + ("0" + element[2]).slice(-2)
    //else date += "-01"

    // console.log(date);
    // if (element[1]) date = ("0" + element[1]).slice(-2) + "." + date
    // if (element[2]) date = ("0" + element[2]).slice(-2) + "." + date
    return date
}



function strDate(date) {
    var res = date[0];

    if (date[1] != '') res += "-" + ("0" + date[1]).slice(-2)
    else res += "-01"

    if (date[2] != '') res += "-" + ("0" + date[2]).slice(-2)
    else res += "-01"

    return res
}

function affiliationCheck() {
    $('.affiliation-warning').show()
    $('form').each(function () {
        var form = $(this)
        form.find('.author input').each(function () {
            var value = $(this).val().split(';')
            if (value[2] == 1) {
                form.find('.affiliation-warning').hide()
                return false;
            }
        })
    })
}

function addAuthorDiv(lastname, firstname, aoi = false, editor = false, el = null) {
    if (el == null) {
        if (editor) {
            el = $('#add-editor')
        } else {
            el = $('#add-author')
        }
    }
    if (lastname === undefined) lastname = ""
    if (firstname === undefined) firstname = ""
    var author = $('<div class="author">')
        .on('dblclick', function () {
            toggleAffiliation(this)
        })
        .html(lastname + ', ' + firstname);
    var val = 0
    if (aoi) {
        val = 1
        author.addClass('author-aoi')
    }
    val = lastname.trim() + ';' + firstname.trim() + ';' + val

    var classname = editor ? "editors" : "authors";
    author.append('<input type="hidden" name="values[' + classname + '][]" value="' + val + '">')
    author.append('<a onclick="removeAuthor(event, this)">&times;</a>')
    author.insertBefore(el)
}

function toggleAffiliation(item) {
    var old = $(item).find('input').val().split(';')
    if ($(item).hasClass('author-aoi')) {
        old[2] = 0
    } else {
        old[2] = 1
    }
    console.log(old);
    $(item).find('input').val(old.join(';'))
    $(item).toggleClass('author-aoi')
    affiliationCheck();
}

function addAuthor(event, el, editor = false) {
    if (event.keyCode == '13') {
        event.preventDefault();
        const match = (SCIENTISTS.indexOf(el.value) != -1)
        var value = el.value.split(',')
        if (value.length !== 2) {
            toastError('Author name must be formatted like this: Lastname, Firstname')
            return;
        }
        addAuthorDiv(value[0], value[1], match, editor, $(el))

        $(el).val('')
        affiliationCheck();
        return false;
    }
}
function removeAuthor(event, el) {
    event.preventDefault();
    $(el).parent().remove()
    affiliationCheck();
}

// function addRow2db(el) {
//     var tr = $(el).closest('tr');
//     //init data string
//     var data = {};
//     var correct = true
//     // for each input in the TR
//     tr.find(':input').each(function () {
//         //retrieve field name and value from the DOM
//         var input = $(this)
//         var field = input.attr('name');
//         if (input.attr('type') == 'checkbox') {
//             if (input.prop('checked')) {
//                 data[field] = 1;
//             }
//         } else {
//             if (input.prop('required') && !$(this).val()) {
//                 // toastError(lang('The field ' + field + ' is required.', 'Das Feld ' + field + ' wird benötigt.'))
//                 input.addClass('is-invalid');
//                 correct = false;
//             }
//             data[field] = input.val();
//         }
//     });
//     console.log("addRow2db", data);
//     if (correct && data.type !== undefined) {
//         $.ajax({
//             type: "POST",
//             data: data,
//             dataType: "html",
//             url: ROOTPATH + '/ajax/' + data.type + '.php',
//             success: function (response) {
//                 if (response.startsWith('Error')) {
//                     toastError(response)
//                 } else {
//                     // toastSuccess()
//                     tr.before(response)
//                 }
//             },
//             error: function (response) {
//                 toastError(response.responseText)
//                 $('.loader').removeClass('show')
//             }
//         })
//     }
// }

function activeButtons(type) {
    $('.select-btns').find('.btn').removeClass('active')

    $('#' + type + '-btn').addClass('active')
    switch (type) {
        case 'publication':
            $('#article-btn').addClass('active')
            break;
        case 'review':
            $('#review2-btn').addClass('active')
            break;
        case 'misc':
            $('#misc-once-btn').addClass('active')
            break;

        case 'students':
            $('#students2-btn').addClass('active')
            break;
        case 'guests':
            $('#students-btn').addClass('active')
            break;
        case 'editorial':
        case 'grant-rev':
        case 'thesis-rev':
            $('#review-btn').addClass('active')
            break;
        case 'misc-once':
        case 'misc-annual':
            $('#misc-btn').addClass('active')
            break;
        case 'article':
        case 'magazine':
        case 'book':
        case 'chapter':
        case 'preprint':
        case 'dissertation':
        case 'others':
            $('#publication-btn').addClass('active')
            break;
        default:
            break;
    }
}

function togglePubType(type) {
    type = type.trim().toLowerCase().replace(' ', '-')
    var types = {
        "journal-article": "article",
        "magazine-article": "magazine",
        "book-chapter": "chapter",
        "publication": 'article',
        'doctoral-thesis': 'students',
        'master-thesis': 'students',
        'bachelor-thesis': 'students',
        'guest-scientist': 'guests',
        'lecture-internship': 'guests',
        'student-internship': 'guests',
        'reviewer': 'review',
        'editor': 'editorial',
    }
    // if (type == "others") return;
    type = types[type] ?? type;
    console.log(type);

    activeButtons(type)
    $('#type').val(type)
    var form = $('#publication-form')

    var publications = ['article', 'book', 'chapter', 'preprint', 'magazine', 'others', 'dissertation']
    if (publications.includes(type)) {
        $('#pubtype option[value="' + type + '"]').prop("selected", true);
        $('#type').val('publication')
    }
    var reviews = ["review", "editorial", "grant-rev", "thesis-rev"]
    if (reviews.includes(type)) {
        $('#role-input option[value="' + type + '"]').prop("selected", true);
        $('#type').val('review')
    }

    var miscs = ["misc", "misc-once", "misc-annual"]
    if (miscs.includes(type)) {
        var misc = type == "misc-annual" ? 'annual' : 'once'
        $('#iteration option[value="' + misc + '"]').prop("selected", true);
        $('#type').val('misc')
    }

    var students = ["students", "guests"]
    if (students.includes(type)) {
        $('#type').val('students')
    }

    var els = form.find('[data-visible]').hide()
        .find('input,select').attr('disabled', true)

    els.each(function () {
        var el = $(this)
        var vis = el.closest('[data-visible]').attr('data-visible')
        if (vis.includes(type)) {
            // console.log(el.attr('name'), vis, el);
            el.attr('disabled', false)
        }
    })

    form.find('[data-visible*=' + type + ']').show()
    //     .find(':not([data-visible]),[data-visible*=' + type + ']')
    //     .find('input,select').attr('disabled', false)



    form.find('[data-visible*=' + type + '] > input, [data-visible*=' + type + '] > select').attr('disabled', false)
    form.slideDown()
}

function todo() {
    digidive.initStickyAlert({
        content: lang('Sorry, but this button does not work yet.', 'Sorry, aber der Knopf funktioniert noch nicht.'),
        title: '<i class="fa-solid fa-face-shush fa-3x text-signal"></i>',
        alertType: "",
        hasDismissButton: true
    })
}

function loadModal(path, data = {}) {
    $.ajax({
        type: "GET",
        dataType: "html",
        data: data,
        url: ROOTPATH + '/' + path,
        success: function (response) {
            $('#modal-content').html(response)
            $('#the-modal').addClass('show')


            if ($('#the-modal .title-editor').length !== 0) {
                var quill = new Quill('#the-modal .title-editor', {
                    modules: {
                        toolbar: [
                            ['italic', 'underline']
                        ]
                    },
                    formats: ['italic', 'underline'],
                    placeholder: '',
                    theme: 'snow' // or 'bubble'
                });
                quill.on('text-change', function (delta, oldDelta, source) {
                    var delta = quill.getContents()
                    console.log(delta);
                    var str = ""
                    delta.ops.forEach(el => {
                        if (el.attributes !== undefined) {
                            if (el.attributes.bold) str += "<b>";
                            if (el.attributes.italic) str += "<i>";
                            if (el.attributes.underline) str += "<u>";
                        }
                        str += el.insert;
                        if (el.attributes !== undefined) {
                            if (el.attributes.underline) str += "</u>";
                            if (el.attributes.italic) str += "</i>";
                            if (el.attributes.bold) str += "</b>";
                        }
                    });
                    $('#the-modal #title').val(str)
                });
            }
        },
        error: function (response) {
            console.log(response);
            toastError(response.responseText)
            $('.loader').removeClass('show')
        }
    })
}

// function toggleEditForm(collection, id) {
//     loadModal('form/' + collection + '/' + id);

// }


function filter_results(input) {
    var table = $('#result-table')
    if (table.length == 0) return;
    var rows = table.find('tbody > tr')
    if (input.length == 0) {
        rows.show();
        return
    }
    rows.hide()
    var data = input.split(" ");
    $.each(data, function (i, v) {
        // workaround: ignore button content (unbreakable)
        rows.find('td:not(.unbreakable)').filter(":contains('" + v + "')").parent().show();
    });
}


function verifyForm(event, form) {
    // event.preventDefault()
    form = $(form)
    correct = true
    form.find(':input').each(function () {
        //retrieve field name and value from the DOM
        var input = $(this)
        var selector = input
        if (input.attr('id') == 'title') {
            selector = $('.title-editor')
        }
        if ((input.prop('required') && !input.prop('disabled'))) {

            console.log(input);
            if (!$(this).val()) {
                selector.addClass('is-invalid')
                // .on('input', function(){
                //     if (this.value !== '') selector.addClass('is-valid').removeClass('is-invalid')
                //     else selector.addClass('is-invalid').removeClass('is-valid')
                // })
                correct = false;
            } else {
                selector.addClass('is-valid').removeClass('is-invalid');
            }
        }
    });

    // check if authors are defined
    if ($('.author-list').find('.author').length === 0) {
        $('.author-list').addClass('is-invalid').removeClass('is-valid')
        correct = false
    } else {
        $('.author-list').addClass('is-valid').removeClass('is-invalid')
    }

    if (correct) return true

    event.preventDefault()
    return false
}

function updateCart(add = true) {
    var cart = $('#cart-counter')
    var counter = cart.html()
    if (add) {
        counter++;
    } else {
        counter--;
    }
    cart.html(counter)
    if (counter == 0) {
        cart.addClass('hidden')
    } else {
        cart.removeClass('hidden')
    }
}

function addToCart(el, id) {//.addClass('animate__flip')
    // document.cookie = "username=John Doe; expires=Thu, 18 Dec 2013 12:00:00 UTC"; 
    var fav = digidive.readCookie('osiris-cart')
    if (fav) {
        var favlist = fav.split(',')
        console.log(favlist);
        const index = favlist.indexOf(id);
        if (index > -1) {
            favlist.splice(index, 1);
            console.info("remove");
            updateCart(false)
        } else {
            if (favlist.length > 30) {
                toastError(lang('You can have no more than 30 items in your cart.', 'Du kannst nicht mehr als 30 Aktivitäten in deinem Einkaufswagen haben.'))
                return;
            }
            favlist.push(id)
            console.info("add");
            updateCart(true)
        }
        fav = favlist.join(',')
    } else {
        fav = id
        console.info("add");
        updateCart(true)
    }
    digidive.createCookie('osiris-cart', fav, 30)
    if (el === null) {
        location.reload()
    } else {
        $(el).find('i').toggleClass('fas').toggleClass('fal').toggleClass('text-success')
    }
    // setTimeout(function () {
    //     $(el).find('i').removeClass('animate__flip')
    //     // animate__headShake
    // }, 1000)
}