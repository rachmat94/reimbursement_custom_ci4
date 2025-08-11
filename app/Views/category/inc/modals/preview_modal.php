<div class="modal fade " id="preview_category_modal" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Category
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
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

            </div>

            <div class="modal-footer  bg-white">
                <button type="button" class="btn btn-outline-dark btn-block btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>