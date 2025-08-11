<div class="modal fade " id="select_category_modal" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content  ">
            <div class="modal-header bg-dark">
                <h4 class="modal-title">Select Category</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body pb-0 overflow-auto">
                <div class="table-responsive pb-3">
                    <table class="table table-hover table-sm text-sm  datatable-fixed" style="width:100%" id="dtbl_select_category">
                        <thead class="bg-white">
                            <tr class="">
                                <th>#</th>
                                <th>Action</th>
                                <th>Status</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($dCategories ?? [] as $dCategory) {
                            ?>
                                <tr>
                                    <td><?= $dCategory["cat_id"]; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-dark" onclick="selectCategory(
                                        '<?= $dCategory['cat_id']; ?>',
                                        '<?= $dCategory['cat_code']; ?>',
                                        '<?= $dCategory['cat_name']; ?>',
                                        '<?= $dCategory['cat_description']; ?>',
                                        );">Select</button>
                                    </td>
                                    <td><?= appRenderBadgeStatus($dCategory["cat_is_active"]); ?></td>
                                    <td><?= appRenderLabel($dCategory["cat_code"], "160px"); ?></td>
                                    <td><?= appRenderLabel($dCategory["cat_name"]); ?></td>
                                    <td><?= appRenderInputText($dCategory["cat_description"]); ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-body  bg-dark">
                <button type="button" class="btn btn-default btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>