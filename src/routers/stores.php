<?php
$app->group($setting['manager'] . "/stores", function ($app) use ($jatbi, $setting) {
    $app->router('/stores', ['GET', 'POST'], function ($vars) use ($app, $jatbi, $setting) {
        $jatbi->permission('stores');
        $vars['title'] = $jatbi->lang("Cửa hàng");

        if ($app->method() === 'GET') {
            echo $app->render($setting['template'] . '/stores/stores.html', $vars);
        } elseif ($app->method() === 'POST') {
            $app->header(['Content-Type' => 'application/json; charset=utf-8']);

            $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
            $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
            $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
            $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
            $statusValue = isset($_POST['status']) ? $_POST['status'] : '';
            $orderName = isset($_POST['order'][0]['name']) ? $_POST['order'][0]['name'] : 'id';
            $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';

            $where = [
                "AND" => [
                    "stores.deleted" => 0,
                ],
                "LIMIT" => [$start, $length],
                "ORDER" => [$orderName => strtoupper($orderDir)]
            ];

            if ($searchValue != '') {
                $where['AND']['OR'] = [
                    'stores.name[~]' => $searchValue,
                    'stores.code[~]' => $searchValue,
                    'stores.phone[~]' => $searchValue,
                    'stores.email[~]' => $searchValue,
                    'stores.address[~]' => $searchValue,
                ];
            }

            if ($statusValue != '') {
                $where['AND']['stores.status'] = $statusValue;
            }

            $countWhere = [
                "AND" => array_merge(
                    ["stores.deleted" => 0],
                    $searchValue != '' ? ["OR" => [
                        'stores.name[~]' => $searchValue,
                        'stores.code[~]' => $searchValue,
                        'stores.phone[~]' => $searchValue,
                        'stores.email[~]' => $searchValue,
                        'stores.address[~]' => $searchValue,
                    ]] : [],
                    $statusValue != '' ? ["stores.status" => $statusValue] : []
                )
            ];
            $count = $app->count("stores", $countWhere);

            $datas = [];
            $app->select("stores", [
                '[>]stores_types' => ['type' => 'id']
            ], [
                'stores.id',
                'stores_types.name(type_name)',
                'stores.code',
                'stores.name',
                'stores.phone',
                'stores.email',
                'stores.address',
                'stores.date',
                'stores.status'
            ], $where, function ($data) use (&$datas, $jatbi, $app) {
                $datas[] = [
                    "checkbox" => (string) ($app->component("box", ["data" => $data['id'] ?? '']) ?? '<input type="checkbox">'),
                    "type" => (string) ($data['type_name'] ?? 'Không xác định'),
                    "code" => (string) ($data['code'] ?? ''),
                    "name" => (string) ($data['name'] ?? ''),
                    "phone" => (string) ($data['phone'] ?? ''),
                    "email" => (string) ($data['email'] ?? ''),
                    "address" => (string) ($data['address'] ?? ''),
                    "date" => $jatbi->datetime($data['date'] ?? ''),
                    "status" => (string) ($app->component("status", [
                        "url" => "/stores/stores-status/" . ($data['id'] ?? ''),
                        "data" => $data['status'] ?? '',
                        "permission" => ['stores.edit']
                    ]) ?? '<span>' . ($data['status'] ?? '') . '</span>'),
                    "action" => (string) ($app->component("action", [
                        "button" => [
                            [
                                'type' => 'button',
                                'name' => $jatbi->lang("Sửa"),
                                'permission' => ['stores.edit'],
                                'action' => ['data-url' => '/stores/stores-edit/' . ($data['id'] ?? ''), 'data-action' => 'modal']
                            ],
                            [
                                'type' => 'button',
                                'name' => $jatbi->lang("Xóa"),
                                'permission' => ['stores.deleted'],
                                'action' => ['data-url' => '/stores/stores-deleted?box=' . ($data['id'] ?? ''), 'data-action' => 'modal']
                            ],
                        ]
                    ]) ?? '<button>Sửa</button><button>Xóa</button>')
                ];
            });

            echo json_encode(
                [
                    "draw" => $draw,
                    "recordsTotal" => $count,
                    "recordsFiltered" => $count,
                    "data" => $datas ?? []
                ],
               
            );
        }
    })->setPermissions(['stores']);

//branch
$app->router('/branch', ['GET', 'POST'], function ($vars) use ($app, $jatbi, $setting) {
    $jatbi->permission('branch');
    $vars['title'] = $jatbi->lang("Quầy hàng");

    if ($app->method() === 'GET') {
        echo $app->render($setting['template'] . '/stores/branch.html', $vars);
    } elseif ($app->method() === 'POST') {
        $app->header(['Content-Type' => 'application/json; charset=utf-8']);

        $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $statusValue = isset($_POST['status']) ? $_POST['status'] : '';
        $orderName = isset($_POST['order'][0]['name']) ? $_POST['order'][0]['name'] : 'id';
        $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';

        $where = [
            "AND" => [
                "branch.deleted" => 0,
            ],
            "LIMIT" => [$start, $length],
            "ORDER" => [$orderName => strtoupper($orderDir)]
        ];

        if ($searchValue != '') {
            $where['AND']['OR'] = [
                'branch.name[~]' => $searchValue,
                'branch.code[~]' => $searchValue,
                'stores.name[~]' => $searchValue, 
            ];
        }

        if ($statusValue != '') {
            $where['AND']['branch.status'] = $statusValue;
        }

        $countWhere = [
            "AND" => array_merge(
                ["branch.deleted" => 0],
                $searchValue != '' ? ["OR" => [
                    'branch.name[~]' => $searchValue,
                    'branch.code[~]' => $searchValue,
                    'stores.name[~]' => $searchValue,
                ]] : [],
                $statusValue != '' ? ["branch.status" => $statusValue] : []
            )
        ];
        $count = $app->count("branch", $countWhere);

        $datas = [];
        $app->select("branch", [
            '[>]stores' => ['stores' => 'id'] // JOIN với bảng stores
        ], [
            'branch.id',
            'branch.code',
            'branch.name',
            'stores.name(store_name)', 
            'branch.status'
        ], $where, function ($data) use (&$datas, $jatbi, $app) {
            $datas[] = [
                "checkbox" => (string) ($app->component("box", ["data" => $data['id'] ?? '']) ?? '<input type="checkbox">'),
                "type" => (string) ($data['store_name'] ?? 'Không xác định'), // Sử dụng tên từ stores
                "code" => (string) ($data['code'] ?? ''),
                "name" => (string) ($data['name'] ?? ''),
                "status" => (string) ($app->component("status", [
                    "url" => "/stores/branch-status/" . ($data['id'] ?? ''),
                    "data" => $data['status'] ?? '',
                    "permission" => ['branch.edit']
                ]) ?? '<span>' . ($data['status'] ?? '') . '</span>'),
                "action" => (string) ($app->component("action", [
                    "button" => [
                        [
                            'type' => 'button',
                            'name' => $jatbi->lang("Sửa"),
                            'permission' => ['branch.edit'],
                            'action' => ['data-url' => '/stores/branch-edit/' . ($data['id'] ?? ''), 'data-action' => 'modal']
                        ],
                        [
                            'type' => 'button',
                            'name' => $jatbi->lang("Xóa"),
                            'permission' => ['branch.deleted'],
                            'action' => ['data-url' => '/stores/branch-deleted?box=' . ($data['id'] ?? ''), 'data-action' => 'modal']
                        ],
                    ]
                ]) ?? '<button>Sửa</button><button>Xóa</button>')
            ];
        });

        echo json_encode(
            [
                "draw" => $draw,
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "data" => $datas ?? []
            ],
        );
    }
})->setPermissions(['branch']);


//stores_types

    $app->router('/stores-types', ['GET', 'POST'], function ($vars) use ($app, $jatbi, $setting) {
        $jatbi->permission('stores-types');
        $vars['title'] = $jatbi->lang("Loại cửa hàng");

        if ($app->method() === 'GET') {
            echo $app->render($setting['template'] . '/stores/stores-types.html', $vars);
        } elseif ($app->method() === 'POST') {
            $app->header(['Content-Type' => 'application/json; charset=utf-8']);

            $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
            $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
            $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
            $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
            $statusValue = isset($_POST['status']) ? $_POST['status'] : '';
            $orderName = isset($_POST['order'][0]['name']) ? $_POST['order'][0]['name'] : 'id';
            $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';

            $where = [
                "AND" => [
                    "stores_types.deleted" => 0,
                ],
                "LIMIT" => [$start, $length],
                "ORDER" => [$orderName => strtoupper($orderDir)]
            ];

            if ($searchValue != '') {
                $where['AND']['OR'] = [
                    'stores_types.name[~]' => $searchValue,
                    'stores_types.notes[~]' => $searchValue,
                ];
            }

            if ($statusValue != '') {
                $where['AND']['stores_types.status'] = $statusValue;
            }

            $countWhere = [
                "AND" => array_merge(
                    ["stores_types.deleted" => 0],
                    $searchValue != '' ? ["OR" => [
                        'stores_types.name[~]' => $searchValue,
                        'stores_types.notes[~]' => $searchValue,
                    ]] : [],
                    $statusValue != '' ? ["stores_types.status" => $statusValue] : []
                )
            ];
            $count = $app->count("stores_types", $countWhere);

            $datas = [];
            $app->select("stores_types", [
                'stores_types.id',
                'stores_types.name',
                'stores_types.notes',
                'stores_types.status'
            ], $where, function ($data) use (&$datas, $jatbi, $app) {
                $datas[] = [
                    "checkbox" => (string) ($app->component("box", ["data" => $data['id'] ?? '']) ?? '<input type="checkbox">'),
                    "type" => (string) ($data['name'] ?? ''),
                    "notes" => (string) ($data['notes'] ?? ''),
                    "status" => (string) ($app->component("status", [
                        "url" => "/stores/stores-types-status/" . ($data['id'] ?? ''),
                        "data" => $data['status'] ?? '',
                        "permission" => ['stores_types.edit']
                    ]) ?? '<span>' . ($data['status'] ?? '') . '</span>'),
                    "action" => (string) ($app->component("action", [
                        "button" => [
                            [
                                'type' => 'button',
                                'name' => $jatbi->lang("Sửa"),
                                'permission' => ['stores_types.edit'],
                                'action' => ['data-url' => '/stores/stores-types-edit/' . ($data['id'] ?? ''), 'data-action' => 'modal']
                            ],
                            [
                                'type' => 'button',
                                'name' => $jatbi->lang("Xóa"),
                                'permission' => ['stores_types.deleted'],
                                'action' => ['data-url' => '/stores/stores-types-deleted?box=' . ($data['id'] ?? ''), 'data-action' => 'modal']
                            ],
                        ]
                    ]) ?? '<button>Sửa</button><button>Xóa</button>')
                ];
            });

            echo json_encode(
                [
                    "draw" => $draw,
                    "recordsTotal" => $count,
                    "recordsFiltered" => $count,
                    "data" => $datas ?? []
                ],

            );

        }
    })->setPermissions(['stores-types']);
})->middleware('login');





