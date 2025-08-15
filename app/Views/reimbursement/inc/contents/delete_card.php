<div class="card mt-5">
    <div class="card-body ">
        <h5 class="text-danger">
            <i class="fas fa-exclamation-triangle"></i> Hapus Data
        </h5>
        <p class="text-muted">
            Data ini akan dihapus secara permanen dan tidak dapat dipulihkan.
            Tindakan ini tidak bisa dibatalkan.
        </p>

        <form autocomplete="off" method="post" action="<?= base_url('reimbursement/do_delete'); ?>" id="delete_reimbursement_form">
            <input type="hidden" name="hdn_reim_id" value="<?= $dReimbursement['reim_id']; ?>">
            <input type="hidden" name="hdn_reim_key" value="<?= $dReimbursement['reim_key']; ?>">

            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash-alt"></i> Hapus Sekarang
            </button>
        </form>

    </div>
</div>