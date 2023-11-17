
$.extend($.fn.DataTable.ext.classes, {
    sPaging: "pagination mt-10 ",
    sPageFirst: "direction ",
    sPageLast: "direction ",
    sPagePrevious: "direction ",
    sPageNext: "direction ",
    sPageButtonActive: "active ",
    sFilterInput: "form-control sm d-inline w-auto ml-10 ",
    sLengthSelect: "form-control sm d-inline w-auto",
    sInfo: "float-right text-muted",
    sLength: "float-right"
});



function navigate(key) {
    $('section').hide()
    $('section#' + key).show()

    $('.pills .btn').removeClass('active')
    $('.pills .btn#btn-' + key).addClass('active')

    switch (key) {
        case 'publications':
            if (publicationTable) break;
            initPublications()
            impactfactors()
            authorrole()
            break;

        case 'activities':
            if (activitiesTable) break;
            initActivities()
            activitiesChart()
        default:
            break;
    }

}


var publicationTable;
function initPublications() {
    publicationTable = $('#publication-table').DataTable({
        "ajax": {
            "url": ROOTPATH + '/api/all-activities',
            "data": {
                page: 'my-activities',
                display_activities: 'web',
                user: CURRENT_USER,
                type: 'publication'
            },
            dataSrc: 'data'
        },
        deferRender: true,
        pageLength: 5,
        columnDefs: [
            {
                targets: 0,
                data: 'type',
                // className: 'w-50'
            },
            {
                targets: 1,
                data: 'activity'
            },
            {
                targets: 2,
                data: 'links',
                className: 'unbreakable'
            },
            {
                targets: 3,
                data: 'search-text',
                searchable: true,
                visible: false,
            },
            {
                targets: 4,
                data: 'start',
                searchable: true,
                visible: false,
            },
        ],
        "order": [
            [4, 'desc'],
            // [0, 'asc']
        ]
    });
}


var activitiesTable;
function initActivities() {
    activitiesTable = $('#activities-table').DataTable({
        "ajax": {
            "url": ROOTPATH + '/api/all-activities',
            "data": {
                page: 'my-activities',
                display_activities: 'web',
                user: CURRENT_USER,
                type: { '$ne': 'publication' }
            },
            dataSrc: 'data'
        },
        deferRender: true,
        pageLength: 5,
        columnDefs: [
            {
                targets: 0,
                data: 'type',
                // className: 'w-50'
            },
            {
                targets: 1,
                data: 'activity'
            },
            {
                targets: 2,
                data: 'links',
                className: 'unbreakable'
            },
            {
                targets: 3,
                data: 'search-text',
                searchable: true,
                visible: false,
            },
            {
                targets: 4,
                data: 'start',
                searchable: true,
                visible: false,
            },
        ],
        "order": [
            [4, 'desc'],
            // [0, 'asc']
        ]
    });
}



function impactfactors() {
    $.ajax({
        type: "GET",
        url: ROOTPATH + "/api/dashboard/impact-factor-hist",
        data: { user: CURRENT_USER },
        dataType: "json",
        success: function (response) {
            console.log(response);
            var container = document.getElementById('chart-impact')
            if (response.count == 0) {
                container.classList.add('hidden')
                return;
            }
            var ctx = document.getElementById('chart-impact-canvas')
            var data = response.data;

            var labels = data.labels;
            var colors = [
                '#006EB795',
            ]
            var i = 0

            console.log(labels);
            var data = {
                type: 'bar',
                options: {
                    plugins: {
                        legend: {
                            display: false,
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                title: (items) => {
                                    if (!items.length) {
                                        return '';
                                    }
                                    const item = items[0];
                                    const x = item.parsed.x;
                                    const min = x;
                                    const max = x + 1;
                                    return `IF: ${min} - ${max}`;
                                }
                            }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'linear',
                            ticks: {
                                stepSize: 1
                            },
                            stacked: true,
                            title: {
                                display: true,
                                text: lang('Impact factor', 'Impact factor')
                            },
                        },
                        y: {
                            title: {
                                display: true,
                                text: lang('Number of publications', 'Anzahl Publikationen')
                            },
                            ticks: {
                                callback: function (value, index, ticks) {
                                    // only show full numbers
                                    if (Number.isInteger(value)) {
                                        return value
                                    }
                                    return "";
                                }
                            }
                        }
                    },
                },
                data: {
                    labels: data.x,
                    datasets: [{
                        data: data.y,
                        backgroundColor: colors[i++],
                        borderWidth: 1,
                        borderColor: '#464646',
                        borderRadius: 4
                    },],
                }
            }


            console.log(data);
            var myChart = new Chart(ctx, data);
        },
        error: function (response) {
            console.log(response);
        }
    });
}




