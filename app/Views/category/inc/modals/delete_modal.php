<div class="modal fade " id="delete_category_modal" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Delete Category
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form autocomplete="off" method="post" action="<?= base_url('category/do_delete'); ?>" id="delete_category_form">
                <div class="modal-body table-responseive">
                    <?= appRenderTableInfo3([
                        [
                            "name" => "Code:",
                            "value" => $dCategory["cat_code"],
                        ],
                        [
                            "name" => "Name:",
                            "value" => $dCategory["cat_name"],
                        ],
                        [
                            "name" => "Status:",
                            "value" => appRenderBadgeStatus($dCategory["cat_is_active"]),
                        ],
                        [
                            "name" => "Description:",
                            "value" => $dCategory["cat_description"],
                        ],
                    ]); ?>
                    <?= mycsrfTokenField(); ?>
                    <input type="hidden" name="hdn_cat_key" value="<?= $dCategory["cat_key"]; ?>">

                    <button type="submit" class="btn btn-danger px-3 my-1"><i class="fas fa-times"></i> Delete</button>
                </div>
            </form>

            <div class="modal-footer  bg-white">
                <button type="button" class="btn btn-outline-dark btn-block btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>