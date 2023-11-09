<h2>
    <?= lang('Open Access') ?>
</h2>

<div class="row row-eq-spacing mt-0">
    <div class="col-md-6">
        <div class="box">
            <div class="chart content text-center">
                <h5 class="title mb-0">Open Access <?= lang('en', 'Verteilung') ?></h5>
                <p class="mt-0 text-muted"><?= lang('Status according to Unpaywall since ', 'Status nach Unpaywall seit ') . $Settings->get('startyear') ?></p>

                <div id="chart-oa-status-pie"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box">
            <div class="chart content text-center">
                <h5 class="title mb-0">Open Access <?= lang('Development', 'Entwicklung') ?></h5>
                <p class="mt-0 text-muted"><?= lang('Status according to Unpaywall', 'Status nach Unpaywall') ?></p>

                <div id="chart-oa-status-line"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.plot.ly/plotly-2.27.0.min.js" charset="utf-8"></script>
    <script>
        $.ajax({
            type: "GET",
            url: ROOTPATH + "/api/dashboard/oa-status",
            dataType: "json",
            success: function(response) {
                const COLORS = {
                    gold: '#ECAF00',
                    closed: '#B51A26',
                    bronze: '#cd7f31',
                    hybrid: '#4C96CB',
                    green: '#1FA138',
                }

                var piechart = [{
                    values: [],
                    labels: [],
                    type: 'pie',
                    hole: .4,
                    marker: {
                        colors: [],
                        line: {
                            color: 'black',
                            width: 1,
                        }
                    },

                }];

                var linechart = []

                response.data.forEach(a => {
                    var accumulated = 0
                    var trace = {
                        name: a['_id'],
                        x: [],
                        y: [],
                        line: {
                            color: COLORS[a['_id']],
                            // dash: 'dash',
                        }
                    };
                    a.data.forEach(b => {
                        trace.x.push(b.year)
                        trace.y.push(b.count)
                        accumulated += b.count
                    })
                    linechart.push(trace)

                    piechart[0].labels.push(a['_id'])
                    piechart[0].values.push(accumulated)
                    piechart[0].marker.colors.push(COLORS[a['_id']] + '80')
                })

                Plotly.newPlot('chart-oa-status-pie', piechart, {
                    paper_bgcolor: 'transparent',
                    plot_bgcolor: 'transparent',
                    height: 300,
                    margin: {
                        t: 25,
                        r: 20,
                        l: 20,
                        b: 20
                    },
                });
                Plotly.newPlot('chart-oa-status-line', linechart, {
                    xaxis: {
                        type: 'category',
                    },
                    paper_bgcolor: 'transparent',
                    plot_bgcolor: 'transparent',
                    height: 300,
                    margin: {
                        t: 25,
                        r: 20,
                        l: 20,
                        b: 20
                    },
                });
            }
        });
    </script>

</div>

<!-- 
<div id="myDiv"></div>

<script>
    var data = [{

        type: 'scattergeo',

        mode: 'markers',

        locations: ['FRA', 'DEU', 'RUS', 'ESP'],

        marker: {

            size: [20, 30, 15, 10],

            color: [10, 20, 40, 50],

            cmin: 0,

            cmax: 50,

            colorscale: 'Greens',

            colorbar: {

                title: 'Some rate',

                ticksuffix: '%',

                showticksuffix: 'last'

            },

            line: {

                color: 'black'

            }

        },

        name: 'europe data'

    }];


    var layout = {

        'geo': {

            // 'scope': 'europe',

            // 'resolution': 50,
            projection: {

                type: 'robinson'

            }
        }

    };


    Plotly.newPlot('myDiv', data, layout);
</script> -->