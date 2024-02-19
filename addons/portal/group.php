<?php

/**
 * Page to see details on a single project
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024, Julia Koblitz
 * 
 * @link        /project/<id>
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2024, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */
?>

<style>
    .dept-icon {
        border-radius: 10rem;
        color: white;
        width: 1.6em;
        height: 1.6em;
        display: inline-block;
        background-color: var(--highlight-color);
        text-align: center;
    }

    .dept-icon i.ph {
        margin: 0;
    }

    h1 {
        color: var(--highlight-color);
    }

    tr.hidden {
        display: none;
    }
</style>

<div <?= $Groups->cssVar($id) ?> class="container">

    <h1>
        <?= $group['name'] ?>
    </h1>
    <h3 class="subtitle">
        <?= $group['unit'] ?>
    </h3>

    <p>
        <?=lang($group['description_en'] ?? '', $group['description'] ?? '')?>
    </p>
    <div class="row row-eq-spacing">
        <div class="col-md-6 col-lg-8">

            <h3><?= lang('Relevant units', 'Verwandte Einheiten') ?></h3>
            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Parent unit', 'Übergeordnete Einheit') ?></span>
                            <?php if ($group['parent']) { ?>
                                <a href="<?= PORTALPATH ?>/group/<?= $group['parent'] ?>"><?= $Groups->getName($group['parent']) ?></a>
                            <?php } else { ?>
                                -
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Child units', 'Untereinheiten') ?></span>
                            <?php
                            $children = $osiris->groups->find(['parent' => $id])->toArray();
                            ?>
                            <?php if (!empty($children)) { ?>
                                <ul class="list">
                                    <?php foreach ($children as $child) { ?>
                                        <li>
                                            <a href="<?= PORTALPATH ?>/group/<?= $child['id'] ?>" class="colorless font-weight-bold"><?= $child['name'] ?></a><br>
                                            <span class="text-muted"><?= $child['unit'] ?></span>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } else { ?>
                                -
                            <?php } ?>

                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- TODO: hier ist Platz für Auswertungen und Graphen -->
        </div>

        <div class="col-md-6 col-lg-4">
            <?php
            $children = $Groups->getChildren($group['id']);
            $persons = $osiris->persons->find(['depts' => ['$in' => $children], 'is_active' => true])->toArray();
            if (isset($group['head'])) {

                $head = $group['head'];
                if (is_string($head)) $head = [$head];
                else $head = DB::doc2Arr($head);


                usort($persons, function ($a, $b) use ($head) {
                    return in_array($a['username'], $head)  ? -1 : 1;
                });
            } else {
                $head = [];
            }
            ?>
            <h3><?= lang('Employees', 'Mitarbeitende') ?></h3>
            <table class="table" id="person-table">
                <tbody>
                    <?php
                    if (empty($persons ?? array())) {
                    ?>
                        <tr>
                            <td>
                                <?= lang('No persons connected.', 'Keine Personen verknüpft.') ?>
                            </td>
                        </tr>
                    <?php
                    } else foreach ($persons as $i => $person) {
                        $username = strval($person['username']);
                    ?>
                        <tr class="<?= $i >= 10 ? 'hidden' : '' ?>">
                            <td>
                                <div class="d-flex align-items-center">

                                    <img src="<?= $img ?>" alt="" style="max-width: 3rem;" class="mr-20 rounded">
                                    <?= $Settings->printProfilePicture($username, 'profile-img small mr-20') ?>
                                    
                                    <div class="">
                                        <h5 class="my-0">
                                            <a href="<?= PORTALPATH ?>/person/<?= $username ?>" class="colorless">
                                              <?= $person['first'] ?>  <?= $person['last'] ?>
                                            </a>
                                        </h5>
                                        <?= $person['position'] ?? '' ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php
                    } ?>



                </tbody>
            </table>
            <?php if (($i ?? 0) >= 10) {
                $n = $i - 9;
            ?>
                <a onclick="$('#person-table').find('tr.hidden').removeClass('hidden');$(this).hide()"><?= lang("Show $n more", "Zeige $n weitere") ?></a>
            <?php } ?>

        </div>

    </div>

</div>