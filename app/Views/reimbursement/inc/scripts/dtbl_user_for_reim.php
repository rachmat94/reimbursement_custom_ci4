<script>
    const dTblUsersForReim = $("#dtbl_users_for_reim");

    function loaddTblUsersForReim() {
        setOnProgress(true);
        dTblUsersForReim.DataTable({
            scrollX: true,
            fixedHeader: true,
            "responsive": false,
            "processing": true,
            "serverSide": true,
            "order": [
                ['0', 'desc']
            ],
            fixedColumns: {
                left: 2,
            },
            // "iDisplayLength": 10,
            "ajax": {
                "url": "<?= base_url('reimbursement/dtbl_user_for_reim'); ?>",
                "type": "POST",
                "data": {
                    tahun: <?= $tahun; ?>,
                    triwulan: <?= $triwulan; ?>
                },
                "dataType": "json",
                "dataSrc": function(response) {
                    setOnProgress(false);
                    <?= mycsrfTokenName(); ?> = response.token;
                    if (response.code != "success") {
                        showSwal(response.code.ucfirst, response.message, response.code);
                    }
                    return response.data;
                },
            },
            "columnDefs": [{}, ]
        });
    }

    function reloaddTblUsersForReim() {
        dTblUsersForReim.DataTable().ajax.reload();
    };
    loaddTblUsersForReim();
</script>