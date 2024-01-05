<?php
/**
 * Page to download activities
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /download
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */
?>


<style>
    @media (min-width: 768px) {
        .row.row-eq-spacing-sm:not(.row-eq-spacing) {
            margin-left: calc(-2rem/2);
            padding-left: 0;
            margin-right: calc(-2rem/2);
            padding-right: 0;
        }
    }
</style>

<div class="container">

    <form action="<?= ROOTPATH ?>/download" method="post">

        <h3><?= lang('Export activities', 'Exportiere Aktivitäten') ?></h3>

        <p class="text-danger">Die Download-Funktion ist noch im Beta-Status. Bug reports gern an <a href="mailto:julia.koblitz@dsmz.de?subject=[OSIRIS] Bug Report Downloads">mich</a>.</p>


        <div class="form-group">
            <label for="filter-type"><?= lang('Filter by type', 'Filter nach Art der Aktivität') ?></label>
            <select name="filter[type]" id="filter-type" class="form-control">
                <option value=""><?= lang('All type of activities', 'Alle Arten von Aktivitäten') ?></option>
                <option value="publication"><?= lang('Publications', 'Publikationen') ?></option>
                <option value="poster"><?= lang('Poster') ?></option>
                <option value="lecture"><?= lang('Lectures', 'Vorträge') ?></option>
                <option value="review"><?= lang('Reviews & Editorial boards') ?></option>
                <option value="teaching"><?= lang('Teaching', 'Lehre') ?></option>
                <option value="students"><?= lang('Students & Guests', 'Studierende & Gäste') ?></option>
                <option value="software"><?= lang('Software & Data', 'Software & Daten') ?></option>
                <option value="misc"><?= lang('Other activities', 'Sonstige Aktivitäten') ?></option>
            </select>
        </div>

        <div class="row position-relative mb-20">
            <div class="col">
                <div class="mr-20">
                    <label for="filter-user"><?= lang('Filter by user', 'Filter nach Nutzer') ?></label>
                    <select name="filter[user]" id="filter-user" class="form-control">
                        <option value="">Alle Nutzer</option>
                        <option value="<?= $_SESSION['username'] ?>"><?= lang('Only my own activities', 'Nur meine eigenen Aktivitäten') ?></option>
                    </select>
                </div>
            </div>

            <div class="text-divider"><?= lang('OR', 'ODER') ?></div>

            
            <div class="col">
                <div class="ml-20">
                    <label for="dept"><?= lang('Department', 'Abteilung') ?></label>
                    <select name="filter[dept]" id="dept" class="form-control">
                        <option value=""><?= lang('All departments', 'Alle Abteilungen') ?></option>
                        <?php

                        foreach ($Departments as $d => $dept) { ?>
                            <option value="<?= $d ?>"><?= $dept ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>


        <div class="form-group">
            <label for="filter-year"><?= lang('Filter by time frame', 'Filter nach Zeitraum') ?></label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><?= lang('From', 'Von') ?></span>
                </div>
                <input type="number" name="filter[time][from][month]" class="form-control" placeholder="month" min="1" max="12" step="1" id="from-month" onchange="filtertime()">
                <input type="number" name="filter[time][from][year]" class="form-control" placeholder="year" min="<?=$Settings->get('startyear')?>" max="<?= CURRENTYEAR ?>" step="1" id="from-year" onchange="filtertime()">
                <div class="input-group-prepend">
                    <span class="input-group-text"><?= lang('to', 'bis') ?></span>
                </div>
                <input type="number" name="filter[time][to][month]" class="form-control" placeholder="month" min="1" max="12" step="1" id="to-month" onchange="filtertime()">
                <input type="number" name="filter[time][to][year]" class="form-control" placeholder="year" min="<?=$Settings->get('startyear')?>" max="<?= CURRENTYEAR ?>" step="1" id="to-year" onchange="filtertime()">

                <div class="input-group-append">
                    <button class="btn" type="button" onclick="filtertime(true)">&times;</button>
                </div>
            </div>
        </div>


        <div class="form-group">

            <?= lang('Highlight:', 'Hervorheben:') ?>

            <div class="custom-radio d-inline-block ml-10">
                <input type="radio" name="highlight" id="highlight-user" value="user" checked="checked">
                <label for="highlight-user"><?= lang('Me', 'Mich') ?></label>
            </div>

            <div class="custom-radio d-inline-block ml-10">
                <input type="radio" name="highlight" id="highlight-aoi" value="aoi">
                <label for="highlight-aoi"><?= $Settings->get('affiliation') ?><?= lang(' Authors', '-Autoren') ?></label>
            </div>

            <div class="custom-radio d-inline-block ml-10">
                <input type="radio" name="highlight" id="highlight-none" value="">
                <label for="highlight-none"><?= lang('None', 'Nichts') ?></label>
            </div>

        </div>


        <div class="form-group">

            <?= lang('File format:', 'Dateiformat:') ?>

            <div class="custom-radio d-inline-block ml-10">
            <input type="radio" name="format" id="format-word" value="word" checked="checked">
                <label for="format-word">Word</label>
            </div>

            <div class="custom-radio d-inline-block ml-10">
            <input type="radio" name="format" id="format-bibtex" value="bibtex">
                <label for="format-bibtex">BibTex</label>
            </div>

        </div>
       


        <button class="btn primary">Download</button>

    </form>
</div>


<script>
    $(document).ready(function() {
        filtertime(true);
    });

    function filtertime(reset = false) {
        var today = new Date();
        if (reset) {
            $("#from-month").val('')
            $("#from-year").val('')
            $("#to-month").val('')
            $("#to-year").val('')
            // dataTable.columns(0).search("", true, false, true).draw();
            return
        }

        var fromMonth = $("#from-month").val()
        if (fromMonth.length == 0 || parseInt(fromMonth) < 1 || parseInt(fromMonth) > 12) {
            fromMonth = 1
        }
        var fromYear = $("#from-year").val()
        if (fromYear.length == 0 || parseInt(fromYear) < <?=$Settings->get('startyear')?> || parseInt(fromYear) > today.getFullYear()) {
            fromYear = <?=$Settings->get('startyear')?>
        }
        var toMonth = $("#to-month").val()
        if (toMonth.length == 0 || parseInt(toMonth) < 1 || parseInt(toMonth) > 12) {
            toMonth = 12
        }
        var toYear = $("#to-year").val()
        if (toYear.length == 0 || parseInt(toYear) < <?=$Settings->get('startyear')?> || parseInt(toYear) > today.getFullYear()) {
            toYear = today.getFullYear()
        }
        // take care that from is not larger than to
        fromMonth = parseInt(fromMonth)
        fromYear = parseInt(fromYear)
        toMonth = parseInt(toMonth)
        toYear = parseInt(toYear)
        if (fromYear > toYear) {
            fromYear = toYear
        }
        if (fromYear == toYear && fromMonth > toMonth) {
            fromMonth = toMonth
        }

        $("#from-month").val(fromMonth)
        $("#from-year").val(fromYear)
        $("#to-month").val(toMonth)
        $("#to-year").val(toYear)

        // var range = dateRange(fromMonth, fromYear, toMonth, toYear)
        // console.log(range);
        // regExSearch = '(' + range.join('|') + ')';
        // dataTable.columns(0).search(regExSearch, true, false, true).draw();
        // table.column(columnNo).search(regExSearch, true, false).draw();
    }
</script>