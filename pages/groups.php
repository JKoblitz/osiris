<?php

/**
 * Page to browse all user groups
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /groups
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

$style = $_GET['style'] ?? 'cards';
?>

<h1>
    <i class="ph ph-student"></i>
    <?= lang('Organisational Units', 'Struktureinheiten') ?>
</h1>

<div class="d-flex align-items-center mb-10">

    <a href="<?= ROOTPATH ?>/groups/new"><i class="ph ph-plus"></i> <?= lang('New unit', 'Neue Einheit') ?></a>

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
            background: var(--department-color);
            color: white;
            opacity: .7;
        }
    </style>
    <div class="row row-eq-spacing">
        <?php foreach ($Groups->groups as $group) { ?>
            <div class="col-md-6 col-lg-4 mb-20">
                <div class="alert h-full" id="<?= $group['id'] ?>" <?= $Groups->cssVar($group['id']) ?>>
                    <a class="title link" href="<?= ROOTPATH ?>/groups/view/<?= $group['id'] ?>">
                        <span class="badge"><?= $group['id'] ?></span>
                        <?= $group['name'] ?>
                    </a>

                    <?php if (!empty($group['parent'])) { ?>
                        <p class="font-size-12">
                            <a href="#<?= $group['parent'] ?>"><?= $Groups->getName($group['parent']) ?></a>
                        </p>
                    <?php } ?>

                    <p class="text-muted">
                        <?= $group['unit'] ?>
                    </p>

                    <?php if (isset($group['head'])) {
                        $heads = $group['head'];
                        if (is_string($heads)) $heads = [$heads];
                        $heads = array_map([$DB, 'getNameFromId'], $heads);
                    ?>
                        <span class="float-right">
                            <i class="ph ph-crown text-signal"></i>
                            <?= implode(', ', $heads) ?>
                        </span>
                    <?php
                    } ?>
                    <?php
                    $children = $Groups->getChildren($group['id']);
                    ?>
                    <?= $osiris->persons->count(['depts' => ['$in' => $children],  'is_active' => true]) ?> <?= lang('Members', 'Mitglieder') ?>
                </div>
            </div>
        <?php } ?>
    </div>

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
    ?>

    <script>
        var tree = JSON.parse('<?= json_encode($Groups->groups) ?>');

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