function authorrole() {
    $.ajax({
        type: "GET",
        url: ROOTPATH + "/api/dashboard/author-role",
        data: { user: CURRENT_USER },
        dataType: "json",
        success: function (response) {
            console.log(response);
            var container = document.getElementById('chart-authors')
            if (response.count == 0) {
                container.classList.add('hidden')
                return;
            }
            var data = response.data;
            var ctx = document.getElementById('chart-authors-canvas')
            var myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: '# of Scientists',
                        data: data.y,
                        backgroundColor: data.colors,
                        borderColor: '#464646', //'',
                        borderWidth: 1,
                    }]
                },
                plugins: [ChartDataLabels],
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            display: true,
                        },
                        title: {
                            display: false,
                            text: 'Scientists approvation'
                        },
                        datalabels: {
                            color: 'black',
                            // anchor: 'end',
                            // align: 'end',
                            // offset: 10,
                            font: {
                                size: 20
                            }
                        }
                    },
                }
            });
        },
        error: function (response) {
            console.log(response);
        }
    });
}

function activitiesChart() {
    $.ajax({
        type: "GET",
        url: ROOTPATH + "/api/dashboard/activity-chart",
        data: { user: CURRENT_USER },
        dataType: "json",
        success: function (response) {
            console.log(response);
            var container = document.getElementById('chart-activities')
            if (response.count == 0) {
                container.classList.add('hidden')
                return;
            }
//             var raw_data = Object.values({"2023":{"x":"2023","publication":20,"poster":3,"lecture":8,"review":6,"students":6,"teaching":6,"software":1,"award":0,"misc":26,"Hund":0},"2022":{"x":"2022","publication":23,"poster":2,"lecture":1,"review":5,"students":3,"teaching":0,"software":1,"award":0,"misc":9,"Hund":0},"2021":{"x":"2021","publication":17,"poster":0,"lecture":0,"review":0,"students":0,"teaching":0,"software":0,"award":0,"misc":0,"Hund":0},"2020":{"x":"2020","publication":8,"poster":0,"lecture":0,"review":0,"students":0,"teaching":0,"software":0,"award":0,"misc":0,"Hund":0}});
                    
// var olc = [{
//     label: 'Publications',
//     data: raw_data,
//     parsing: {
//         yAxisKey: 'publication'
//     },
//     backgroundColor: '#006eb795',
//     borderColor: '#464646', //'#006eb7',
//     borderWidth: 1
// },
// {
//     label: 'Poster',
//     data: raw_data,
//     parsing: {
//         yAxisKey: 'poster'
//     },
//     backgroundColor: '#b61f2995',
//     borderColor: '#464646', //'#b61f29',
//     borderWidth: 1
// },
// {
//     label: 'Lectures',
//     data: raw_data,
//     parsing: {
//         yAxisKey: 'lecture'
//     },
//     backgroundColor: '#ecaf0095',
//     borderColor: '#464646', //'#ecaf00',
//     borderWidth: 1
// },]
            
            var dataset = response.data;
            // console.log(raw_data);
            // var datasets = []
            // var labels = []
           
            var ctx = document.getElementById('chart-activities-canvas')

            var data = {
                type: 'bar',
                options: {
                    plugins: {
                        title: {
                            display: false,
                            text: 'All activities'
                        },
                        legend: {
                            display: true,
                        }
                    },
                    responsive: true,
                    scales: {
                        x: {
                            stacked: false,
                            title: {
                                display: true,
                                text: lang('Years', 'Jahre')
                            }
                        },
                        y: {
                            stacked: false,
                            ticks: {
                                callback: function (value, index, ticks) {
                                    // only show full numbers
                                    if (Number.isInteger(value)) {
                                        return value
                                    }
                                    return "";
                                }
                            },
                            title: {
                                display: true,
                                text: lang('Number of activities', 'Anzahl der Aktivitäten')
                            }
                        }
                    },
                    maintainAspectRatio: false,
                    onClick: (e) => {
                        const canvasPosition = Chart.helpers.getRelativePosition(e, activityChart);
                        // Substitute the appropriate scale IDs
                        const dataX = activityChart.scales.x.getValueForPixel(canvasPosition.x);
                        const dataY = activityChart.scales.y.getValueForPixel(canvasPosition.y);
                        window.location = ROOTPATH + "/my-year/" + CURRENT_USER + "?year=" + dataset.labels[dataX]
                    }
                },
                data: {
                    labels: dataset.labels,
                    datasets: dataset.data,
                    // grouped:true
                },
            }


            console.log(data);
            var activityChart = new Chart(ctx, data);
        },
        error: function (response) {
            console.log(response);
        }
    });
}