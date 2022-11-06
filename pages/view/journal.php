<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>


<table class="table" id="result-table">
    <tr>
        <td>ID</td>
        <td><?= $data['_id'] ?></td>
    </tr>
    <tr>
        <td>Journal</td>
        <td><?= $data['journal'] ?></td>
    </tr>
    <tr>
        <td><?= lang('Abbreviated', 'Abgekürzt') ?></td>
        <td><?= $data['abbr'] ?></td>
    </tr>
    <tr>
        <td>Publisher</td>
        <td><?= $data['publisher'] ?? '' ?></td>
    </tr>
    <tr>
        <td>ISSN</td>
        <td><?= implode(', ', $data['issn']->bsonSerialize()) ?></td>
    </tr>
</table>
<?php
$impacts = $data['impact'];
$years = array_column((array) $impacts, 'year');
// dump($impacts);
?>


<div class="box">
    <div class="chart content">
        <h5 class="title "><?= lang('Impact factors', 'Impact-Faktoren') ?></h5>
        <canvas id="chart-if" style="max-height: 400px;"></canvas>
    </div>
</div>

<script>
    var barChartConfig = {
        type: 'bar',
        data: [],
        options: {
            plugins: {
                title: {
                    display: false,
                    text: 'Chart'
                },
                legend: {
                    display: false,
                }
            },
            responsive: true,
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true,
                }
            }
        },

    };
    var ctx = document.getElementById('chart-if')
    var data = Object.assign({}, barChartConfig)
    var raw_data = Object.values(<?= json_encode($impacts) ?>);
    console.log(raw_data);
    data.data = {
        labels: <?= json_encode($years) ?>,
        datasets: [{
                label: 'Impact factor',
                data: raw_data,
                parsing: {
                    yAxisKey: 'impact',
                    xAxisKey: 'year'
                },
                backgroundColor: 'rgba(236, 175, 0, 0.7)',
                borderColor: 'rgba(236, 175, 0, 1)',
                borderWidth: 3
            },
        ],
    }


    console.log(data);
    var myChart = new Chart(ctx, data);
</script>
