
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
            break;

        case 'projects':
            projectTimeline()
            break;

        case 'coauthors':
            coauthorNetwork()
            break;

        case 'concepts':
            conceptTooltip()
            break;
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
                data: 'icon',
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
                data: 'icon',
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

            var dataset = response.data;

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

projectTimelineExists = false;
function projectTimeline() {
    if (projectTimelineExists) return;
    projectTimelineExists = true
    $.ajax({
        type: "GET",
        url: ROOTPATH + "/api/dashboard/project-timeline",
        data: { user: CURRENT_USER },
        dataType: "json",
        success: function (response) {
            console.log(response);
            var events = []

            const CURRENT_YEAR = new Date().getFullYear();
            var startyear = CURRENT_YEAR
            var endyear = CURRENT_YEAR

            response.data.forEach(element => {
                var s = element.start
                var start = new Date(s.year, s.month, s.day)
                if (start.getFullYear() < startyear)
                    startyear = start.getFullYear()

                var e = element.end
                var end = new Date(e.year, e.month, e.day)
                if (end.getFullYear() > endyear)
                    endyear = end.getFullYear()

                events.push({
                    startdate: start,
                    enddate: end,
                    title: element.name,
                    role: element.persons.role,
                    funder: element.funder
                })
            });

            var radius = 3,
                distance = radius * 2 + 2,
                divSelector = '#project-timeline'

            var margin = {
                top: 8,
                right: 25,
                bottom: 30,
                left: 25
            },
                width = 600,
                // height = (distance * types.length) + margin.top + margin.bottom;
                height = (distance * response.count) - distance + margin.top + margin.bottom;


            var svg = d3.select(divSelector).append('svg')
                .attr("viewBox", `0 0 ${width} ${height}`)

            width = width - margin.left - margin.right
            height = height - margin.top - margin.bottom;

            var timescale = d3.scaleTime()
                .domain([new Date(startyear, 0, 1), new Date(endyear, 12, 1)])
                .range([0, width]);

            const typeInfo = {
                'PI': { color: '#f78104', label: lang('Pi', 'PI') },
                'worker': { color: '#008083', label: lang('Worker', 'Projektmitarbeiter:in') },
                'associate': { color: '#AAAAAA', label: lang('Associate', 'Beteiligte Person') },
            }

            var axisBottom = d3.axisBottom(timescale)
                .ticks(12)
            // .tickPadding(5).tickSize(20);
            svg.append('g').attr('class', 'axes')
                .attr('transform', `translate(${margin.left}, ${height + margin.top + radius * 2})`)
                .call(axisBottom);

            var quarter = svg.append('g')
                .attr('transform', `translate(${margin.left - 6}, ${height + margin.top + radius * 2})`)
            // .selectAll("g")

            quarter.append('rect')
                .style("fill", 'rgb(236, 175, 0)')
                // .attr('height', height+margin.top+radius*4)
                .attr('height', 8)
                .attr('width', function (d, i) {
                    var date = new Date(CURRENT_YEAR, 1, 1)
                    var x1 = timescale(date)
                    var date = new Date(CURRENT_YEAR, 12, 31)
                    var x2 = timescale(date)
                    return x2 - x1
                })
                .style('opacity', .2)
                .attr('x', (d) => {
                    var date = new Date(CURRENT_YEAR, 1, 1)
                    return timescale(date)
                })
                // .attr('y', radius*-2)
                .attr('y', 0)

            quarter.append('text')
                .attr('x', (d) => {
                    var date = new Date(CURRENT_YEAR, 1, 1)
                    var x1 = timescale(date)
                    var date = new Date(CURRENT_YEAR, 12, 31)
                    var x2 = timescale(date)
                    return x1 + (x2 - x1) / 2
                })
                .attr('y', 6)
                .attr('text-anchor', 'middle')
                .style('fill', 'rgb(165, 122, 0)')
                .style('font-size', "5px")
                .html(lang('Current year', 'Aktuelles Jahr'))


            d3.selectAll("g>.tick>text")
                .each(function (d, i) {
                    d3.select(this).style("font-size", "8px");
                });

            var Tooltip = d3.select(divSelector)
                .append("div")
                .style("opacity", 0)
                .attr("class", "tooltip")
                .style("background-color", "white")
                .style("border", "solid")
                .style("border-width", "2px")
                .style("border-radius", "5px")
                .style("padding", "5px")


            function mouseover(d, i) {

                d3.select(this)
                    .select('circle,rect')
                    .transition()
                    .duration(300)
                    .style('opacity', 1)

                //Define and show the tooltip over the mouse location
                $(this).popover({
                    placement: 'auto top',
                    container: divSelector,
                    mouseOffset: 10,
                    followMouse: true,
                    trigger: 'hover',
                    html: true,
                    content: function () {
                        var role = '';
                        console.log(d.role);
                        if (typeInfo[d.role]) {
                            role = `<span style="color:${typeInfo[d.role].color}">${typeInfo[d.role].label}</span>`
                        }
                        return `${d.title ?? 'No title available'}<br>${d.funder}<br>${role}`
                    }
                });
                $(this).popover('show');
            } //mouseoverChord

            //Bring all chords back to default opacity
            function mouseout(event, d) {
                d3.select(this).select('circle,rect')
                    .transition()
                    .duration(300)
                    .style('opacity', .5)
                //Hide the tooltip
                $('.popover').each(function () {
                    $(this).remove();
                });
            }

            var eventGroup = svg.append('g')
                .attr('transform', `translate(${margin.left}, ${margin.top})`)
                .selectAll("g")
                .data(events)
                .enter().append("g")
                .attr('transform', function (d, i) {
                    var date = d.startdate
                    var x = timescale(date)
                    var y = i * distance
                    return `translate(${x}, ${y})`
                })

            eventGroup.on("mouseover", mouseover)
                .on("mouseout", mouseout)

            var lines = eventGroup.append('rect')
                .style("fill", function (d, i) {
                    return typeInfo[d.role].color
                })
                .attr('height', radius * 2)
                .attr('width', function (d, i) {
                    var date = d.startdate
                    var x1 = timescale(date)
                    var date = d.enddate
                    var x2 = timescale(date)
                    return x2 - x1
                })
                .style('opacity', .6)
                .attr('rx', 3)
                .attr('y', -radius)
        },
        error: function (response) {
            console.log(response);
        }
    });
}

