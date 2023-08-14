<?php
/**
 * Page to see roadmap
 * 
 * NOT IN USE
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
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
    #timeline {
        position: relative;
        display: table;
        height: 100%;
        margin-left: auto;
        margin-right: auto;
        margin-top: 2rem;
    }

    #timeline div:after {
        content: "";
        width: 2px;
        position: absolute;
        top: 0.5rem;
        bottom: 3rem;
        left: 5.5rem;
        z-index: 1;
        background: #888;
    }

    #timeline h3 {
        position: -webkit-sticky;
        position: sticky;
        top: 7rem;
        color: #888;
        margin: 0;
        font-size: 1em;
        font-weight: 400;
    }

    @media (min-width: 62em) {
        #timeline h3 {
            font-size: 1.1em;
        }
    }

    #timeline section.year {
        position: relative;
    }

    #timeline section.year:first-child section {
        margin-top: -1.3em;
        padding-bottom: 0px;
    }

    #timeline section.year section {
        position: relative;
        padding-bottom: 1.25em;
        margin-bottom: 2.2em;
    }

    #timeline section.year section h4 {
        position: absolute;
        top: 0;
        font-size: 0.9em;
        font-weight: 600;
        line-height: 1.2em;
        margin: 0;
        padding: 0 0 0 89px;
        color: var(--muted-color-light);
    }
    
    #timeline section.year section.active h4 {
        color: var(--signal-color);
    }

    @media (min-width: 62em) {
        #timeline section.year section h4 {
            font-size: 1em;
        }
    }

    #timeline section.year section ul {
        list-style-type: none;
        padding: 2.4rem 0 0 75px;
        margin: -1.35rem 1.5rem 1em;
        /* max-width: 32rem; */
        font-size: 1em;
    }

    @media (min-width: 62em) {
        #timeline section.year section ul {
            font-size: 1.1em;
            padding-left: 81px;
            list-style: disc outside;
        }
    }

    #timeline section.year section ul:last-child {
        margin-bottom: 0;
    }

    #timeline section.year section ul:first-of-type:after {
        content: "";
        width: 1.3rem;
        height: 1.3rem;
        background: var(--muted-color-light);
        border: 3px solid var(--body-color);
        -webkit-border-radius: 50%;
        -moz-border-radius: 50%;
        -ms-border-radius: 50%;
        border-radius: 50%;
        position: absolute;
        left: calc(5.5rem - (1.3rem / 2 - 0.1rem) );
        top: 2px;
        z-index: 2;
    }
    #timeline section.year section.active ul:first-of-type:after {
        background: var(--signal-color);

    }


    #timeline section.year section ul li {
        margin-left: 0.5rem;
    }


    #timeline section.year section ul li:not(:first-child) {
        margin-top: 0.5rem;
    }

</style>

<?php
    if (!function_exists('format_month')) {
        
function format_month($month)
{
    if (empty($month)) return '';
    $month = intval($month);
    $array = [
        1 => lang("January", "Januar"),
        2 => lang("February", "Februar"),
        3 => lang("March", "März"),
        4 => lang("April"),
        5 => lang("May", "Mai"),
        6 => lang("June", "Juni"),
        7 => lang("July", "Juli"),
        8 => lang("August"),
        9 => lang("September"),
        10 => lang("October", "Oktober"),
        11 => lang("November"),
        12 => lang("December", "Dezember")
    ];
    return $array[$month];
}
    }
?>


<h1 id="roadmap">Roadmap</h1>

<div id="timeline">
    <div>

        <?php

        $roadmap_json = file_get_contents(BASEPATH . "/roadmap.json");
        $roadmap = json_decode($roadmap_json, true, 512, JSON_NUMERIC_CHECK);
        foreach ($roadmap as $year => $months) {
            echo "<section class='year'>";
            echo "<h3>$year</h3>";
            foreach ($months as $month => $items) {
                $active = "";
                if ($year <= CURRENTYEAR && $month <= CURRENTMONTH ){
                    $active = "active";
                }
                echo "<section class='$active'>";
                echo "<h4>".(format_month($month))."</h4>";
                echo "<ul>";
                foreach ($items as $item) {
                    echo "<li>$item</li>";
                }
                echo "</ul>";
                echo "</section>";
            }

            echo "</section>";
        }

        ?>
    </div>
</div>