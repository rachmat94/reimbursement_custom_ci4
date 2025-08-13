<?php
function masterJenisGroup($type = "", bool $forceSingle = false)
{
    $data = [
        "lainnya" => [
            "code" => "lainnya",
            "label" => "Lainnya",
            "color" => "dark",
            "description" => "Jenis group lain yang tidak termasuk dalam kategori utama.",
        ],

        "jenis_group_1" => [
            "code" => "jenis_group_1",
            "label" => "Jenis Group 1",
            "color" => "primary",
            "description" => "Jenis Group 1, deskripsi bisa disesuaikan.",
        ],
        "jenis_group_2" => [
            "code" => "jenis_group_2",
            "label" => "Jenis Group 2",
            "color" => "secondary",
            "description" => "Jenis Group 2, deskripsi bisa disesuaikan.",
        ],
        "jenis_group_3" => [
            "code" => "jenis_group_3",
            "label" => "Jenis Group 3",
            "color" => "success",
            "description" => "Jenis Group 3, deskripsi bisa disesuaikan.",
        ],
        "jenis_group_4" => [
            "code" => "jenis_group_4",
            "label" => "Jenis Group 4",
            "color" => "warning",
            "description" => "Jenis Group 4, deskripsi bisa disesuaikan.",
        ],
        "jenis_group_5" => [
            "code" => "jenis_group_5",
            "label" => "Jenis Group 5",
            "color" => "danger",
            "description" => "Jenis Group 5, deskripsi bisa disesuaikan.",
        ],
    ];

    if ($type != "") {
        if (array_key_exists(strtolower($type), $data)) {
            return $data[strtolower($type)];
        } else {
            return [
                "code" => "unknown",
                "label" => "Unknown",
                "color" => "secondary",
                "description" => "",
            ];
        }
    }

    if ($forceSingle) {
        return [
            "code" => "unknown",
            "label" => "Unknown",
            "color" => "secondary",
            "description" => "",
        ];
    }

    return $data;
}

function masterReimbursementStatus($type = "", bool $forceSingle = false)
{
    $data = [
        "draft" => [
            "code" => "draft",
            "label" => "Draft",
            "color" => "blue",
            "description" => "",
        ],
        "diajukan" => [
            "code" => "diajukan",
            "label" => "Diajukan",
            "color" => "primary",
            "description" => "",
        ],
        "validasi" => [
            "code" => "validasi",
            "label" => "Validasi",
            "color" => "info",
            "description" => "",
        ],
        "revisi" => [
            "code" => "revisi",
            "label" => "Revisi",
            "color" => "warning",
            "description" => "",
        ],
        "disetujui" => [
            "code" => "disetujui",
            "label" => "Disetujui",
            "color" => "success",
            "description" => "",
        ],
        "selesai" => [
            "code" => "selesai",
            "label" => "Selesai",
            "color" => "success",
            "description" => "",
        ],
    ];
    if ($type != "") {
        if (in_array(strtolower($type), array_keys($data))) {
            return $data[$type];
        } else {
            return [
                "code" => "unknown",
                "label" => "Unknown",
                "color" => "secondary",
                "description" => "",
            ];
        }
    }
    if ($forceSingle) {
        return [
            "code" => "unknown",
            "label" => "Unknown",
            "color" => "secondary",
            "description" => "",
        ];
    }
    return $data;
}


function masterUserCategoryInGroup($type = "", bool $forceSingle = false)
{
    $data = [
        "anggota" => [
            "code" => "anggota",
            "label" => "Anggota",
            "color" => "primary",
            "description" => "",
        ],
        "ketua" => [
            "code" => "ketua",
            "label" => "Ketua",
            "color" => "danger",
            "description" => "",
        ],
    ];
    if ($type != "") {
        if (in_array(strtolower($type), array_keys($data))) {
            return $data[$type];
        } else {
            return [
                "code" => "unknown",
                "label" => "Unknown",
                "color" => "secondary",
                "description" => "",
            ];
        }
    }
    if ($forceSingle) {
        return [
            "code" => "unknown",
            "label" => "Unknown",
            "color" => "secondary",
            "description" => "",
        ];
    }
    return $data;
}

function masterUserRole($type = "", bool $forceSingle = false)
{
    $data = [
        "user" => [
            "code" => "user",
            "label" => "User",
            "color" => "secondary",
            "description" => "",
        ],
        "admin_group" => [
            "code" => "admin_group",
            "label" => "Admin Grup",
            "color" => "dark",
            "description" => "",
        ],
        "validator" => [
            "code" => "validator",
            "label" => "Admin Validasi",
            "color" => "warning",
            "description" => "",
        ],
        "super_user" => [
            "code" => "super_user",
            "label" => "Super User",
            "color" => "danger",
            "description" => "",
        ],
    ];
    if ($type != "") {
        if (in_array(strtolower($type), array_keys($data))) {
            return $data[$type];
        } else {
            return [
                "code" => "unknown",
                "label" => "Unknown",
                "color" => "secondary",
                "description" => "",
            ];
        }
    }
    if ($forceSingle) {
        return [
            "code" => "unknown",
            "label" => "Unknown",
            "color" => "secondary",
            "description" => "",
        ];
    }
    return $data;
}
