<?php
    
/**
 * Page to view a selected group
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /groups/view/<id>
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
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
        background-color: var(--department-color);
        text-align: center;
    }

    .dept-icon i.ph {
        margin: 0;
    }

    h1 {
        color: var(--department-color);
    }
</style>

<div <?= $Groups->cssVar($id) ?> class="">

<a class="btn float-right" href="<?= ROOTPATH ?>/preview/group/<?= $id ?>">
    <i class="ph ph-eye ph-fw"></i>
    <?= lang('Preview', 'Vorschau') ?>
</a>
    <h1>
        <?= $group['name'] ?>
    </h1>
    <h3 class="subtitle">
        <?= $group['unit'] ?>
    </h3>
    <div class="row row-eq-spacing">
        <div class="col-md-6 col-lg-8">

        <h3><?=lang('Relevant units', 'Verwandte Einheiten')?></h3>
            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Parent unit', 'Übergeordnete Einheit') ?></span>
                            <?php if ($group['parent']) { ?>
                                <a href="<?= ROOTPATH ?>/groups/view/<?= $group['parent'] ?>"><?= $Groups->getName($group['parent']) ?></a>
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
                                            <a href="<?= ROOTPATH ?>/groups/view/<?= $child['id'] ?>" class="colorless font-weight-bold"><?= $child['name'] ?></a><br>
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
                $persons = $osiris->persons->find(['depts' => $id, 'is_active' => true], ['sort'=>['last'=> 1]])->toArray();
            ?>
            <h3><?=lang('Employees', 'Mitarbeitende')?></h3>
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

                        $img = ROOTPATH . "/img/person.jpg";
                        if (file_exists(BASEPATH . "/img/users/" . $username . "_sm.jpg")) {
                            $img = ROOTPATH . "/img/users/" . $username . "_sm.jpg";
                        }

                    ?>
                        <tr class="<?= $i >= 10 ? 'hidden' : '' ?>">
                            <td>
                                <div class="d-flex align-items-center">

                                    <img src="<?= $img ?>" alt="" style="max-width: 3rem;" class="mr-20 rounded">
                                    <div class="">
                                        <h5 class="my-0">
                                            <a href="<?= ROOTPATH ?>/profile/<?= $username ?>" class="colorless">
                                                <?= $person['first'] ?>
                                                <?= $person['last'] ?>
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
                <a onclick="$('#person-table').find('tr.hidden').removeClass('hidden');$(this).hide()"><?=lang("Show $n more", "Zeige $n weitere")?></a>
            <?php } ?>

        </div>

    </div>

</div>
<?php

if (isset($_GET['verbose'])){
    dump($group, true);
}
?>