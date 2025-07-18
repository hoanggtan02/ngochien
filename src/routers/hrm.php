<?php
    if (!defined(constant_name: 'ECLO')) die("Hacking attempt");
    $app->group($setting['manager']."/hrm" , function($app) use($jatbi,$setting){

        $app->router('/personnels', ['GET', 'POST'], function($vars) use ($app, $jatbi, $setting) {
            $jatbi->permission('personnels');
            if ($app->method() === 'GET') {
                $vars['title'] = $jatbi->lang("Quản Lý Nhân Sự");
                $vars['officesList'] = $app->select("offices", ['id','name'], ['status' => 'A', 'deleted' => 0]);
                echo $app->render($setting['template'].'/hrm/personnels.html', $vars);
            }

            if ($app->method() === 'POST') {
                $app->header(['Content-Type' => 'application/json']);

                $filter_office = isset($_POST['office']) ? $_POST['office'] : '';

                // Lấy tham số từ DataTables
                $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
                $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
                $length = isset($_POST['length']) ? intval($_POST['length']) : $setting['site_page'] ?? 10;
                $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
                $orderName = isset($_POST['order'][0]['name']) ? $_POST['order'][0]['name'] : 'id';
                $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';
                $status = isset($_POST['status']) ? [$_POST['status'],$_POST['status']] : '';

                $recordsTotal = $app->count("personnels", [
                    'deleted' => 0,
                ]);

                $where = [
                    "AND" => [
                        "OR" => [
                            'code[~]'    => $searchValue,
                            'name[~]'    => $searchValue,
                            'phone[~]'   => $searchValue,
                            'email[~]'   => $searchValue,
                            'address[~]' => $searchValue,
                            'idcode[~]'  => $searchValue,                        
                        ],
                        'deleted' => 0,
                    ],
                    "LIMIT" => [$start, $length],
                    "ORDER" => [$orderName => strtoupper($orderDir)],
                ];
                
                if (!empty($filter_office)) {
                    $where['AND']['office'] = $filter_office;
                }
                if (!empty($status)) {
                    $where['AND']['status'] = $status;
                }
                $recordsFiltered = $app->count("personnels", $where['AND']);
                $datas = [];
                $app->select("personnels", "*", $where, function ($data) use (&$datas, $jatbi, $app) {
                    $officeName = $app->get("offices", "name", ['id' => $data['office']]);
                    $storeName = $app->get("stores", "name", ['id' => $data['stores']]);
                    
                    $datas[] = [
                        "checkbox" => $app->component("box", ["data" => $data['id']]),
                        "code"     => $data['code'],
                        "office"   => $officeName,
                        'name'     => $data['name'],
                        'phone'    => $data['phone'],
                        'email'    => $data['email'],
                        'date'     => date("d/m/Y", strtotime($data['date'])), 
                        "status" => $app->component("status", [
                            "url" => "/province/province-status/" . $data['id'],
                            "data" => $data['status'],
                            "permission" => ['personnels.edit']
                        ]),                        
                        "stores"   => $storeName,
                        "action"   => $app->component("action", [
                            "button" => [
                                [
                                    'type'       => 'button',
                                    'name'       => $jatbi->lang("Sửa"),
                                    'permission' => ['personnels.edit'],
                                    'action'     => ['data-url' => '/admin/personnels-edit/' . $data['id'], 'data-action' => 'modal']
                                ],
                                [
                                    'type'       => 'button',
                                    'name'       => $jatbi->lang("Xóa"),
                                    'permission' => ['personnels.delete'],
                                    'action'     => ['data-url' => '/admin/personnels-delete?id=' . $data['id'], 'data-action' => 'modal-confirm']
                                ],
                            ]
                        ]),
                    ];
                });
                echo json_encode([
                    "draw"            => $draw,
                    "recordsTotal"    => $recordsTotal,
                    "recordsFiltered" => $recordsFiltered,
                    "data"            => $datas
                ]);
            }
        })->setPermissions(['personnels']);




    // Route offices
    $app->router("/offices", ['GET', 'POST'], function ($vars) use ($app, $jatbi, $setting) {
        $jatbi->permission('offices');
        $vars['title'] = $jatbi->lang("Phòng ban");

        if ($app->method() === 'GET') {
            echo $app->render($setting['template'] . '/hrm/offices.html', $vars);
        }
        if ($app->method() === 'POST') {
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
                    "OR" => [
                        'offices.code[~]' => $searchValue,
                        'offices.name[~]' => $searchValue,
                        'offices.notes[~]' => $searchValue,
                    ],
                    'deleted' => 0,
                ],
                "LIMIT" => [$start, $length],
                "ORDER" => [$orderName => strtoupper($orderDir)],
            ];

            if ($statusValue != '') {
                $where['AND']['offices.status'] = $statusValue;
            }

            $countWhere = [
                "AND" => array_merge(
                    ["offices.deleted" => 0],
                    $searchValue != '' ? [
                        "OR" => [
                            'offices.code[~]' => $searchValue,
                            'offices.name[~]' => $searchValue,
                            'offices.notes[~]' => $searchValue,
                        ]
                    ] : [],
                    $statusValue != '' ? ["offices.status" => $statusValue] : []
                )
            ];
            $count = $app->count("offices", $countWhere);

            $datas = [];
            $app->select("offices", "*", $where, function ($data) use (&$datas, $jatbi, $app) {
                $datas[] = [
                    "checkbox" => $app->component("box", ["data" => $data['id']]),
                    "code" => ($data['code'] ?? ''),
                    "name" => ($data['name'] ?? ''),
                    "notes" => ($data['notes'] ?? ''),
                    "status" => ($app->component("status", [
                        "url" => "/hrm/offices-status/" . ($data['id'] ?? ''),
                        "data" => $data['status'] ?? '',
                        "permission" => ['offices.edit']
                    ]) ?? '<span>' . ($data['status'] ?? '') . '</span>'),
                    "action" => ($app->component("action", [
                        "button" => [
                            [
                                'type' => 'button',
                                'name' => $jatbi->lang("Sửa"),
                                'permission' => ['offices.edit'],
                                'action' => ['data-url' => '/hrm/offices-edit/' . ($data['id'] ?? ''), 'data-action' => 'modal']
                            ],
                            [
                                'type' => 'button',
                                'name' => $jatbi->lang("Xóa"),
                                'permission' => ['offices.deleted'],
                                'action' => ['data-url' => '/hrm/offices-deleted?box=' . ($data['id'] ?? ''), 'data-action' => 'modal']
                            ],
                        ]
                    ]))
                ];
            });

            echo json_encode(
                [
                    "draw" => $draw,
                    "recordsTotal" => $count,
                    "recordsFiltered" => $count,
                    "data" => $datas ?? []
                ]
            );
        }
    })->setPermissions(['offices']);

})->middleware('login');

?>