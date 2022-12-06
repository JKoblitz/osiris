<script src="<?= ROOTPATH ?>/js/chart.min.js"></script>

<?php if ($USER['is_controlling'] || $USER['is_admin']) { ?>
    <a href="<?= ROOTPATH ?>/journal/edit/<?= $id ?>" class="btn btn-osiris float-right"><?= lang('Edit Journal', 'Journal bearbeiten') ?></a>
<?php } ?>


<h2>
    <?= $data['journal'] ?>
</h2>


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
        <td><?= implode('<br>', $data['issn']->bsonSerialize()) ?></td>
    </tr>
</table>


<h3>
    <?= lang('Publications in this journal', 'Publikationen in diesem Journal') ?>
</h3>

<table class="table" id="publication-table">
    <thead>
        <th>Activity</th>
        <th>Link</th>
    </thead>
    <tbody>
    </tbody>
</table>
<script src="<?= ROOTPATH ?>/js/jquery.dataTables.min.js"></script>
<script src="<?= ROOTPATH ?>/js/jquery.dataTables.naturalsort.js"></script>

<script>
    $.extend($.fn.DataTable.ext.classes, {
        sPaging: "pagination mt-10 ",
        sPageFirst: "direction ",
        sPageLast: "direction ",
        sPagePrevious: "direction ",
        sPageNext: "direction ",
        sPageButtonActive: "active ",
        sFilterInput: "form-control form-control-sm d-inline w-auto ml-10 ",
        sLengthSelect: "form-control form-control-sm d-inline w-auto",
        sInfo: "float-right text-muted",
        sLength: "float-right"
    });
    var dataTable;
    $(document).ready(function() {
        $('#publication-table').DataTable({
            ajax: {
                "url": ROOTPATH + '/api/activities',
                "data": {
                    "filter": {
                        journal_id: '<?= $id ?>',
                    },
                    formatted: true
                }
            },
            language: {
                "zeroRecords": "No matching records found",
                "emptyTable": lang('No publications available for this journal.', 'Für dieses Journal sind noch keine Publikationen verfügbar.'),
            },
            "pageLength": 5,
            columnDefs: [{
                    targets: 0,
                    data: 'activity'
                },
                {
                    "targets": 1,
                    "data": "name",
                    "render": function(data, type, full, meta) {
                        return `<a href="${ROOTPATH}/journal/view/${full.id}"><i class="icon-activity-search"></a>`;
                    }
                },
            ],
            <?php if (isset($_GET['q'])) { ?> "oSearch": {
                    "sSearch": "<?= $_GET['q'] ?>"
                }
            <?php } ?>
        });

    });
</script>

<?php
$impacts = $data['impact']->bsonSerialize();
sort($impacts);
$years = array_column((array) $impacts, 'year');
?>


<h3><?= lang('Impact factors', 'Impact-Faktoren') ?></h3>

<div class="box">
    <div class="content">

        <?php if ($USER['is_controlling'] || $USER['is_admin']) { ?>
            <div class="dropdown with-arrow float-right mb-20">
                <button class="btn btn-osiris" data-toggle="dropdown" type="button" id="dropdown-2" aria-haspopup="true" aria-expanded="false">
                    <?= lang('Add IF', 'Füge IF hinzu') ?> <i class="fas fa-angle-down ml-5" aria-hidden="true"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-2">
                    <div class="content">
                        <form action="<?= ROOTPATH ?>/update-journal/<?= $id ?>" method="post">
                            <input type="hidden" class="hidden" name="redirect" value="<?= $url ?? $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">
                            <div class="form-group">
                                <label for="year"><?= lang('Year', 'Jahr') ?></label>
                                <input type="number" min="1970" max="2050" step="1" class="form-control" name="values[year]" id="year" value="<?= CURRENTYEAR ?>" required>
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