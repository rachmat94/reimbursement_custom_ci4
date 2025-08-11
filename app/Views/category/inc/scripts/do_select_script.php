<script>
    $("#dtbl_select_category").DataTable({
        "responsive": true,
        "order": [
            ['0', 'desc']
        ],
        fixedColumns: {
            left: 1,
        },

    });

    function selectCategory(categoryId = "", categoryCode = "", categeoryName = "", categoryDescription = "") {
        setOnProgress(true);
        $("#hdn_category_id").val(categoryId);
        $("#td_cat_id").html(categoryId);
        $("#td_cat_code").html(categoryCode);
        $("#td_cat_name").html(categeoryName);
        $("#td_cat_description").html(categoryDescription);
        $("#select_category_modal").modal("hide");
        setOnProgress(false);

    }
</script>