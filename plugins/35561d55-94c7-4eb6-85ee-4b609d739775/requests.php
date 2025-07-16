<?php
    return [
        "content" => [
            "item" => [
                "proposal" => [
                    "menu" => $jatbi->lang("Đề xuất"),
                    "url" => "/proposal",
                    "icon" => '<i class="ti ti-file-like"></i>',
                    "main" => 'false',
                    "sub" => [
                        'proposal'      =>[
                            "name"  => $jatbi->lang("Đề xuất"),
                            "router"=> '/proposal',
                            "icon"  => '<i class="ti ti-file-like"></i>',
                        ],
                        'proposal.report'      =>[
                            "name"  => $jatbi->lang("Báo cáo"),
                            "router"=> '/proposal/report',
                            "icon"  => '<i class="ti ti-chart-pie"></i>',
                        ],
                        'proposal.config'      =>[
                            "name"  => $jatbi->lang("Cấu hình"),
                            "router"=> '/proposal/config',
                            "icon"  => '<i class="ti ti-settings-2"></i>',
                        ],
                    ],
                    "permission"=>[
                        'proposal'=> $jatbi->lang("Xem Đề xuất"),
                        'proposal.add' => $jatbi->lang("Thêm Đề xuất"),
                        'proposal.edit' => $jatbi->lang("Sửa Đề xuất"),
                        'proposal.deleted' => $jatbi->lang("Xóa Đề xuất"),
                        'proposal.report'=> $jatbi->lang("Báo Cáo Đề xuất"),
                        'proposal.config'=> $jatbi->lang("Cấu hình Đề xuất"),
                        'proposal.config.add' => $jatbi->lang("Thêm Cấu Hình Đề xuất"),
                        'proposal.config.edit' => $jatbi->lang("Sửa Cấu Hình Đề xuất"),
                        'proposal.config.deleted' => $jatbi->lang("Xóa Cấu Hình Đề xuất"),

                    ],
                ],
                "cash-flow" => [
                    "menu" => $jatbi->lang("Dòng tiền"),
                    "url" => "/cash-flow",
                    "icon" => '<i class="ti ti-cash"></i>',
                    "sub" => [
                        'cash-flow.private'      =>[
                            "name"  => $jatbi->lang("Kế hoạch cá nhân"),
                            "router"=> '/cash-flow/private',
                            "icon"  => '<i class="ti ti-user"></i>',
                        ],
                    ],
                    "main" => 'false',
                    "permission"=>[
                        'cash-flow.private'=> $jatbi->lang("Xem Kế hoạch cá nhân"),
                        'cash-flow.private.add' => $jatbi->lang("Thêm Kế hoạch cá nhân"),
                        'cash-flow.private.edit' => $jatbi->lang("Sửa Kế hoạch cá nhân"),
                        'cash-flow.private.deleted' => $jatbi->lang("Xóa Kế hoạch cá nhân"),

                    ],
                ],
            ],
        ],
    ];

?>
