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
            'crafting' => [
                "menu" => $jatbi->lang("Chế tác"),
                "url" => '/crafting',
                "icon" => '<i class="ti ti-hammer"></i>',
                "sub" => [
                    'craftinggold' => [
                        "name" => $jatbi->lang("Kho chế tác vàng"),
                        "router" => '/crafting/craftinggold',
                        "icon" => '<i class="ti ti-box" style="color: #f1c40f;"></i>',
                    ],
                    'craftingsilver' => [
                        "name" => $jatbi->lang("Kho chế tác bạc"),
                        "router" => '/crafting/craftingsilver',
                        "icon" => '<i class="ti ti-box" style="color: #bdc3c7;"></i>',
                    ],
                    'craftingchain' => [
                        "name" => $jatbi->lang("Kho chế tác chuỗi"),
                        "router" => '/crafting/craftingchain',
                        "icon" => '<i class="ti ti-link"></i>',
                    ],
                    'goldsmithing' => [
                        "name" => $jatbi->lang("Chế tác vàng"),
                        "router" => '/crafting/goldsmithing',
                        "icon" => '<i class="ti ti-flame" style="color: #f1c40f;"></i>',
                    ],
                    'silversmithing' => [
                        "name" => $jatbi->lang("Chế tác bạc"),
                        "router" => '/crafting/silversmithing',
                        "icon" => '<i class="ti ti-flame" style="color: #bdc3c7;"></i>',
                    ],
                    'chainmaking' => [
                        "name" => $jatbi->lang("Chế tác chuỗi"),
                        "router" => '/crafting/chainmaking',
                        "icon" => '<i class="ti ti-flame"></i>',
                    ],
                    'fixed' => [
                        "name" => $jatbi->lang("Kho sửa chế tác"),
                        "router" => '/crafting/fixed',
                        "icon" => '<i class="ti ti-tool"></i>',
                    ],
                ],
                "main" => 'false',
                "permission" => [
                    'craftinggold' => $jatbi->lang("Xem kho chế tác vàng"),
                    'craftinggold.add' => $jatbi->lang("Thêm vào kho chế tác vàng"),
                    'craftinggold.edit' => $jatbi->lang("Sửa trong kho chế tác vàng"),
                    'craftinggold.deleted' => $jatbi->lang("Xóa khỏi kho chế tác vàng"),

                    'craftingsilver' => $jatbi->lang("Xem kho chế tác bạc"),
                    'craftingsilver.add' => $jatbi->lang("Thêm vào kho chế tác bạc"),
                    'craftingsilver.edit' => $jatbi->lang("Sửa trong kho chế tác bạc"),
                    'craftingsilver.deleted' => $jatbi->lang("Xóa khỏi kho chế tác bạc"),

                    'craftingchain' => $jatbi->lang("Xem kho chế tác chuỗi"),
                    'craftingchain.add' => $jatbi->lang("Thêm vào kho chế tác chuỗi"),
                    'craftingchain.edit' => $jatbi->lang("Sửa trong kho chế tác chuỗi"),
                    'craftingchain.deleted' => $jatbi->lang("Xóa khỏi kho chế tác chuỗi"),

                    'goldsmithing' => $jatbi->lang("Xem lệnh chế tác vàng"),
                    'goldsmithing.add' => $jatbi->lang("Tạo lệnh chế tác vàng"),
                    'goldsmithing.edit' => $jatbi->lang("Sửa lệnh chế tác vàng"),
                    'goldsmithing.deleted' => $jatbi->lang("Xóa lệnh chế tác vàng"),
                    'goldsmithing.approve' => $jatbi->lang("Duyệt lệnh chế tác vàng"),

                    'silversmithing' => $jatbi->lang("Xem lệnh chế tác bạc"),
                    'silversmithing.add' => $jatbi->lang("Tạo lệnh chế tác bạc"),
                    'silversmithing.edit' => $jatbi->lang("Sửa lệnh chế tác bạc"),
                    'silversmithing.deleted' => $jatbi->lang("Xóa lệnh chế tác bạc"),
                    'silversmithing.approve' => $jatbi->lang("Duyệt lệnh chế tác bạc"),

                    'chainmaking' => $jatbi->lang("Xem lệnh chế tác chuỗi"),
                    'chainmaking.add' => $jatbi->lang("Tạo lệnh chế tác chuỗi"),
                    'chainmaking.edit' => $jatbi->lang("Sửa lệnh chế tác chuỗi"),
                    'chainmaking.deleted' => $jatbi->lang("Xóa lệnh chế tác chuỗi"),
                    'chainmaking.approve' => $jatbi->lang("Duyệt lệnh chế tác chuỗi"),

                    'fixed' => $jatbi->lang("Xem kho sửa chế tác"),
                    'fixed.add' => $jatbi->lang("Thêm vào kho sửa chế tác"),
                    'fixed.edit' => $jatbi->lang("Sửa trong kho sửa chế tác"),
                    'fixed.deleted' => $jatbi->lang("Xóa khỏi kho sửa chế tác"),
                ]
            ],
            'drivers' => [
                "menu" => $jatbi->lang("Nội bộ"),
                "url" => '/drivers',
                "icon" => '<i class="ti ti-steering-wheel"></i>',
                "sub" => [
                    'invoices' => [
                        "name" => $jatbi->lang("Đơn hàng"),
                        "router" => '/drivers/invoices',
                        "icon" => '<i class="ti ti-file-text"></i>',
                    ],
                    'driver' => [
                        "name" => $jatbi->lang("Thông tin tài xế"),
                        "router" => '/drivers/driver',
                        "icon" => '<i class="ti ti-user-circle"></i>',
                    ],
                    'driver-payment' => [
                        "name" => $jatbi->lang("Thanh toán tài xế"),
                        "router" => '/drivers/driver-payment',
                        "icon" => '<i class="ti ti-receipt"></i>',
                    ],
                    'other_commission_costs' => [
                        "name" => $jatbi->lang("Thanh toán hoa hồng khác"),
                        "router" => '/drivers/other_commission_costs',
                        "icon" => '<i class="ti ti-wallet"></i>',
                    ],
                ],
                "main" => 'false',
                "permission" => [
                    'invoices' => $jatbi->lang("Xem đơn hàng nội bộ"),
                    'invoices.add' => $jatbi->lang("Thêm đơn hàng nội bộ"),
                    'invoices.edit' => $jatbi->lang("Sửa đơn hàng nội bộ"),
                    'invoices.deleted' => $jatbi->lang("Xóa đơn hàng nội bộ"),

                    'driver' => $jatbi->lang("Xem thông tin tài xế"),
                    'driver.add' => $jatbi->lang("Thêm thông tin tài xế"),
                    'driver.edit' => $jatbi->lang("Sửa thông tin tài xế"),
                    'driver.deleted' => $jatbi->lang("Xóa thông tin tài xế"),

                    'driver-payment' => $jatbi->lang("Xem thanh toán tài xế"),
                    'driver-payment.add' => $jatbi->lang("Tạo thanh toán tài xế"),
                    'driver-payment.edit' => $jatbi->lang("Sửa thanh toán tài xế"),
                    'driver-payment.deleted' => $jatbi->lang("Xóa thanh toán tài xế"),
                    'driver-payment.confirm' => $jatbi->lang("Xác nhận thanh toán tài xế"),

                    'other_commission_costs' => $jatbi->lang("Xem thanh toán hoa hồng khác"),
                    'other_commission_costs.add' => $jatbi->lang("Tạo thanh toán hoa hồng khác"),
                    'other_commission_costs.edit' => $jatbi->lang("Sửa thanh toán hoa hồng khác"),
                    'other_commission_costs.deleted' => $jatbi->lang("Xóa thanh toán hoa hồng khác"),
                ]
            ],
            'purchases' => [
                "menu" => $jatbi->lang("Mua hàng"),
                "url" => '/purchases',
                "icon" => '<i class="ti ti-shopping-cart"></i>',
                "sub" => [
                    'purchase' => [
                        "name" => $jatbi->lang("Đề xuất mua hàng"),
                        "router" => '/purchases/purchase',
                        "icon" => '<i class="ti ti-file-text"></i>',
                    ],
                    'vendors' => [
                        "name" => $jatbi->lang("Nhà cung cấp"),
                        "router" => '/purchases/vendors',
                        "icon" => '<i class="ti ti-truck-delivery"></i>',
                    ],
                    'vendors-types' => [
                        "name" => $jatbi->lang("Loại nhà cung cấp"),
                        "router" => '/purchases/vendors-types',
                        "icon" => '<i class="ti ti-tags"></i>',
                    ],
                ],
                "main" => 'false',
                "permission" => [
                    'purchase' => $jatbi->lang("Xem đề xuất mua hàng"),
                    'purchase.add' => $jatbi->lang("Thêm đề xuất mua hàng"),
                    'purchase.edit' => $jatbi->lang("Sửa đề xuất mua hàng"),
                    'purchase.deleted' => $jatbi->lang("Xóa đề xuất mua hàng"),
                    'purchase.approve' => $jatbi->lang("Duyệt đề xuất mua hàng"),

                    'vendors' => $jatbi->lang("Xem nhà cung cấp"),
                    'vendors.add' => $jatbi->lang("Thêm nhà cung cấp"),
                    'vendors.edit' => $jatbi->lang("Sửa nhà cung cấp"),
                    'vendors.deleted' => $jatbi->lang("Xóa nhà cung cấp"),

                    'vendors-types' => $jatbi->lang("Xem loại nhà cung cấp"),
                    'vendors-types.add' => $jatbi->lang("Thêm loại nhà cung cấp"),
                    'vendors-types.edit' => $jatbi->lang("Sửa loại nhà cung cấp"),
                    'vendors-types.deleted' => $jatbi->lang("Xóa loại nhà cung cấp"),
                ]
            ],
            'accountants' => [
                "menu" => $jatbi->lang("Kế toán"),
                "url" => '/accountants',
                "icon" => '<i class="ti ti-calculator"></i>',
                "sub" => [
                    'expenditure' => [
                        "name" => $jatbi->lang("Thu chi"),
                        "router" => '/accountants/expenditure',
                        "icon" => '<i class="ti ti-arrows-exchange"></i>',
                    ],
                    'expenditure_report' => [
                        "name" => $jatbi->lang("Quỹ tiền mặt"),
                        "router" => '/accountants/expenditure_report',
                        "icon" => '<i class="ti ti-wallet"></i>',
                    ],
                    'deposit_book' => [
                        "name" => $jatbi->lang("Số tiền gửi"),
                        "router" => '/accountants/deposit_book',
                        "icon" => '<i class="ti ti-building-bank"></i>',
                    ],
                    'accounts-code' => [
                        "name" => $jatbi->lang("Mã tài khoản"),
                        "router" => '/accountants/accounts-code',
                        "icon" => '<i class="ti ti-list-numbers"></i>',
                    ],
                    'accountant' => [
                        "name" => $jatbi->lang("Hạch toán"),
                        "router" => '/accountants/accountant',
                        "icon" => '<i class="ti ti-notebook"></i>',
                    ],
                    'financial_paper' => [
                        "name" => $jatbi->lang("Chứng từ kế toán"),
                        "router" => '/accountants/financial_paper',
                        "icon" => '<i class="ti ti-file-text"></i>',
                    ],
                    'accounts_receivable' => [
                        "name" => $jatbi->lang("Chi tiết công nợ phải thu"),
                        "router" => '/accountants/accounts_receivable',
                        "icon" => '<i class="ti ti-file-analytics"></i>',
                    ],
                    'subsidiary_ledger' => [
                        "name" => $jatbi->lang("Sổ chi tiết tài khoản"),
                        "router" => '/accountants/subsidiary_ledger',
                        "icon" => '<i class="ti ti-book-2"></i>',
                    ],
                    'income_statement' => [
                        "name" => $jatbi->lang("Báo cáo kết quả kinh doanh"),
                        "router" => '/accountants/income_statement',
                        "icon" => '<i class="ti ti-report-analytics"></i>',
                    ],
                    'aggregate_cost' => [
                        "name" => $jatbi->lang("Tổng hợp chi phí"),
                        "router" => '/accountants/aggregate_cost',
                        "icon" => '<i class="ti ti-sum"></i>',
                    ],
                    'inventory_table' => [
                        "name" => $jatbi->lang("Bản kiểm kê"),
                        "router" => '/accountants/inventory_table',
                        "icon" => '<i class="ti ti-clipboard-list"></i>',
                    ],
                ],
                "main" => 'false',
                "permission" => [
                    'expenditure' => $jatbi->lang("Xem thu chi"),
                    'expenditure.add' => $jatbi->lang("Thêm phiếu thu chi"),
                    'expenditure.edit' => $jatbi->lang("Sửa phiếu thu chi"),
                    'expenditure.deleted' => $jatbi->lang("Xóa phiếu thu chi"),

                    'expenditure_report' => $jatbi->lang("Xem báo cáo quỹ tiền mặt"),
                    'expenditure_report.add' => $jatbi->lang("Thêm báo cáo quỹ tiền mặt"),
                    'expenditure_report.edit' => $jatbi->lang("Sửa báo cáo quỹ tiền mặt"),
                    'expenditure_report.deleted' => $jatbi->lang("Xóa báo cáo quỹ tiền mặt"),

                    'deposit_book' => $jatbi->lang("Xem sổ tiền gửi"),
                    'deposit_book.add' => $jatbi->lang("Thêm sổ tiền gửi"),
                    'deposit_book.edit' => $jatbi->lang("Sửa sổ tiền gửi"),
                    'deposit_book.deleted' => $jatbi->lang("Xóa sổ tiền gửi"),

                    'accounts-code' => $jatbi->lang("Xem mã tài khoản"),
                    'accounts-code.add' => $jatbi->lang("Thêm mã tài khoản"),
                    'accounts-code.edit' => $jatbi->lang("Sửa mã tài khoản"),
                    'accounts-code.deleted' => $jatbi->lang("Xóa mã tài khoản"),

                    'accountant' => $jatbi->lang("Xem hạch toán"),
                    'accountant.add' => $jatbi->lang("Thêm hạch toán"),
                    'accountant.edit' => $jatbi->lang("Sửa hạch toán"),
                    'accountant.deleted' => $jatbi->lang("Xóa hạch toán"),

                    'financial_paper' => $jatbi->lang("Xem chứng từ kế toán"),
                    'financial_paper.add' => $jatbi->lang("Thêm chứng từ kế toán"),
                    'financial_paper.edit' => $jatbi->lang("Sửa chứng từ kế toán"),
                    'financial_paper.deleted' => $jatbi->lang("Xóa chứng từ kế toán"),

                    'accounts_receivable' => $jatbi->lang("Xem công nợ phải thu"),
                    'accounts_receivable.add' => $jatbi->lang("Thêm công nợ phải thu"),
                    'accounts_receivable.edit' => $jatbi->lang("Sửa công nợ phải thu"),
                    'accounts_receivable.deleted' => $jatbi->lang("Xóa công nợ phải thu"),

                    'subsidiary_ledger' => $jatbi->lang("Xem sổ chi tiết tài khoản"),
                    'subsidiary_ledger.add' => $jatbi->lang("Thêm sổ chi tiết tài khoản"),
                    'subsidiary_ledger.edit' => $jatbi->lang("Sửa sổ chi tiết tài khoản"),
                    'subsidiary_ledger.deleted' => $jatbi->lang("Xóa sổ chi tiết tài khoản"),

                    'income_statement' => $jatbi->lang("Xem báo cáo KQKD"),
                    'income_statement.add' => $jatbi->lang("Thêm báo cáo KQKD"),
                    'income_statement.edit' => $jatbi->lang("Sửa báo cáo KQKD"),
                    'income_statement.deleted' => $jatbi->lang("Xóa báo cáo KQKD"),

                    'aggregate_cost' => $jatbi->lang("Xem tổng hợp chi phí"),
                    'aggregate_cost.add' => $jatbi->lang("Thêm tổng hợp chi phí"),
                    'aggregate_cost.edit' => $jatbi->lang("Sửa tổng hợp chi phí"),
                    'aggregate_cost.deleted' => $jatbi->lang("Xóa tổng hợp chi phí"),

                    'inventory_table' => $jatbi->lang("Xem bảng kiểm kê"),
                    'inventory_table.add' => $jatbi->lang("Thêm bảng kiểm kê"),
                    'inventory_table.edit' => $jatbi->lang("Sửa bảng kiểm kê"),
                    'inventory_table.deleted' => $jatbi->lang("Xóa bảng kiểm kê"),
                ]
            ],
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
                    'customers-card' => [
                        "name" => $jatbi->lang("Thẻ khách hàng"),
                        "router" => '/customers/customers-card',
                        "icon" => '<i class="fas fa-universal-access"></i>',
                    ],
                    'sources' => [
                        "name" => $jatbi->lang("Nguồn kênh"),
                        "router" => '/customers/sources',
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
                    'sources' => $jatbi->lang("Nguồn kênh"),
                    'sources.add' => $jatbi->lang("Thêm Nguồn kênh"),
                    'sources.edit' => $jatbi->lang("Sửa Nguồn kênh"),
                    'sources.deleted' => $jatbi->lang("Xóa Nguồn kênh"),
                    'customers-card' => $jatbi->lang("Thẻ khách hàng"),
                    'customers-card.add' => $jatbi->lang("Thêm Thẻ khách hàng"),
                    'customers-card.edit' => $jatbi->lang("Sửa Thẻ khách hàng"),
                    'customers-card.deleted' => $jatbi->lang("Xóa Thẻ khách hàng"),
                ]
            ],
            'reports' => [
                "menu" => $jatbi->lang("Báo cáo"),
                "url" => '/reports',
                "icon" => '<i class="ti ti-chart-bar"></i>',
                "sub" => [
                    'revenue' => [
                        "name" => $jatbi->lang("Doanh thu"),
                        "router" => '/reports/revenue',
                        "icon" => '<i class="ti ti-report-money"></i>',
                    ],
                    'revenue_personnels' => [
                        "name" => $jatbi->lang("Doanh thu nhân viên"),
                        "router" => '/reports/revenue_personnels',
                        "icon" => '<i class="ti ti-users"></i>',
                    ],
                    'liabilities' => [
                        "name" => $jatbi->lang("Công nợ"),
                        "router" => '/reports/liabilities',
                        "icon" => '<i class="ti ti-file-invoice"></i>',
                    ],
                    'purchases-liabilities' => [
                        "name" => $jatbi->lang("Công nợ mua hàng"),
                        "router" => '/reports/purchases-liabilities',
                        "icon" => '<i class="ti ti-receipt-2"></i>',
                    ],
                    'purchases-revenue' => [
                        "name" => $jatbi->lang("Chi phí mua hàng"),
                        "router" => '/reports/purchases-revenue',
                        "icon" => '<i class="ti ti-shopping-cart-x"></i>',
                    ],
                    'inventory' => [
                        "name" => $jatbi->lang("Xuất nhập tồn"),
                        "router" => '/reports/inventory',
                        "icon" => '<i class="ti ti-package"></i>',
                    ],
                    'inventory_ingredient' => [
                        "name" => $jatbi->lang("Xuất nhập tồn nguyên liệu"),
                        "router" => '/reports/inventory_ingredient',
                        "icon" => '<i class="ti ti-flask"></i>',
                    ],
                    'inventory_crafting' => [
                        "name" => $jatbi->lang("Xuất nhập tồn kho chế tác"),
                        "router" => '/reports/inventory_crafting',
                        "icon" => '<i class="ti ti-diamond"></i>',
                    ],
                    'selling_products' => [
                        "name" => $jatbi->lang("Sản phẩm bán chạy"),
                        "router" => '/reports/selling_products',
                        "icon" => '<i class="ti ti-trending-up"></i>',
                    ],
                ],
                "main" => 'false',
                "permission" => [
                    'revenue' => $jatbi->lang("Xem báo cáo doanh thu"),
                    'revenue.add' => $jatbi->lang("Thêm báo cáo doanh thu"),
                    'revenue.edit' => $jatbi->lang("Sửa báo cáo doanh thu"),
                    'revenue.deleted' => $jatbi->lang("Xóa báo cáo doanh thu"),

                    'revenue_personnels' => $jatbi->lang("Xem báo cáo doanh thu nhân viên"),
                    'revenue_personnels.add' => $jatbi->lang("Thêm báo cáo doanh thu nhân viên"),
                    'revenue_personnels.edit' => $jatbi->lang("Sửa báo cáo doanh thu nhân viên"),
                    'revenue_personnels.deleted' => $jatbi->lang("Xóa báo cáo doanh thu nhân viên"),

                    'liabilities' => $jatbi->lang("Xem báo cáo công nợ"),
                    'liabilities.add' => $jatbi->lang("Thêm báo cáo công nợ"),
                    'liabilities.edit' => $jatbi->lang("Sửa báo cáo công nợ"),
                    'liabilities.deleted' => $jatbi->lang("Xóa báo cáo công nợ"),

                    'purchases-liabilities' => $jatbi->lang("Xem báo cáo công nợ mua hàng"),
                    'purchases-liabilities.add' => $jatbi->lang("Thêm báo cáo công nợ mua hàng"),
                    'purchases-liabilities.edit' => $jatbi->lang("Sửa báo cáo công nợ mua hàng"),
                    'purchases-liabilities.deleted' => $jatbi->lang("Xóa báo cáo công nợ mua hàng"),

                    'purchases-revenue' => $jatbi->lang("Xem báo cáo chi phí mua hàng"),
                    'purchases-revenue.add' => $jatbi->lang("Thêm báo cáo chi phí mua hàng"),
                    'purchases-revenue.edit' => $jatbi->lang("Sửa báo cáo chi phí mua hàng"),
                    'purchases-revenue.deleted' => $jatbi->lang("Xóa báo cáo chi phí mua hàng"),

                    'inventory' => $jatbi->lang("Xem báo cáo xuất nhập tồn"),
                    'inventory.add' => $jatbi->lang("Thêm báo cáo xuất nhập tồn"),
                    'inventory.edit' => $jatbi->lang("Sửa báo cáo xuất nhập tồn"),
                    'inventory.deleted' => $jatbi->lang("Xóa báo cáo xuất nhập tồn"),

                    'inventory_ingredient' => $jatbi->lang("Xem báo cáo XNT nguyên liệu"),
                    'inventory_ingredient.add' => $jatbi->lang("Thêm báo cáo XNT nguyên liệu"),
                    'inventory_ingredient.edit' => $jatbi->lang("Sửa báo cáo XNT nguyên liệu"),
                    'inventory_ingredient.deleted' => $jatbi->lang("Xóa báo cáo XNT nguyên liệu"),

                    'inventory_crafting' => $jatbi->lang("Xem báo cáo XNT kho chế tác"),
                    'inventory_crafting.add' => $jatbi->lang("Thêm báo cáo XNT kho chế tác"),
                    'inventory_crafting.edit' => $jatbi->lang("Sửa báo cáo XNT kho chế tác"),
                    'inventory_crafting.deleted' => $jatbi->lang("Xóa báo cáo XNT kho chế tác"),

                    'selling_products' => $jatbi->lang("Xem báo cáo sản phẩm bán chạy"),
                    'selling_products.add' => $jatbi->lang("Thêm báo cáo sản phẩm bán chạy"),
                    'selling_products.edit' => $jatbi->lang("Sửa báo cáo sản phẩm bán chạy"),
                    'selling_products.deleted' => $jatbi->lang("Xóa báo cáo sản phẩm bán chạy"),
                ]
            ],
            'hrm' => [
                "menu" => $jatbi->lang("Nhân sự"),
                "url" => '/hrm',
                "icon" => '<i class="ti ti-user"></i>',
                "sub" => [
                    'personnels' => [
                        "name" => $jatbi->lang("Nhân viên"),
                        "router" => '/hrm/personnels',
                        "icon" => '<i class="ti ti-user"></i>',
                    ],
                    'offices' => [
                        "name" => $jatbi->lang("Phòng ban"),
                        "router" => '/hrm/offices',
                        "icon" => '<i class="ti ti-user"></i>',
                    ],
                ],
                "main" => 'false',
                "permission" => [
                    'offices' => $jatbi->lang("Phòng ban"),
                    'offices.add' => $jatbi->lang("Thêm phòng ban"),
                    'offices.edit' => $jatbi->lang("Sửa phòng ban"),
                    'offices.deleted' => $jatbi->lang("Xóa phòng ban"),
                    'personnels' => $jatbi->lang("Phòng ban"),
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
            'stores' => [
                "menu" => $jatbi->lang("Cửa hàng"),
                "url" => '/stores/stores/',
                "icon" => '<i class="ti ti-home"></i>',
                "sub" => [
                    'stores' => [
                        "name" => $jatbi->lang("Cửa hàng"),
                        "router" => '/stores/stores',
                        "icon" => '<i class="fas fa-building"></i>',
                    ],
                    'branch' => [
                        "name" => $jatbi->lang("Quầy hàng"),
                        "router" => '/stores/branch',
                        "icon" => '<i class="fas fa-store-alt"></i>',
                    ],
                    'stores-types' => [
                        "name" => $jatbi->lang("Loại cửa hàng"),
                        "router" => '/stores/stores-types',
                        "icon" => '<i class="fas fa-stream"></i>',
                    ],
                ],
                "main" => 'false',
                "permission" => [
                    'stores' => $jatbi->lang("Cửa hàng"),
                    'stores.add' => $jatbi->lang("Thêm cửa hàng"),
                    'stores.edit' => $jatbi->lang("Sửa cửa hàng"),
                    'stores.deleted' => $jatbi->lang("xóa cửa hàng"),
                    'branch' => $jatbi->lang("Quầy hàng"),
                    'branch.add' => $jatbi->lang("Thêm quầy hàng"),
                    'branch.edit' => $jatbi->lang("sửa quầy hàng"),
                    'branch.deleted' => $jatbi->lang("xóa quầy hàng"),
                    'stores-types' => $jatbi->lang("Loại cửa hàng"),
                    'stores-types.add' => $jatbi->lang("Thêm loại cửa hàng"),
                    'stores-types.edit' => $jatbi->lang("Sửa loại cửa hàng"),
                    'stores-types.deleted' => $jatbi->lang("xóa loại cửa hàng"),
                ]
            ],
            'areas' => [
                "menu" => $jatbi->lang("Khu vực"),
                "url" => '/areas/province/',
                "icon" => '<i class="ti ti-map"></i>',
                "sub" => [
                    'province' => [
                        "name" => $jatbi->lang("Tỉnh thành"),
                        "router" => '/areas/province',
                        "icon" => '<i class="fas fa-city"></i>',
                    ],
                    'district' => [
                        "name" => $jatbi->lang("Quận huyện"),
                        "router" => '/areas/district',
                        "icon" => '<i class="fas fa-archway"></i>',
                    ],
                    'ward' => [
                        "name" => $jatbi->lang("Phường xã"),
                        "router" => '/areas/ward',
                        "icon" => '<i class="fas fa-road"></i>',
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
