var activitiesTable = false,
    publicationTable = false,
    projectsExists = false,
    coauthorsExists = false,
    conceptsExists = false,
    wordcloudExists = false;
    
function navigate(key) {
    $('section').hide()
    $('section#' + key).show()

    $('.pills .btn').removeClass('active')
    $('.pills .btn#btn-' + key).addClass('active')

    switch (key) {
        case 'publications':
            if (publicationTable) break;
            publicationTable = true;
            initActivities('#publication-table', {
                page: 'my-activities',
                display_activities: 'web',
                user: CURRENT_USER,
                type: 'publication'
            })
            impactfactors('chart-impact', 'chart-impact-canvas', { user: CURRENT_USER })
            authorrole('chart-authors', 'chart-authors-canvas', { user: CURRENT_USER })
            break;

        case 'activities':
            if (activitiesTable) break;
            activitiesTable = true;
            initActivities('#activities-table', {
                page: 'my-activities',
                display_activities: 'web',
                user: CURRENT_USER,
                type: { '$ne': 'publication' }
            })
            activitiesChart('chart-activities', 'chart-activities-canvas', { user: CURRENT_USER })
            break;

        case 'projects':
            if (projectsExists) break;
            projectsExists = true;
            projectTimeline('#project-timeline', { user: CURRENT_USER })
            break;

        case 'coauthors':
            if (coauthorsExists) break;
            coauthorsExists = true;
            coauthorNetwork('#chord', { user: CURRENT_USER })
            break;

        case 'concepts':
            if (conceptsExists) break;
            conceptsExists = true;
            conceptTooltip()
            break;

        case 'wordcloud':
            if (wordcloudExists) break;
            wordcloudExists = true;
            wordcloud('#wordcloud-chart', { user: CURRENT_USER })
            break;
        case 'general': 
            break;
        default:
            $('section#news').show()
            break;
    }

    // save as hash
    window.location.hash = 'section-'+key
}


$(document).ready(function () {
     // get hash
     var hash = window.location.hash
     if (hash) {
         navigate(hash.replace('#section-', ''))
     }
});

function conferenceToggle(el, id, type='interests') {
    // ajax call to update user's conference interests
    $.ajax({
        url: ROOTPATH+'/ajax/conferences/toggle-interest',
        type: 'POST',
        data: { type: type, conference: id },
        success: function (data) {
            if (data) {
                $('#conference-' + type).toggleClass('active')
                $(el).toggleClass('active')
                $(el).find('b').html(data)
            }

        }
    })
}
