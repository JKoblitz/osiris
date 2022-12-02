<script src="<?= ROOTPATH ?>/js/chart.min.js"></script>

<?php if ($USER['is_controlling'] || $USER['is_admin']) { ?>
    <a href="<?= ROOTPATH ?>/edit/journal/<?= $id ?>" class="btn btn-osiris float-right"><?= lang('Edit Journal', 'Journal bearbeiten') ?></a>
<?php } ?>


<h1>
    <?= $data['journal'] ?>
</h1>


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
$impacts = $data['impact']->bsonSerialize();
sort($impacts);
$years = array_column((array) $impacts, 'year');
// dump($impacts);
?>


<div class="box">
    <div class="chart content">
        <?php if ($USER['is_controlling'] || $USER['is_admin']) { ?>
            <div class="dropdown with-arrow float-right">
                <button class="btn btn-osiris" data-toggle="dropdown" type="button" id="dropdown-2" aria-haspopup="true" aria-expanded="false">
                    <?= lang('Add IF', 'Füge IF hinzu') ?> <i class="fas fa-angle-down ml-5" aria-hidden="true"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-2">
                    <div class="content">
                        <form action="<?= ROOTPATH ?>/update-journal/<?= $id ?>" method="post">
                        <input type="hidden" class="hidden" name="redirect" value="<?= $url ?? $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">
                            <div class="form-group">
                                <label for="year"><?= lang('Year', 'Jahr') ?></label>
                                <input type="number" min="1970" max="2050" step="1" class="form-control" name="values[year]" id="year" value="<?=CURRENTYEAR?>" required>
                            </div>
                            <div class="form-group">
                                <label for="if"><?= lang('Impact') ?></label>
                                <input type="number" min="0" max="300" step="0.001" class="form-control" name="values[if]" id="if">
                            </div>
                            <button class="btn btn-block"><i class="fas fa-check"></i> <?= lang('Add', 'Hinzuf.') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>


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
        }, ],
    }


    console.log(data);
    var myChart = new Chart(ctx, data);
</script>