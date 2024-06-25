<?php

/**
 * Page to browse all user groups
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * 
 * @link        /groups
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

$style = $_GET['style'] ?? 'cards';
?>

<?php if ($Settings->featureEnabled('portal')) { ?>
    <a href="<?= ROOTPATH ?>/preview/groups" class="btn float-right"><i class="ph ph-eye"></i> <?= lang('Preview', 'Vorschau') ?></a>
<?php } ?>
<h1>
    <i class="ph ph-users-three"></i>
    <?= lang('Organisational Units', 'Organisationseinheiten') ?>
</h1>

<div class="d-flex align-items-center mb-10">

    <?php if ($Settings->hasPermission('guests.add')) { ?>
        <a href="<?= ROOTPATH ?>/groups/new"><i class="ph ph-plus"></i> <?= lang('New unit', 'Neue Einheit') ?></a>
    <?php } ?>

    <div class="pills small ml-auto">
        <span class="badge text-muted"><?= lang('Show as', 'Zeige als') ?></span>
        <a class="btn <?= $style == 'cards' ? 'active' : '' ?>" href="?style=cards"><?= lang('Cards', 'Karten') ?></a>
        <a class="btn <?= $style == 'hirarchy' ? 'active' : '' ?>" href="?style=hirarchy"><?= lang('Hirarchy', 'Hirarchie') ?></a>
        <a class="btn <?= $style == 'organigramm' ? 'active' : '' ?>" href="?style=organigramm"><?= lang('Organization Chart', 'Organigramm') ?></a>
    </div>
</div>



<?php if ($style == 'cards') { ?>

    <style>
        .badge {
            background: var(--highlight-color);
            color: white;
            opacity: .7;
        }

        .table.cards {
            border: none;
            background: transparent;
            box-shadow: none;
        }

        .table.cards thead {
            display: none;
        }

        .table.cards tbody {
            display: flex;
            flex-grow: column;
            flex-direction: row;
            flex-wrap: wrap;

        }

        .table.cards tr {
            width: 100%;
            margin: 0.5em;
            border: 1px solid var(--border-color);
            border-radius: 0.5em;
            box-shadow: var(--box-shadow);
            background: white;
        }

        .table.cards tr td {
            border: 0;
            box-shadow: none;
            width: 100%;
            height: 100%;
            display: block;
        }

        .table.cards tr td h5 {
            margin: 0;
        }
        .table.cards a.title {
            color: var(--highlight-color);
            font-size: 1.6rem;
        }


        @media (min-width: 768px) {
            .table.cards tbody tr {
                width: calc(50% - 1.4rem);
            }
        }

        @media (min-width: 1200px) {
            .table.cards tbody tr {
                width: calc(33.3% - 1.4rem);
            }
        }
    </style>

    <table class="table cards" id="group-table">
        <thead>
            <th></th>
        </thead>
        <tbody>
            <?php foreach ($Groups->groups as $group) { ?>
                <tr>
                    <td class="" id="<?= $group['id'] ?>" <?= $Groups->cssVar($group['id']) ?>>
                        <span style="display:none">
                            <!-- hidden field for sorting based on level -->
                            <?= $Groups->getLevel($group['id']) ?>
                        </span>
                        <span class="badge dept-id float-md-right"><?= $group['id'] ?></span>
                        <span class="text-muted"><?= $group['unit'] ?></span>
                        <h5>
                            <a href="<?= ROOTPATH ?>/groups/view/<?= $group['id'] ?>" class="title">
                                <?= lang($group['name'], $group['name_de'] ?? null) ?>
                            </a>
                        </h5>

                        <div class="text-muted font-size-12">
                            <?php
                            $children = $Groups->getChildren($group['id']);
                            ?>
                            <?= $osiris->persons->count(['depts' => ['$in' => $children],  'is_active' => true]) ?> <?= lang('Coworkers', 'Mitarbeitende') ?>

                        </div>
                        <?php if (isset($group['head'])) {
 ?>
                        <hr>
                        <div class="mb-0">
                            <?php 
                                $heads = $group['head'];
                                if (is_string($heads)) $heads = [$heads];
                                $heads = array_map([$DB, 'getNameFromId'], $heads);
                            ?>
                                <i class="ph ph-crown text-signal"></i>
                                <?= implode(', ', $heads) ?>
                        </div>
                    <?php } ?>

                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <script>
        var dataTable;
        $(document).ready(function() {
            dataTable = $('#group-table').DataTable({
                dom: 'frtipP',

                // columnDefs: [{
                //     targets: [0],
                //     searchable: false,
                //     sortable: false,
                //     visible: true
                // }],
                // "order": [
                //     [0, 'asc'],
                // ],
                paging: true,
                autoWidth: true,
                pageLength: 12,
                initComplete: function(settings, json) {
                    // $(".dt-buttons .btn-group").append(
                    //     '<a id="cv" class="btn btn-primary" href="#">CARDs VIEW</a>'
                    // );
                    // var labels = [];
                    // $("#result-table thead th").each(function() {
                    //     labels.push($(this).text());
                    // });
                    // $("#result-table tbody tr").each(function() {
                    //     $(this)
                    //         .find("td")
                    //         .each(function(column) {
                    //             $("<span class='key'>" + labels[column] + "</span>").prependTo(
                    //                 $(this)
                    //             );
                    //         });
                    // });
                }
            });
        });
    </script>


<?php } else if ($style == 'hirarchy') {

    echo $Groups->getHirarchy();
} else if ($style == 'organigramm') { ?>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/d3-org-chart@3"></script>
    <script src="https://cdn.jsdelivr.net/npm/d3-flextree@2.1.2/build/d3-flextree.js"></script>


    <div class="btn-group float-left">
        <button class="btn small" onclick="chart.layout('left').render().fit()">Left</button>
        <button class="btn small" onclick="chart.layout('top').render().fit()">Top</button>
    </div>

    <!-- <canvas id="canvas" width="900" height="900" style="background:#FFF"></canvas> -->
    <div class="chart-container" style="background-color: #f6f6f6"></div>
    <?php
    $levels = $Groups->groups;

    foreach ($levels as $k => &$value) {
        $res = array();
        if (isset($value['head'])) {
            $heads = $value['head'];
            if (is_string($heads)) $heads = [$heads];
            foreach ($heads as $i => $k) {
                $res[] = $DB->getNameFromId($k);
            }
        }
        $value['head'] = implode(', ', $res);
    }

    $g = array_map(function ($a) {
        return [
            'id' => $a['id'],
            'name' => $a['name'],
            'unit' => $a['unit'],
            'head' => $a['head'],
            'color' => $a['color'],
            'parent' => $a['parent'],
        ];
    }, $Groups->groups);

    ?>

    <script>
        var tree = JSON.parse('<?= json_encode($g) ?>');

        console.log(tree);
        var data = [];
        for (const id in tree) {
            const el = tree[id];
            el.parentId = el.parent
            data.push(el);
        }

        chart = new d3.OrgChart()
            .container('.chart-container')
            .data(data)
            .layout('left')
            .nodeWidth((d) => 250)
            .svgHeight(window.innerHeight - 300)
            .initialZoom(0.7)
            .nodeHeight((d) => 175)
            .childrenMargin((d) => 40)
            .compactMarginBetween((d) => 15)
            .compactMarginPair((d) => 80)
            .nodeContent(function(d, i, arr, state) {
                return `
            <div style="padding-top:30px;background-color:none;margin-left:1px;height:${
              d.height
            }px;border-radius:2px;overflow:visible">
              <div style="height:${
                d.height - 32
              }px;padding-top:0px;background-color:white;border:1px solid var(--border-color);">

               <div style="margin-right:10px;margin-top:15px;float:right">${
                 d.data.id
               }</div>
               
               <div style="margin-top:-15px;background-color:${d.data.color};height:10px;width:${
                 d.width - 2
               }px;border-radius:1px"></div>

               <div style="padding:20px; padding-top:35px;text-align:center">
                   <div style="color:${d.data.color};font-size:16px;font-weight:bold"> ${
                     d.data.name
                   } </div>
                   <div class="text-muted mt-5"> ${
                     d.data.unit
                   } </div>
                   <em>${d.data.head}</em>
               </div> 
              </div>     
      </div>
  `;
            })
            .render();
    </script>

<?php } ?>