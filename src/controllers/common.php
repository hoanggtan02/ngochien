<?php
	$common = [
		"data-field" => [
		    'text' => [
		        'label'   => $jatbi->lang('Nhập chữ ngắn'),
		        'type'	  => 'input',
		        'options' => [
		            'default_value' => true,
		        ],
		    ],
		    'textarea' => [
		        'label'   => $jatbi->lang('Nhập chữ dài'),
		        'type'	  => 'textarea',
		        'options' => [
		            'default_value' => true,
		        ],
		    ],
		    'number' => [
		        'label'   => $jatbi->lang('Nhập số'),
		        'type'	  => 'input',
		        'options' => [
		            'default_value' => true,
		        ],
		    ],
		    'email' => [
		        'label'   => $jatbi->lang('Nhập email'),
		        'type'	  => 'input',
		        'options' => [
		            'default_value' => true,
		        ],
		    ],
		    'tel' => [
		        'label'   => $jatbi->lang('Nhập điện thoại'),
		        'type'	  => 'input',
		        'options' => [
		            'default_value' => true,
		        ],
		    ],
		    'url' => [
		        'label'   => $jatbi->lang('Nhập đường dẫn'),
		        'type'	  => 'input',
		        'options' => [
		            'default_value' => true,
		        ],
		    ],
		    'date' => [
		        'label'   => $jatbi->lang('Nhập ngày'),
		        'type'	  => 'input',
		        'options' => [
		            'default_value' => true,
		        ],
		    ],
		    'time' => [
		        'label'   => $jatbi->lang('Nhập giờ'),
		        'type'	  => 'input',
		        'options' => [
		            'default_value' => true,
		        ],
		    ],
		    'datetime-local' => [
		        'label'   => $jatbi->lang('Nhập ngày giờ'),
		        'type'	  => 'input',
		        'options' => [
		            'default_value' => true,
		        ],
		    ],
		    'select' => [
		        'label'   => $jatbi->lang('Danh sách chọn'),
		        'type'	  => 'select',
		        'options' => [
		            'default_value' => false,
		            'choices'       => true,
		            'connect_table' => true,
		        ],
		    ],
		    'select_multi' => [
		        'label'   => $jatbi->lang('Danh sách chọn nhiều'),
		        'type'	  => 'select',
		        'options' => [
		            'default_value' => false,
		            'choices'       => true,
		            'connect_table' => true,
		            'max_selected'  => true,
		        ],
		    ],
		    'checkbox' => [
		        'label'   => $jatbi->lang('Hộp chọn nhiều'),
		        'type'	  => 'checkbox',
		        'options' => [
		            'default_value' => false,
		            'choices' => true,
		            'connect_table' => true,
		        ],
		    ],
		    'radio' => [
		        'label'   => $jatbi->lang('Hộp chọn dúng / sai'),
		        'type'	  => 'radio',
		        'options' => [
		            'default_value' => false,
		            'choices' => true,
		            'connect_table' => true,
		        ],
		    ],
		    'upload-images' => [
		        'label'   => $jatbi->lang('Tải hình ảnh'),
		        'type'	  => 'upload-images',
		        'options' => [],
		    ],
		],
		"database" => [
			"accounts" => [
				"text" => $jatbi->lang("Tài khoản"),
				"value" => 'accounts',
			],
			"customers" => [
				"text" => $jatbi->lang("Khách hàng"),
				"value" => 'customers',
			],
		],
		'weeks' => [
            $jatbi->lang("T2"), 
            $jatbi->lang("T3"), 
            $jatbi->lang("T4"), 
            $jatbi->lang("T5"), 
            $jatbi->lang("T6"), 
            $jatbi->lang("T7"), 
            $jatbi->lang("CN")
        ],
	];
	$CommonCss = [
        "assets/css/bootstrap.min.css",
        "assets/css/datatables.min.css",
        "assets/plugins/bootstrap-select/css/bootstrap-select.min.css",
        "assets/plugins/swiper/swiper-bundle.min.css",
        "assets/css/style.css",
    ];
    $CommonJs = [
        "assets/js/jquery-3.7.1.min.js",
        "assets/js/moment.min.js",
        "assets/js/bootstrap.bundle.min.js",
        "assets/js/crypto-js.min.js",
        "assets/js/sweetalert2.all.min.js",
        "assets/js/lazysizes.min.js",
        "assets/js/ls.bgset.min.js",
        "assets/js/infinite-ajax-scroll.min.js",
        "assets/js/pjax.min.js",
        "assets/js/topbar.min.js",
        "assets/js/datatables.min.js",
        "assets/plugins/swiper/swiper-bundle.min.js",
        "assets/plugins/bootstrap-select/js/bootstrap-select.min.js",
        "assets/plugins/richtexteditor/richtexteditor/rte.js",
        "assets/plugins/richtexteditor/richtexteditor/plugins/all_plugins.js",
        "assets/plugins/daterangepicker/daterangepicker.min.js",
        "assets/plugins/chartjs/chart.umd.js",
        "assets/js/main.js",
    ];
?>