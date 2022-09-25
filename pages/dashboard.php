<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<style>
    .row .box {
        margin-top: 0;
    }
</style>

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
                    ticks: {
                        // Include a dollar sign in the ticks
                        callback: function(value, index, ticks) {
                            // only show full numbers
                            if (Number.isInteger(value)) {
                                return value
                            }
                            return "";
                        }
                    }
                }
            }
        }

    };
</script>

<?php if ($USER['is_controlling']) {

    include BASEPATH . "/pages/dashboard-controlling.php";
    
} elseif ($USER['is_scientist']) {

    include BASEPATH . "/pages/dashboard-scientist.php";

} else { ?>
    <p>
        <?= lang('You are neither scientist nor controlling staff.', 'Du bist weder Wissenschaftler:in noch Controlling.') ?>
    </p>
<?php } ?>