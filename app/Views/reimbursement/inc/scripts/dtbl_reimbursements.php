<script>
    const dTblReimbursements = $("#dtbl_reimbursement");

    function loaddTblReimbursements() {
        setOnProgress(true);
        dTblReimbursements.DataTable({
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
                "url": "<?= base_url('reimbursement/dtbl_reimbursements'); ?>",
                "type": "POST",
                "data": {},
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

    function reloaddTblReimbursements() {
        dTblReimbursements.DataTable().ajax.reload();
    };
    loaddTblReimbursements();
</script>