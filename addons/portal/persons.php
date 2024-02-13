<style>
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
        width: 32%;
        margin: 0.5em;
        border: 1px solid var(--border-color);
        border-radius: 0.5em;
        box-shadow: var(--box-shadow);
        background: white;
        display: flex;
        align-items: center;
    }

    .table.cards tr td {
        border: 0;
        box-shadow: none;
        /* width: 100%; */
        height: 100%;
        display: block;
    }

    .table.cards tbody tr img,
    .table#persons img {
        max-height: 6rem;
    }

    .table.cards tbody tr td {
        display: flex;
        align-items: center;
        border: 0;
    }
</style>
<div class="container">

    <h1>
        <i class="ph ph-student"></i>
        <?= lang('Persons', 'Personen') ?>
    </h1>


    <table class="table dataTable cards" id="user-table">
        <thead>
            <th></th>
            <th></th>
        </thead>
        <tbody>

            <?php
            foreach ($data as $document) {
                $username = strval($document['username']);
                $img = ROOTPATH . "/img/no-photo.png";
                if (file_exists(BASEPATH . "/img/users/" . $username . "_sm.jpg")) {
                    $img = ROOTPATH . "/img/users/" . $username . "_sm.jpg";
                }
            ?>
                <tr class="">
                    <td>
                        <img src="<?= $img ?>" alt="" class="rounded">
                    </td>
                    <td>
                        <div class="">
                            <h5 class="my-0">
                                <!-- hidden field for sorting without title -->
                                <div style="display: none;"><?= $document['first'] ?> <?= $document['last'] ?></div>
                                <a href="<?= PORTALPATH ?>/person/<?= $document['_id'] ?>" class="">
                                    <?= $document['academic_title'] ?? '' ?>
                                    <?= $document['first'] ?>
                                    <?= $document['last'] ?>
                                </a>
                            </h5>
                            <small>
                                <?php
                                foreach ($document['depts'] as $i => $d) {
                                    $dept = implode('/', $Groups->getParents($d));
                                ?>
                                    <a href="<?= PORTALPATH ?>/group/<?= $d ?>">
                                        <?= $dept ?>
                                    </a>
                                <?php } ?>
                            </small>
                        </div>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

    <script>
        var dataTable;
        $(document).ready(function() {
            dataTable = $('#user-table').DataTable({
                dom: 'frtipP',

                columnDefs: [{
                    targets: [0],
                    searchable: false,
                    sortable: false,
                    visible: true
                }],
                "order": [
                    [1, 'asc'],
                ],
                paging: true,
                autoWidth: true,
                pageLength: 18,
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

</div>