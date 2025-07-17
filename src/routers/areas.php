<?php
    if (!defined('ECLO')) die("Hacking attempt");
    $provinces = $app->select("province", "*",["deleted"=> 0,"status"=>'A',]);
	$districts = $app->select("district", "*",["deleted"=> 0,"status"=>'A',]);
    $app->group($setting['manager']."/areas",function($app) use ($jatbi,$setting){
        $app->router("/province", ['GET', 'POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Tỉnh thành");
            if ($app->method() === 'GET') {
                echo $app->render($setting['template'].'/areas/province.html', $vars);
            }
            elseif ($app->method() === 'POST') {
                $app->header([
                    'Content-Type' => 'application/json',
                ]);

                // Lấy tham số từ DataTables
                $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
                $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
                $length = isset($_POST['length']) ? intval($_POST['length']) : $setting['site_page'] ?? 10;
                $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
                $orderName = isset($_POST['order'][0]['name']) ? $_POST['order'][0]['name'] : 'id';
                $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';
                $status = isset($_POST['status']) ? [$_POST['status'],$_POST['status']] : '';

                // Điều kiện lọc
                $where = [
                    "AND" => [
                        "OR" => [
                            "province.name[~]" => $searchValue,
                        ],
                        "province.deleted" => 0,
                        "province.status[<>]" => $status,
                    ],
                    "LIMIT" => [$start, $length],
                    "ORDER" => [$orderName => strtoupper($orderDir)],
                ];
                // if ($status != '') {
                //     $where['AND']['status'] = $status; // lọc đúng theo trạng thái chọn
                // }
                // Đếm tổng số bản ghi
                $count = $app->count("province", [
                    "AND" => $where['AND'],
                ]);

                // Lấy dữ liệu phiếu giảm giá
                $datas = [];
                $app->select("province", "*", $where, function ($data) use (&$datas, $jatbi, $app) {
                    $datas[] = [
                        "checkbox" => $app->component("box", ["data" => $data['id']]),
                        "name" => $data['name'],
                        "status" => $app->component("status", [
                            "url" => "/province/province-status/" . $data['id'],
                            "data" => $data['status'],
                            "permission" => ['province.edit']
                        ]),
                        "action" => $app->component("action", [
                            "button" => [
                                [
                                    'type' => 'link',
                                    'name' => $jatbi->lang("Xem"),
                                    'permission' => ['province'],
                                    'action' => ['href' => '/coupons/coupons-views/' . $data['id'], 'data-pjax' => '']
                                ],
                                [
                                    'type' => 'button',
                                    'name' => $jatbi->lang("Sửa"),
                                    'permission' => ['province.edit'],
                                    'action' => ['data-url' => '/coupons/coupons-edit/' . $data['id'], 'data-action' => 'modal']
                                ],
                                [
                                    'type' => 'button',
                                    'name' => $jatbi->lang("Xóa"),
                                    'permission' => ['province.deleted'],
                                    'action' => ['data-url' => '/coupons/coupons-deleted?box=' . $data['id'], 'data-action' => 'modal']
                                ],
                            ]
                        ]),
                    ];
                });

                // Trả về dữ liệu JSON
                echo json_encode([
                    "draw" => $draw,
                    "recordsTotal" => $count,
                    "recordsFiltered" => $count,
                    "data" => $datas ?? [],
                ]);
            }
        })->setPermissions(['province']);

        $app->router("/district", ['GET', 'POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Quận huyện");
            if ($app->method() === 'GET') {
                $vars['provinces'] = $app->select("province", "*",["deleted"=> 0,"status"=>'A',]);
                echo $app->render($setting['template'].'/areas/district.html', $vars);
            }
            elseif ($app->method() === 'POST') {
                $app->header([
                    'Content-Type' => 'application/json',
                ]);

                // Lấy tham số từ DataTables
                $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
                $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
                $length = isset($_POST['length']) ? intval($_POST['length']) : $setting['site_page'] ?? 10;
                $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
                $orderName = isset($_POST['order'][0]['name']) ? $_POST['order'][0]['name'] : 'id';
                $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';
                $status = isset($_POST['status']) ? [$_POST['status'],$_POST['status']] : '';
                $province = isset($_POST['province']) ? $_POST['province'] : '';

                // Điều kiện lọc
                $where = [
                    "AND" => [
                        "OR" => [
                            "district.name[~]" => $searchValue,
                            // "province.name[~]" => $searchValue,
                        ],
                        "district.status[<>]" => $status,
                        "district.deleted" => 0,
                        // "province.deleted" => 0,
                        // "province.status[<>]" => $status,
                    ],
                    "LIMIT" => [$start, $length],
                    "ORDER" => [$orderName => strtoupper($orderDir)],
                ];
                // if ($status != '') {
                //     $where['AND']['status'] = $status; // lọc đúng theo trạng thái chọn
                // }
                if (!empty($province)) {
                    $where['AND']['district.province'] = $province; // lọc theo tỉnh thành
                }
                // Đếm tổng số bản ghi
                $count = $app->count("district",[
                    "AND" => $where['AND'],
                ]);

                // Lấy dữ liệu phiếu giảm giá
                $datas = [];
                $app->select("district" , [
                    "[>]province" => ["province" => "id"],
                ],[
                    "district.id",
                    "district.name",
                    "district.status",
                    "province.name (province_name)" // alias để dễ truy xuất
                ], $where, function ($data) use (&$datas, $jatbi, $app) {
                    $datas[] = [
                        "checkbox" => $app->component("box", ["data" => $data['id']]),
                        "name" => $data['name'],
                        "province" => $data['province_name'] ?? '',
                        "status" => $app->component("status", [
                            "url" => "/district/district-status/" . $data['id'],
                            "data" => $data['status'],
                            "permission" => ['district.edit']
                        ]),
                        "action" => $app->component("action", [
                            "button" => [
                                [
                                    'type' => 'link',
                                    'name' => $jatbi->lang("Xem"),
                                    'permission' => ['coupons'],
                                    'action' => ['href' => '/coupons/coupons-views/' . $data['id'], 'data-pjax' => '']
                                ],
                                [
                                    'type' => 'button',
                                    'name' => $jatbi->lang("Sửa"),
                                    'permission' => ['district.edit'],
                                    'action' => ['data-url' => '/district/district-edit/' . $data['id'], 'data-action' => 'modal']
                                ],
                                [
                                    'type' => 'button',
                                    'name' => $jatbi->lang("Xóa"),
                                    'permission' => ['district.deleted'],
                                    'action' => ['data-url' => '/district/district-deleted?box=' . $data['id'], 'data-action' => 'modal']
                                ],
                            ]
                        ]),
                    ];
                });

                // Trả về dữ liệu JSON
                echo json_encode([
                    "draw" => $draw,
                    "recordsTotal" => $count,
                    "recordsFiltered" => $count,
                    "data" => $datas ?? [],
                ]);
            }
        })->setPermissions(['district']);
        $app->router("/ward", ['GET', 'POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Phường xã");
            if ($app->method() === 'GET') {
                $vars['provinces'] = $app->select("province", "*",["status"=>'A', "deleted"=>0]);
                if (isset($_POST['province'])) {
                    $vars['districts'] = $app->select("district", "*",["status"=>'A', "deleted"=>0, "province" => $_GET['province']]);
                } else {
                    $vars['districts'] = $app->select("district", "*",["deleted"=> 0,"status"=>'A',]);
                }

                echo $app->render($setting['template'].'/areas/ward.html', $vars);
            }
            elseif ($app->method() === 'POST') {
                $app->header([
                    'Content-Type' => 'application/json',
                ]);

                // Lấy tham số từ DataTables
                $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
                $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
                $length = isset($_POST['length']) ? intval($_POST['length']) : $setting['site_page'] ?? 10;
                $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
                $orderName = isset($_POST['order'][0]['name']) ? $_POST['order'][0]['name'] : 'id';
                $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';
                $status = isset($_POST['status']) ? [$_POST['status'],$_POST['status']] : '';
                $district = isset($_POST['district']) ? $_POST['district'] : '';
                $province = isset($_POST['province']) ? $_POST['province'] : '';

                // Điều kiện lọc
                $where = [
                    "AND" => [
                        "OR" => [
                            "ward.name[~]" => $searchValue,
                            // "province.name[~]" => $searchValue,
                        ],
                        "ward.status[<>]" => $status,
                        "ward.deleted" => 0,
                        // "province.deleted" => 0,
                        // "province.status[<>]" => $status,
                    ],
                    "LIMIT" => [$start, $length],
                    "ORDER" => [$orderName => strtoupper($orderDir)],
                ];
                if (!empty($district)) {
                    $where['AND']['ward.district'] = $district; // lọc theo quận huyện
                }
                if (!empty($province)) {
                    $where['AND']['ward.province'] = $province; // lọc theo tỉnh thành
                }
                // Đếm tổng số bản ghi
                $count = $app->count("ward",[
                    "AND" => $where['AND'],
                ]);

                // Lấy dữ liệu phiếu giảm giá
                $datas = [];
                $app->select("ward" , [
                    "[>]district" => ["district" => "id"],
                    "[>]province" => ["province" => "id"],
                ],[
                    "ward.id",
                    "ward.name",
                    "ward.status",
                    "province.name (province_name)",
                    "district.name (district_name)" // alias để dễ truy xuất
                ], $where, function ($data) use (&$datas, $jatbi, $app) {
                    $datas[] = [
                        "checkbox" => $app->component("box", ["data" => $data['id']]),
                        "name" => $data['name'],
                        "district" => $data['district_name'] ?? '',
                        "province" => $data['province_name'] ?? '',
                        "status" => $app->component("status", [
                            "url" => "/district/district-status/" . $data['id'],
                            "data" => $data['status'],
                            "permission" => ['district.edit']
                        ]),
                        "action" => $app->component("action", [
                            "button" => [
                                [
                                    'type' => 'link',
                                    'name' => $jatbi->lang("Xem"),
                                    'permission' => ['coupons'],
                                    'action' => ['href' => '/coupons/coupons-views/' . $data['id'], 'data-pjax' => '']
                                ],
                                [
                                    'type' => 'button',
                                    'name' => $jatbi->lang("Sửa"),
                                    'permission' => ['district.edit'],
                                    'action' => ['data-url' => '/district/district-edit/' . $data['id'], 'data-action' => 'modal']
                                ],
                                [
                                    'type' => 'button',
                                    'name' => $jatbi->lang("Xóa"),
                                    'permission' => ['district.deleted'],
                                    'action' => ['data-url' => '/district/district-deleted?box=' . $data['id'], 'data-action' => 'modal']
                                ],
                            ]
                        ]),
                    ];
                });

                // Trả về dữ liệu JSON
                echo json_encode([
                    "draw" => $draw,
                    "recordsTotal" => $count,
                    "recordsFiltered" => $count,
                    "data" => $datas ?? [],
                ]);
            }
        })->setPermissions(['ward']);
    })->middleware('login');


?>