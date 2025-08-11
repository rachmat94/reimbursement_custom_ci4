<script>
    const dTblUsers = $("#dtbl_users");

    function loaddTblUsers() {
        setOnProgress(true);
        dTblUsers.DataTable({
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
                "url": "<?= base_url('user/dtbl_main'); ?>",
                "type": "POST",
                "data": function(d) {},
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

    function reloaddTblUsers() {
        dTblUsers.DataTable().ajax.reload();
    };
    loaddTblUsers();
</script>