coauthorNetworkExists = false;
function coauthorNetwork() {
    if (coauthorNetworkExists) return;
    coauthorNetworkExists = true
    $.ajax({
        type: "GET",
        url: ROOTPATH + "/api/dashboard/author-network",
        data: {
            user: CURRENT_USER
        },
        dataType: "json",
        success: function (response) {
            console.log(response);
            var matrix = response.data.matrix;
            var DEPTS = response.data.labels;

            var data = Object.values(DEPTS);
            var labels = data.map(item => item['name']);

            var colors = []
            var links = []
            var depts_in_use = {};

            data.forEach(function (d, i) {
                colors.push(d.dept.color ?? '#cccccc');
                var link = null
                if (i !== 0) link = ROOTPATH + "/profile/" + d.user
                links.push(link)

                if (d.dept.id && depts_in_use[d.dept.id] === undefined)
                    depts_in_use[d.dept.id] = d.dept;
            })

            Chords('#chord', matrix, labels, colors, data, links, false, DEPTS[CURRENT_USER]['index']);


            var legend = d3.select('#legend')
                .append('div').attr('class', 'content')

            legend.append('div')
                .style('font-weight', 'bold')
                .attr('class', 'mb-5')
                .text(lang("Departments", "Abteilungen"))

            for (const dept in depts_in_use) {
                if (Object.hasOwnProperty.call(depts_in_use, dept)) {
                    const d = depts_in_use[dept];
                    var row = legend.append('div')
                        .attr('class', 'd-flex mb-5')
                        .style('color', d.color)
                    row.append('div')
                        .style('background-color', d.color)
                        .style("width", "2rem")
                        .style("height", "2rem")
                        .style("border-radius", ".5rem")
                        .style("display", "inline-block")
                        .style("margin-right", "1rem")
                    row.append('span').text(d.name)
                }
            }

        },
        error: function (response) {
            console.log(response);
        }
    });
}


conceptTooltipExists = false;
function conceptTooltip() {
    if (conceptTooltipExists) return;
    conceptTooltipExists = true
    $('.concept').each(function () {
        var el = $(this)
        var data = {
            score: el.attr('data-score'),
            name: el.attr('data-name'),
            count: el.attr('data-count'),
            wikidata: el.attr('data-wikidata'),
        }
        el.popover({
            placement: 'auto bottom',
            container: '#concepts',
            mouseOffset: 10,
            trigger: 'click',
            html: true,
            content: function () {
                var label = lang('Activities', 'Aktivitäten')
                if (data.count == 1) label = lang('Activity', 'Aktivität');
                return `<b>${data.name}</b><br>
                    Score: ${data.score} %</br>
                    In ${data.count} ${label}<br>
                    <hr>
                    <a href="${ROOTPATH}/concepts/${data.name}" target="_blank" rel="noopener noreferrer"><i class="ph ph-arrow-right"></i> Concept page</a><br>
                    <a href="${data.wikidata}" target="_blank" rel="noopener noreferrer"><i class="ph ph-arrow-up-right"></i> Wikidata</a>
                    `;
            }
        });
    }
    );
}