<style>
    /* Class untuk tabel DataTables */
    .datatable-fixed {
        width: 100%;
        /* white-space: nowrap; */
        /* Mencegah teks terpotong */
    }

    /* Class untuk tabel header dan sel */
    .datatable-fixed th,
    .datatable-fixed td {
        text-align: left;
        /* Rata kiri (opsional) */
        padding: 8px;
        /* Spasi dalam sel */
        /* border: 1px solid #ddd; */
        /* Garis pembatas */
    }

    /* Class untuk scroll tampilan DataTables */
    /* .datatable-container { */
    /* overflow-x: auto; */
    /* Aktifkan scroll horizontal */
    /* overflow-y: hidden; */
    /* Matikan scroll vertical (opsional) */
    /* } */


    /* Pastikan latar belakang untuk kolom fixed solid */
    .dtfc-fixed-left,
    .dtfc-fixed-right {
        background-color: #ffffff;
        /* Warna latar belakang solid */
        z-index: 2;
        /* Pastikan kolom fixed berada di atas */
    }

    /* Tambahkan border pada elemen fixed */
    .dtfc-fixed-left th,
    .dtfc-fixed-left td,
    .dtfc-fixed-right th,
    .dtfc-fixed-right td {
        background-color: #ffffff;
        /* Warna latar belakang solid */
        border: 1px solid #ddd;
        /* Tambahkan border agar konsisten */
    }

    /* Perbaikan untuk header fixed */
    .dtfc-fixed-left thead th,
    .dtfc-fixed-right thead th {
        background-color: #f8f9fa;
        /* Warna khusus untuk header */
        position: sticky;
        /* Pertahankan posisi */
        z-index: 3;
    }
</style>