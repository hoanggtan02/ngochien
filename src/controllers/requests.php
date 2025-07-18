<?php
if (!defined('ECLO'))
    die("Hacking attempt");
$requests = [
    "main" => [
        "name" => $jatbi->lang("Chính"),
        "item" => [
            '/' => [
                "menu" => $jatbi->lang("Trang chủ"),
                "url" => '/',
                "icon" => '<i class="ti ti-dashboard"></i>',
                "main" => 'true',
            ],
            'files' => [
                "menu" => $jatbi->lang("Dữ liệu"),
                "url" => '/files',
                "icon" => '<i class="ti ti-server-bolt text-success"></i>',
                "main" => 'true',
            ],
            'sales' => [
                "menu" => $jatbi->lang("Bán hàng"),
                "url" => '/invoices/sales/',
                "icon" => '<i class="fas fa-cash-register text-success"></i>',
                "hidden" => 'false',
                "main" => 'true',
            ],
            'customers' => [
                "menu" => $jatbi->lang("Khách hàng"),
                "url" => '/customers/customers/',
                "icon" => '<i class="fas fa-users text-danger"></i>',
                "hidden" => 'false',
                "main" => 'true',
            ],
        ],
    ],
    "content" => [
        "name" => $jatbi->lang("Nội dung"),
        "item" => [
            'customers' => [
                "menu" => $jatbi->lang("Khách hàng"),
                "url" => '/customers',
                "icon" => '<i class="ti ti-users-group "></i>',
                "sub" => [
                    'customers' => [
                        "name" => $jatbi->lang("Khách hàng & NCC"),
                        "router" => '/customers',
                        "icon" => '<i class="ti ti-user"></i>',
                    ],                    
                    'customers.overview' => [
                        "name" => $jatbi->lang("Tổng quan"),
                        "router" => '/customers/overview',
                        "icon" => '<i class="fas fa-universal-access"></i>',
                    ],
                    'customers.config' => [
                        "name" => $jatbi->lang("Cấu hình"),
                        "router" => '/customers/config',
                        "icon" => '<i class="fas fa-universal-access"></i>',
                    ],
                ],
                "main" => 'false',
                "permission" => [
                    'customers' => $jatbi->lang("khách hàng"),
                    'customers.add' => $jatbi->lang("Thêm khách hàng"),
                    'customers.edit' => $jatbi->lang("Sửa khách hàng"),
                    'customers.deleted' => $jatbi->lang("Xóa khách hàng"),
                    'customers.birthday' => $jatbi->lang("Sinh nhật khách hàng"),
                    'customers.overview' => $jatbi->lang("Tổng quan khách hàng"),
                    'customers.config' => $jatbi->lang("Cấu hình khách hàng"),
                    'customers.config.add' => $jatbi->lang("Thêm Cấu hình khách hàng"),
                    'customers.config.edit' => $jatbi->lang("Sửa Cấu hình khách hàng"),
                    'customers.config.deleted' => $jatbi->lang("Xóa Cấu hình khách hàng"),
                ]
            ],
            'hrm' => [
                "menu" => $jatbi->lang("Nhân sự"),
                "url" => '/hrm',
                "icon" => '<i class="ti ti-user"></i>',
                "sub" => [
                    'personnels' => [
                        "name" => $jatbi->lang("Nhân viên"),
                        "router" => '/personnels',
                        "icon" => '<i class="ti ti-user"></i>',
                    ],                    
                ],
                "main" => 'false',
                "permission" => [
                    'personnels' => $jatbi->lang("Nhân viên"),
                    'personnels.add' => $jatbi->lang("Thêm Nhân viên"),
                    'personnels.edit' => $jatbi->lang("Sửa Nhân viên"),
                    'personnels.deleted' => $jatbi->lang("Xóa Nhân viên"),

                ]
            ],
        ],
    ],
    "page" => [
        "name" => $jatbi->lang("Quản trị"),
        "item" => [
            'users' => [
                "menu" => $jatbi->lang("Người dùng"),
                "url" => '/users',
                "icon" => '<i class="ti ti-user "></i>',
                "sub" => [
                    'accounts-partner' => [
                        "name" => $jatbi->lang("Tài khoản đối tác"),
                        "router" => '/users/accounts-partner',
                        "icon" => '<i class="ti ti-user"></i>',
                    ],
                    'accounts' => [
                        "name" => $jatbi->lang("Tài khoản"),
                        "router" => '/users/accounts',
                        "icon" => '<i class="ti ti-user"></i>',
                    ],
                    'permission' => [
                        "name" => $jatbi->lang("Nhóm quyền"),
                        "router" => '/users/permission',
                        "icon" => '<i class="fas fa-universal-access"></i>',
                    ],
                ],
                "main" => 'false',
                "permission" => [
                    'accounts' => $jatbi->lang("Tài khoản"),
                    'accounts.add' => $jatbi->lang("Thêm tài khoản"),
                    'accounts.edit' => $jatbi->lang("Sửa tài khoản"),
                    'accounts.deleted' => $jatbi->lang("Xóa tài khoản"),
                    'permission' => $jatbi->lang("Nhóm quyền"),
                    'permission.add' => $jatbi->lang("Thêm Nhóm quyền"),
                    'permission.edit' => $jatbi->lang("Sửa Nhóm quyền"),
                    'permission.deleted' => $jatbi->lang("Xóa Nhóm quyền"),
                    'accounts-partner' => $jatbi->lang("Tài khoản đối tác"),
                    'accounts-partner.add' => $jatbi->lang("Thêm Tài khoản đối tác"),
                    'accounts-partner.edit' => $jatbi->lang("Sửa Tài khoản đối tác"),
                    'accounts-partner.deleted' => $jatbi->lang("Xóa Tài khoản đối tác"),
                ]
            ],
            'stores'=>[
					"menu"=> $jatbi->lang("Cửa hàng"),
					"url"=>'/stores/stores/',
					"icon"=>'<i class="ti ti-home"></i>',
					"sub"=>[
						'stores'		=>[
							"name"	=>  $jatbi->lang("Cửa hàng"),
							"router"=> '/stores/stores',
							"icon"	=> '<i class="fas fa-building"></i>',
						],
						'branch'		=>[
							"name"	=>  $jatbi->lang("Quầy hàng"),
							"router"=> '/stores/branch',
							"icon"	=> '<i class="fas fa-store-alt"></i>',
						],
						'stores-types'	=>[
							"name"	=>  $jatbi->lang("Loại cửa hàng"),
							"router"=> '/stores/stores-types',
							"icon"	=> '<i class="fas fa-stream"></i>',
						],
					],
					"main"=>'false',
					"permission"=>[
						'stores'=> $jatbi->lang("Cửa hàng"),
						'stores.add' =>  $jatbi->lang("Thêm cửa hàng"),
						'stores.edit'=> $jatbi->lang("Sửa cửa hàng"),
						'stores.deleted'=> $jatbi->lang("xóa cửa hàng"),
						'branch'=> $jatbi->lang("Quầy hàng"),
						'branch.add' =>  $jatbi->lang("Thêm quầy hàng"),
						'branch.edit'=> $jatbi->lang("sửa quầy hàng"),
						'branch.deleted'=> $jatbi->lang("xóa quầy hàng"),
						'stores-types'=> $jatbi->lang("Loại cửa hàng"),
						'stores-types.add' =>  $jatbi->lang("Thêm loại cửa hàng"),
						'stores-types.edit'=> $jatbi->lang("Sửa loại cửa hàng"),
						'stores-types.deleted'=> $jatbi->lang("xóa loại cửa hàng"),
					]
				],
            'areas' => [
                "menu" => $jatbi->lang("Khu vực"),
                "url" => '/areas/province/',
                "icon" => '<i class="ti ti-map"></i>',
                "sub" => [
                    'province'		=>[
						"name"	=> $jatbi->lang("Tỉnh thành"),
						"router"=> '/areas/province',
						"icon"	=> '<i class="fas fa-city"></i>',
					],
					'district'		=>[
						"name"	=> $jatbi->lang("Quận huyện"),
						"router"=> '/areas/district',
						"icon"	=> '<i class="fas fa-archway"></i>',
					],
					'ward'		=>[
						"name"	=> $jatbi->lang("Phường xã"),
						"router"=> '/areas/ward',
						"icon"	=> '<i class="fas fa-road"></i>',
					], 


                ],
                "main" => 'false',
                "permission" => [
                    'province' => $jatbi->lang("Tỉnh thành"),
                    'province.add' => $jatbi->lang("Thêm Tỉnh thành"),
                    'province.edit' => $jatbi->lang("Sửa Tỉnh thành"),
                    'province.deleted' => $jatbi->lang("Xóa Tỉnh thành"),
                    'district' => $jatbi->lang("Quận huyện"),
                    'district.add' => $jatbi->lang("Thêm Quận huyện"),
                    'district.edit' => $jatbi->lang("Sửa Quận huyện"),
                    'district.deleted' => $jatbi->lang("Xóa Quận huyện"),
                    'ward' => $jatbi->lang("Phường xã"),
                    'ward.add' => $jatbi->lang("Thêm Phường xã"),
                    'ward.edit' => $jatbi->lang("Sửa Phường xã"),
                    'ward.deleted' => $jatbi->lang("Xóa Phường xã"),
                ]
            ],
            'admin' => [
                "menu" => $jatbi->lang("Quản trị"),
                "url" => '/admin',
                "icon" => '<i class="ti ti-settings "></i>',
                "sub" => [
                    'plugins' => [
                        "name" => $jatbi->lang("Tiện ích mở rộng"),
                        "router" => '/admin/plugins',
                        "icon" => '<i class="fas fa-plugin"></i>',
                    ],
                    'coupons' => [
                        "name" => $jatbi->lang("Thẻ quà tặng"),
                        "router" => '/admin/coupons',
                        "icon" => '<i class="fas fa-ticket-alt"></i>',
                    ],
                    'transport' => [
                        "name" => $jatbi->lang("Vận chuyển"),
                        "router" => '/admin/transport',
                        "icon" => '<i class="fas fa-shipping-fast"></i>',
                    ],
                    'type-payments' => [
                        "name" => $jatbi->lang("Hình thức thanh toán"),
                        "router" => '/admin/type-payments',
                        "icon" => '<i class="fas fa-money-bill-wave"></i>',
                    ],
                    'flood' => [
                        "name" => $jatbi->lang("Danh sách chặn"),
                        "router" => '/admin/flood',
                        "icon" => '<i class="fas fa-shield-alt"></i>',
                    ],
                    'blockip' => [
                        "name" => $jatbi->lang("Chặn truy cập"),
                        "router" => '/admin/blockip',
                        "icon" => '<i class="fas fa-ban"></i>',
                    ],
                    'notification' => [
                        "name" => $jatbi->lang("Thông báo"),
                        "router" => '/admin/notification',
                        "icon" => '<i class="fas fa-bell"></i>',
                    ],
                    'trash' => [
                        "name" => $jatbi->lang("Thùng rác"),
                        "router" => '/admin/trash',
                        "icon" => '<i class="fa fa-list-alt"></i>',
                    ],
                    'logs' => [
                        "name" => $jatbi->lang("Nhật ký"),
                        "router" => '/admin/logs',
                        "icon" => '<i class="fa fa-list-alt"></i>',
                    ],
                    'config' => [
                        "name" => $jatbi->lang("Cấu hình"),
                        "router" => '/admin/config',
                        "icon" => '<i class="fa fa-cog"></i>',
                        "action" => 'modal',
                    ],
                ],
                "main" => 'false',
                "permission" => [
                    'plugins' => $jatbi->lang("Mở rộng"),
                    'plugins.add' => $jatbi->lang("Thêm Mở rộng"),
                    'plugins.edit' => $jatbi->lang("Sửa Mở rộng"),
                    'plugins.deleted' => $jatbi->lang("Xóa Mở rộng"),
                    'transport' => $jatbi->lang("Vận chuyển"),
                    'transport.add' => $jatbi->lang("Thêm Vận chuyển"),
                    'transport.edit' => $jatbi->lang("Sửa Vận chuyển"),
                    'transport.deleted' => $jatbi->lang("Xóa Vận chuyển"),
                    'flood' => $jatbi->lang("Danh Sách Chặn"),
                    'flood.add' => $jatbi->lang("Thêm Danh Sách Chặn"),
                    'flood.edit' => $jatbi->lang("Sửa Danh Sách Chặn"),
                    'flood.deleted' => $jatbi->lang("Xóa Danh Sách Chặn"),
                    'blockip' => $jatbi->lang("Chặn truy cập"),
                    'blockip.add' => $jatbi->lang("Thêm Chặn truy cập"),
                    'blockip.edit' => $jatbi->lang("Sửa Chặn truy cập"),
                    'blockip.deleted' => $jatbi->lang("Xóa Chặn truy cập"),
                    'notification' => $jatbi->lang("Thông Báo"),
                    'notification.add' => $jatbi->lang("Thêm Chặn Thông Báo"),
                    'notification.edit' => $jatbi->lang("Sửa Chặn Thông Báo"),
                    'notification.deleted' => $jatbi->lang("Xóa Chặn Thông Báo"),
                    'config' => $jatbi->lang("Cấu hình"),
                    'logs' => $jatbi->lang("Nhật ký"),
                    'trash' => $jatbi->lang("Thùng rác"),
                    'coupons' => $jatbi->lang("Thẻ quà tặng"),
                    'type-payments' => $jatbi->lang("Hình thức thanh toán"),
                    'type-payments.add' => $jatbi->lang("Thêm Hình thức thanh toán"),
                    'type-payments.edit' => $jatbi->lang("Sửa Hình thức thanh toán"),
                    'type-payments.deleted' => $jatbi->lang("Xóa Hình thức thanh toán"),
                ]
            ],
        ],
    ],
];
