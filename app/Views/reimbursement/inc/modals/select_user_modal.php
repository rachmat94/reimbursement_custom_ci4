<div class="modal fade " id="select_user_modal" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content  ">
            <div class="modal-header bg-white">
                <h4 class="modal-title">
                    Pilih User
                </h4>
                <button type="button" class="close btn-light " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover" style="width: 100%;" id="dtbl_select_user">
                        <thead>
                            <tr>
                                <th>Aksi</th>
                                <th>Kode</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Group</th>
                                <th>Kategori User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($dUsers  as $kUser  => $vUser) {
                            ?>
                                <tr>
                                    <td>
                                        <button type="button" onclick="doSelectUser(<?= $vUser['usr_id']; ?>)" class="btn btn-sm btn-dark"><i class="fas fa-user"></i> Pilih</button>
                                    </td>
                                    <td><?= $vUser["usr_code"]; ?></td>
                                    <td><?= $vUser["usr_username"]; ?></td>
                                    <td><?= $vUser["usr_email"]; ?></td>
                                    <td><?= masterUserRole($vUser["usr_role"], true)["label"]; ?></td>
                                    <td><?= $vUser["group_name"]; ?></td>
                                    <td><?= $vUser["usr_group_category"]; ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer  bg-white">
                <button type="button" class="btn btn-outline-dark btn-block btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>