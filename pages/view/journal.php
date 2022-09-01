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
        <td><?= $data['journal_abbr'] ?></td>
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
?>

<h4><?= lang('Impact factors', 'Impact-Faktoren') ?></h4>

<div class="box">
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
                        "field": "year"
                    },
                    "range": "width",
                    "padding": 0.05,
                    "round": true
                },
                {
                    "name": "yscale",
                    "domain": {
                        "data": "table",
                        "field": "impact"
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
                                "field": "year"
                            },
                            "width": {
                                "scale": "xscale",
                                "band": 1
                            },
                            "y": {
                                "scale": "yscale",
                                "field": "impact"
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
                                "signal": "tooltip.year",
                                "band": 0.5
                            },
                            "y": {
                                "scale": "yscale",
                                "signal": "tooltip.impact",
                                "offset": -2
                            },
                            "text": {
                                "signal": "tooltip.impact"
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
    <div id="view" class="content">
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
</div>