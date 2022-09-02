<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<?php

$n_scientists = $osiris->users->count(["is_scientist" => true]);
$n_approved = $osiris->users->count(["is_scientist" => true, "approved" => SELECTEDYEAR. "Q" . SELECTEDQUARTER]);

?>



<h2><?= lang('Welcome', 'Willkommen') ?>, <?= $USER['name'] ?></h2>

<h4 class="text-muted font-weight-normal">Controlling</h4>




<h4><?= lang('Approved in') . " " . SELECTEDYEAR. "Q" . SELECTEDQUARTER ?></h4>

<div class="box">
    <div class="chart w-400 mw-full content">

        <canvas id="approved-chart" ></canvas>
        <button class="btn mt-20" onclick="loadModal('components/controlling-approved')">
            <i class="fas fa-search-plus"></i> <?= lang('Show details', 'Zeige Details') ?>
        </button>

    </div>

    <script>
        const ctx = document.getElementById('approved-chart')
        const myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Approved', 'Missing'],
                datasets: [{
                    label: '# of Scientists',
                    data: [<?= $n_approved ?>, <?= $n_scientists - $n_approved ?>],
                    backgroundColor: [
                        'rgba(236, 175, 0, 0.2)',
                        'rgba(182, 31, 41, 0.2)',
                    ],
                    borderColor: [
                        'rgba(236, 175, 0, 1)',
                        'rgba(182, 31, 41, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Scientists approvation'
                    }
                }
            }
        });
    </script>
</div>