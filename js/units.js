var activitiesTable = false,
    publicationTable = false,
    projectsExists = false,
    coauthorsExists = false,
    conceptsExists = false,
    collabExists = false,
    personsExists = false,
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
                user: {'$in': USERS},
                type: 'publication'
            })
            // impactfactors('chart-impact', 'chart-impact-canvas', { user: {'$in': USERS} })
            // authorrole('chart-authors', 'chart-authors-canvas', { user: {'$in': USERS} })
            break;

        case 'activities':
            if (activitiesTable) break;
            activitiesTable = true;
            initActivities('#activities-table', {
                page: 'my-activities',
                display_activities: 'web',
                user: {'$in': USERS},
                type: { '$ne': 'publication' }
            })
            // activitiesChart('chart-activities', 'chart-activities-canvas', { user: {'$in': USERS} })
            break;

        case 'projects':
            // if (projectsExists) break;
            // projectsExists = true;
            // projectTimeline('#project-timeline', { user: {'$in': USERS} })
            break;

        // case 'coauthors':
        //     if (coauthorsExists) break;
        //     coauthorsExists = true;
        //     coauthorNetwork('#chord', { user: {'$in': USERS} })
        //     break;

        case 'persons':
            if (personsExists) break;
            personsExists = true;
            userTable('#user-table', {
                filter: {depts: {'$in': DEPT_TREE}, 'is_active':true},
                subtitle: 'position'
            })
            break;

        case 'collab':
            if (collabExists) break;
            collabExists = true;
            collabChart('#collab-chart', {
                type: 'publication',
                dept: DEPT,
            })
            break;
    
        case 'concepts':
            if (conceptsExists) break;
            conceptsExists = true;
            conceptTooltip()
            break;

        case 'wordcloud':
            if (wordcloudExists) break;
            wordcloudExists = true;
            wordcloud('#wordcloud-chart', { user: {'$in': USERS} })
            break;
        default:
            break;
    }

}


function collabChart(selector, data) {
    $.ajax({
        type: "GET",
        url: ROOTPATH + "/api/dashboard/department-network",
        data: data,
        dataType: "json",
        success: function(response) {
            console.log(response);
            // if (response.count <= 1) {
            //     $('#collab').hide()
            //     return
            // }
            var matrix = response.data.matrix;
            var data = response.data.labels;

            var labels = [];
            var colors = [];
            data = Object.values(data)
            data.forEach(element => {
                labels.push(element.id);
                colors.push(element.color)
            });


            Chords(selector, matrix, labels, colors, data, links = false, useGradient = true, highlightFirst = false, type = 'publication');
        },
        error: function(response) {
            console.log(response);
        }
    });
}
    