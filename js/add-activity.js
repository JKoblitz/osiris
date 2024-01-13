
let SETTINGS = {};
const TYPES = {
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
    "monograph": "book",
    "misc": "misc-annual",
    "edited-book": "book"
}

function toggleExamples(subtype) {
    $('#examples').find('[data-visible]').hide()
    const vis = $('#examples').find('[data-visible="' + subtype + '"]')
    if (vis.length === 0) {
        $('#examples').find('[data-visible="none"]').show()
    } else {
        vis.show()
    }
}

fetch(ROOTPATH + '/settings')
    .then((response) => response.json())
    .then((json) => SETTINGS = json);

function togglePubType(type, callback = () => { }) {
    type = type.trim().toLowerCase().replace(' ', '-')
    type = TYPES[type] ?? type;
    console.log(type);

    $('#type').val(type)
    $('#type-description').empty()

    if (SETTINGS.activities === undefined) {
        fetch(ROOTPATH + '/settings?v='+Math.random().toString(16).slice(2))
            .then((response) => response.json())
            .then((json) => {
                SETTINGS = json;
                togglePubType(type, callback);
            });
        return;

    }

    // select data
    let SELECTED_TYPE = null;
    let SELECTED_SUBTYPE = null;

    var setActivities = {};

    if (Array.isArray(SETTINGS.activities)) {
        // convertArrayToObject()
        for (let i = 0; i < SETTINGS.activities.length; i++) {
            SELECTED_TYPE = SETTINGS.activities[i];
            if (SELECTED_TYPE.display && SELECTED_TYPE.subtypes !== undefined)
                SELECTED_SUBTYPE = SELECTED_TYPE.subtypes.find(x => x.id == type)
            if (SELECTED_SUBTYPE !== undefined && SELECTED_SUBTYPE !== null) {
                break;
            }
        }
    } else {
        for (const key in SETTINGS.activities) {
            SELECTED_TYPE = SETTINGS.activities[key];
            // console.log(SELECTED_TYPE);
            // if ((SELECTED_TYPE.display ?? true) && SELECTED_TYPE.subtypes !== undefined)
            //     SELECTED_SUBTYPE = SELECTED_TYPE.subtypes.find(x => x.id == type)
            // if (SELECTED_SUBTYPE !== undefined && SELECTED_SUBTYPE !== null) {
            //     break;
            // }
            if (SELECTED_TYPE.subtypes[type] !== undefined) {
                SELECTED_SUBTYPE = SELECTED_TYPE.subtypes[type];
                break;
            }
        }
        // SELECTED_TYPE = SETTINGS.activities[type];
    }
    console.log(SELECTED_TYPE);
    if (SELECTED_TYPE === null || SELECTED_SUBTYPE === null) {
        toastError('Selected activity type does not exist in settings.json.<br> If the activity is new, maybe a reload without cache is needed.')
        return
    }
    const SELECTED_MODULES = SELECTED_SUBTYPE.modules
    console.log(SELECTED_SUBTYPE);

    $('#type').val(SELECTED_TYPE.id)
    $('#subtype').val(SELECTED_SUBTYPE.id)
    // toggleExamples(SELECTED_SUBTYPE.id)
    var descr = ""
    descr += lang(SELECTED_SUBTYPE.description??'', SELECTED_SUBTYPE.description_de??'')
    if (descr != '') descr = "<i class='ph ph-info'></i> "+descr
    $('#type-description').html(descr)

    // show correct subtype buttons
    var form = $('#publication-form')
    form.find('.select-btns')
        .hide()
    form.find('.select-btns[data-type="' + SELECTED_TYPE.id + '"]')
        .show()

    $('.select-btns')
        .find('.btn').removeClass('active')
    $('.select-btns')
        .find('.btn[data-subtype="' + SELECTED_SUBTYPE.id + '"],.btn[data-type="' + SELECTED_TYPE.id + '"]')
        .addClass('active')

    $.ajax({
        type: "GET",
        url: ROOTPATH + "/get-modules",
        data: {
            id: ID,
            modules: SELECTED_MODULES,
            copy: COPY ?? false
        },
        dataType: "html",
        success: function (response) {
            // console.log(response);
            $('#data-modules').html(response)
            // if (SELECTED_MODULES.includes('title')) {
            $('.title-editor').each(function (el) {
                var element = this;
                // initQuill(element)


                var authordiv = $('.author-list')
                if (authordiv.length > 0) {
                    authordiv.sortable({});
                }
            })
            // }

            callback()
            console.log('TEST');
            $('#data-modules').find(':input').on('change', function(){console.log('test');doubletCheck()})
        }
    });


    // first hide all modules
    // var modules = $('#data-modules')
    // modules.find('[data-module]').hide()
    //     .find('input,select').attr('disabled', true)

    // // then show selected modules and enable data fields within
    // SELECTED_MODULES.forEach(name => {
    //     modules.find('[data-module~=' + name + ']').show()
    //         .find('input,select').attr('disabled', false)
    // });

    // show form    
    form.slideDown()
    return;

}

const convertArrayToObject = (array, key) => {
    if (!Array.isArray(array)) return array;
    const initialValue = {};
    return array.reduce((obj, item) => {
        return {
            ...obj,
            [item[key]]: item,
        };
    }, initialValue);
};


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

