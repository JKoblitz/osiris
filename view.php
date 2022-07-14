<div class="row row-eq-spacing-lg">
    <div class="col-lg-9">
        <div class="content">

            <a href="<?= ROOTPATH ?>/edit/<?= $page ?>/<?= $id ?>" class="btn btn-primary float-right"><i class="far fa-edit mr-5"></i> Edit entry</a>

            <h4><?= ucwords($page) ?>: #<?= $dataset[$idname] ?></h4>
        </div>
        <table class="table table-sm " id="">
            <tbody>
                <?php foreach ($dataset as $key => $value) {
                    $schema = $schemata[$key][0];
                ?>
                    <tr id="row-<?= $key ?>">
                        <td class="w-200">
                            <?= $key ?>
                        </td>
                        <td class="">
                            <?php
                            switch ($schema["DATA_TYPE"]) {
                                case 'boolean':
                                case 'tinyint':
                                    echo ($value == 0) ? 'no' : 'yes';
                                    break;
                                case 'date':
                                    $date = new DateTime($value);
                                    echo $date->format('d.m.Y');
                                    break;

                                default:
                                    echo $value;
                                    break;
                            }
                            if ($key == 'derivative_group' && isset($group) && !empty($group)) {
                                echo " <span class='badge badge-pill text-danger ml-10'>$group</span>";
                            } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="col-lg-3">
        <?php if ($page == 'publication' || $page == 'poster' || $page == 'lecture') {
            $stmt = $db->prepare("SELECT * FROM authors WHERE ${page}_id = ?");
            $stmt->execute([$id]);
            $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
            <div class="content">
                <h4><?= lang('Authors', 'Autoren') ?></h4>
            </div>

            <table class="table table-sm">
                <?php foreach ($authors as $author) { ?>
                    <tr>
                        <td><?= $author['last_name'] ?></td>
                        <td><?= $author['first_name'] ?></td>
                        <td><?= $author['position'] ?></td>
                        <td><?= $author['aoi'] == 1 ? 'DSMZ' : '' ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>

</div>

<div class="content">
    <?php if ($page == 'journal') {
        $stmt = $db->prepare("SELECT year AS category, impact_factor AS amount FROM journal_if WHERE journal_id = ? ORDER BY year ASC");
        $stmt->execute([$id]);
        $impacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <h4><?= lang('Impact factors', 'Impact-Faktoren') ?></h4>

       <div class="box">
       <div id="view" class="content">
            <script type="application/json" id="vega-schema">
                {
                    "$schema": "https://vega.github.io/schema/vega/v5.json",
                    "description": "A basic bar chart example, with value labels shown upon mouse hover.",
                    "width": 600,
                    "height": 200,
                    "padding": 5,
                    "data": [{
                        "name": "table",
                        "values": <?= json_encode($impacts) ?>
                    }],

                    "signals": [{
                        "name": "tooltip",
                        "value": {},
                        "on": [{
                                "events": "rect:mouseover",
                                "update": "datum"
                            },
                            {
                                "events": "rect:mouseout",
                                "update": "{}"
                            }
                        ]
                    }],

                    "scales": [{
                            "name": "xscale",
                            "type": "band",
                            "domain": {
                                "data": "table",
                                "field": "category"
                            },
                            "range": "width",
                            "padding": 0.05,
                            "round": true
                        },
                        {
                            "name": "yscale",
                            "domain": {
                                "data": "table",
                                "field": "amount"
                            },
                            "nice": true,
                            "range": "height"
                        }
                    ],
                    "axes": [{
                            "orient": "bottom",
                            "scale": "xscale"
                        },
                        {
                            "orient": "left",
                            "scale": "yscale"
                        }
                    ],

                    "marks": [{
                            "type": "rect",
                            "from": {
                                "data": "table"
                            },
                            "encode": {
                                "enter": {
                                    "x": {
                                        "scale": "xscale",
                                        "field": "category"
                                    },
                                    "width": {
                                        "scale": "xscale",
                                        "band": 1
                                    },
                                    "y": {
                                        "scale": "yscale",
                                        "field": "amount"
                                    },
                                    "y2": {
                                        "scale": "yscale",
                                        "value": 0
                                    }
                                },
                                "update": {
                                    "fill": {
                                        "value": "#eca001"
                                    }
                                },
                                "hover": {
                                    "fill": {
                                        "value": "#b61f29"
                                    }
                                }
                            }
                        },
                        {
                            "type": "text",
                            "encode": {
                                "enter": {
                                    "align": {
                                        "value": "center"
                                    },
                                    "baseline": {
                                        "value": "bottom"
                                    },
                                    "fill": {
                                        "value": "#333"
                                    }
                                },
                                "update": {
                                    "x": {
                                        "scale": "xscale",
                                        "signal": "tooltip.category",
                                        "band": 0.5
                                    },
                                    "y": {
                                        "scale": "yscale",
                                        "signal": "tooltip.amount",
                                        "offset": -2
                                    },
                                    "text": {
                                        "signal": "tooltip.amount"
                                    },
                                    "fillOpacity": [{
                                            "test": "datum === tooltip",
                                            "value": 0
                                        },
                                        {
                                            "value": 1
                                        }
                                    ]
                                }
                            }
                        }
                    ]
                }
            </script>
        </div>
       </div>



        <script src="https://vega.github.io/vega/vega.min.js"></script>
        <script>
            function render(spec) {
                view = new vega.View(vega.parse(spec), {
                    renderer: 'svg', // renderer (canvas or svg)
                    container: '#view', // parent DOM container
                    hover: true // enable hover processing
                });
                return view.runAsync();
            }
            var js = JSON.parse(document.getElementById('vega-schema').innerHTML);
            console.log(js);
            render(js);
        </script>
    <?php } ?>
</div>