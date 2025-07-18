<?php
    if (!defined('ECLO')) die("Hacking attempt");
    $app->group($setting['manager']."/admin",function($app) use ($jatbi,$setting){

        $app->router("/search", ['GET', 'POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Tìm kiếm");
            if ($app->method() === 'GET') {
                echo $app->render($setting['template'].'/admin/search.html', $vars, $jatbi->ajax());
            }
        });

        $app->router("/plugins", ['GET','POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Tiện ích mở rộng");
            if($app->method()==='GET'){
                echo $app->render($setting['template'].'/admin/plugins.html', $vars);
            }
            elseif($app->method()==='POST'){
                $app->header([
                    'Content-Type' => 'application/json',
                ]);
                $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
                $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
                $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
                $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
                $orderName = isset($_POST['order'][0]['name']) ? $_POST['order'][0]['name'] : 'id';
                $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';
                $status = isset($_POST['status']) ? [$_POST['status'],$_POST['status']] : '';
                $where = [
                    "AND" => [
                        "OR" => [
                            "name[~]" => $searchValue,
                        ],
                        "status[<>]" => $status,
                        "deleted" => 0,
                    ],
                    "LIMIT" => [$start, $length],
                    "ORDER" => [$orderName => strtoupper($orderDir)]
                ];
                $count = $app->count("plugins",["AND" => $where['AND']]);
                $app->select("plugins", "*", $where, function ($data) use (&$datas, $jatbi,$app) {
                    $getConfig = json_decode($data['config'], true); // Chuyển JSON thành mảng
                    $config = [];
                    foreach ($getConfig as $key => $value) {
                        $config[] = $key.': '.$value;
                    }
                    $output = implode("\n", $config);
                    if($data['install']==0){
                        $install = [
                            'type' => 'button',
                            'name' => $jatbi->lang("Cài đặt"),
                            'permission' => ['plugins.add'],
                            'icon' => '<i class="ti me-2 ti-settings-cog"></i>',
                            'action' => ['data-url' => '/admin/plugins-install/'.$data['active'], 'data-action' => 'modal']
                        ];
                    }
                    $datas[] = [
                        "checkbox" => $app->component("box",["data"=>$data['active']]),
                        "name" => $data['name'] .' <br><small>'.$data['uid'].'</small>',
                        "version" => $data['version'],
                        "author" => $data['author'],
                        "requires" => $data['requires'],
                        "description" => $data['description'],
                        "date" => $jatbi->datetime($data['date']),
                        "install" => $data['install'] == 0 ? '<span class="text-danger fw-bold">'.$jatbi->lang("Chưa cài đặt").'</span>':'<span class="text-success fw-bold">'.$jatbi->lang("Đã cài").'</span>',
                        "status" => $app->component("status",["url"=>"/admin/plugins-status/".$data['active'],"data"=>$data['status'],"permission"=>['plugins.edit']]),
                        "action" => $app->component("action",[
                            "button" => [
                                $install ?? [],
                                [
                                    'type' => 'button',
                                    'name' => $jatbi->lang("Cập nhật"),
                                    'permission' => ['plugins.edit'],
                                    'icon' => '<i class="ti me-2 ti-rotate-rectangle"></i>',
                                    'action' => ['data-url' => '/admin/plugins-update/'.$data['active'], 'data-action' => 'modal']
                                ],
                                [
                                    'type' => 'button',
                                    'name' => $jatbi->lang("Xóa"),
                                    'permission' => ['plugins.deleted'],
                                    'icon' => '<i class="ti me-2 ti-trash"></i>',
                                    'action' => ['data-url' => '/admin/plugins-deleted?box='.$data['active'], 'data-action' => 'modal']
                                ],
                            ]
                        ]),
                    ];
                });
                echo json_encode([
                    "draw" => $draw,
                    "recordsTotal" => $count,
                    "recordsFiltered" => $count,
                    "data" => $datas ?? []
                ]);
            }
        })->setPermissions(['plugins']);

        $app->router("/plugins-add", ['GET', 'POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Thêm tiện ích mở rộng");
            if ($app->method() === 'GET') {
                $vars['data'] = [
                    "status" => 'A',
                ];
                echo $app->render($setting['template'].'/admin/plugins-post.html', $vars, $jatbi->ajax());
            } elseif ($app->method() === 'POST') {
                $app->header([
                    'Content-Type' => 'application/json',
                ]);
                if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                    echo json_encode(["status" => "error", "content" => $jatbi->lang("Vui lòng chọn tệp plugin để tải lên.")]);
                    return;
                }
                $pluginFile = $_FILES['file'];
                $filename = $jatbi->active();
                $uploadDir = $setting['plugins'].'/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $handle = $app->upload($pluginFile);
                if ($handle->file_src_mime !== 'application/zip') {
                    echo json_encode(["status" => "error", "content" => $jatbi->lang("Chỉ hỗ trợ tệp zip.")]);
                    return;
                }
                $path_upload = $uploadDir . basename($pluginFile['name']);
                $handle->allowed        = array('application/zip');
                $handle->Process($uploadDir);
                if ($handle->uploaded) {
                    $path_zip = $handle->file_dst_pathname;
                    $zip = new ZipArchive;
                    if ($zip->open($path_zip) === TRUE) {
                        $extractPath = $uploadDir . $filename;
                        if (!file_exists($extractPath)) {
                            mkdir($extractPath, 0755, true);
                        }
                        $zip->extractTo($extractPath);
                        $zip->close();
                        unlink($path_zip);
                        $configFile = $extractPath . '/config.json';
                        if (file_exists($configFile)) {
                            $config = json_decode(file_get_contents($configFile), true);
                        }
                        $getPlugin = $app->get("plugins",["id"],["uid"=>$config['id']]);
                        if($getPlugin){
                            echo json_encode(["status" => "error", "content" => $jatbi->lang("Mã tiện ích đã có vui lòng thay đổi hoặc cập nhật")]);
                            $jatbi->deleteFolder($extractPath);
                            return;
                        }
                        $insert = [
                            "name"    => $config['name'],
                            "uid"    => $config['id'],
                            "version"    => $config['version'],
                            "requires"    => $config['requires'],
                            "description"    => $config['description'],
                            "author"    => $config['author'],
                            "install"    => $config['install'] == 'true' ? 1 : 0,
                            "status"  => 'A',
                            "config"  => json_encode($config),
                            "plugins" => $filename,
                            "date"    => date("Y-m-d H:i:s"),
                            "active"  => $jatbi->active(),
                        ];
                        $app->insert("plugins", $insert);
                        echo json_encode(['status' => 'success', 'content' => $jatbi->lang("Cập nhật thành công")]);
                        $jatbi->logs('plugins', 'plugins-add', $insert);
                    } else {
                        echo json_encode(["status" => "error", "content" => $jatbi->lang("Giải nén tệp thất bại.")]);
                    }
                } else {
                    echo json_encode(["status" => "error", "content" => $jatbi->lang("Tải lên tệp thất bại.")]);
                }
            }
        })->setPermissions(['plugins.add']);

        $app->router("/plugins-update/{id}", ['GET', 'POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Thêm tiện ích mở rộng");
            if ($app->method() === 'GET') {
                $vars['data'] = $app->get("plugins","*",["active"=>$vars['id']]);
                if($vars['data']>1){
                    echo $app->render($setting['template'].'/admin/plugins-post.html', $vars, $jatbi->ajax());
                }
                else {
                    echo $app->render($setting['template'].'/error.html', $vars, $jatbi->ajax());
                }
            } 
            elseif ($app->method() === 'POST') {
                $app->header([
                    'Content-Type' => 'application/json',
                ]);
                $data = $app->get("plugins","*",["active"=>$vars['id']]);
                if($data>1){
                    if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                        echo json_encode(["status" => "error", "content" => $jatbi->lang("Vui lòng chọn tệp plugin để tải lên.")]);
                        return;
                    }
                    $pluginFile = $_FILES['file'];
                    $filename = $jatbi->active();
                    $uploadDir = $setting['plugins'].'/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $handle = $app->upload($pluginFile);
                    if ($handle->file_src_mime !== 'application/zip') {
                        echo json_encode(["status" => "error", "content" => $jatbi->lang("Chỉ hỗ trợ tệp zip.")]);
                        return;
                    }
                    $path_upload = $uploadDir . basename($pluginFile['name']);
                    $handle->allowed        = array('application/zip');
                    $handle->Process($uploadDir);
                    if ($handle->uploaded) {
                        $path_zip = $handle->file_dst_pathname;
                        $zip = new ZipArchive;
                        if ($zip->open($path_zip) === TRUE) {
                            $extractPath = $uploadDir . $filename;
                            if (!file_exists($extractPath)) {
                                mkdir($extractPath, 0755, true);
                            }
                            $zip->extractTo($extractPath);
                            $zip->close();
                            unlink($path_zip);
                            $configFile = $extractPath . '/config.json';
                            if (file_exists($configFile)) {
                                $config = json_decode(file_get_contents($configFile), true);
                            }
                            $insert = [
                               "name"    => $config['name'],
                                "uid"    => $config['id'],
                                "version"    => $config['version'],
                                "requires"    => $config['requires'],
                                "description"    => $config['description'],
                                "author"    => $config['author'],
                                "status"  => 'A',
                                "config"  => json_encode($config),
                                "plugins" => $filename,
                                "date"    => date("Y-m-d H:i:s"),
                                "active"  => $jatbi->active(),
                            ];
                            $jatbi->deleteFolder($data['plugins']);
                            $app->update("plugins", $insert,["id"=>$data['id']]);
                            echo json_encode(['status' => 'success', 'content' => $jatbi->lang("Cập nhật thành công")]);
                            $jatbi->logs('plugins', 'plugins-add', $insert);
                        } else {
                            echo json_encode(["status" => "error", "content" => $jatbi->lang("Giải nén tệp thất bại.")]);
                        }
                    } else {
                        echo json_encode(["status" => "error", "content" => $jatbi->lang("Tải lên tệp thất bại.")]);
                    }
                }
                else {
                    echo json_encode(["status"=>"error","content"=>$jatbi->lang("Không tìm thấy dữ liệu")]);
                }
            }
        })->setPermissions(['plugins.add']);

        $app->router("/plugins-install/{id}", ['GET', 'POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Cài tiện ích mở rộng");
            if ($app->method() === 'GET') {
                $vars['data'] = $app->get("plugins","*",["active"=>$vars['id'],'install' => 0]);
                if($vars['data']>1){
                    echo $app->render($setting['template'].'/admin/plugins-install.html', $vars, $jatbi->ajax());
                }
                else {
                    echo $app->render($setting['template'].'/error.html', $vars, $jatbi->ajax());
                }
            } 
            elseif ($app->method() === 'POST') {
                $app->header([
                    'Content-Type' => 'application/json',
                ]);
                $data = $app->get("plugins","*",["active"=>$vars['id'], 'install' => 0]);
                if($data>1){
                    $app->update("plugins", ["install" => 1],["id"=>$data['id']]);
                    echo json_encode(['status' => 'success', 'content' => $jatbi->lang("Cài đặt thành công")]);
                }
                else {
                    echo json_encode(["status"=>"error","content"=>$jatbi->lang("Không tìm thấy dữ liệu")]);
                }
            }
        })->setPermissions(['plugins.add']);

        $app->router("/plugins-status/{id}", 'POST', function($vars) use ($app, $jatbi,$setting) {
            $app->header([
                'Content-Type' => 'application/json',
            ]);
            $data = $app->get("plugins","*",["active"=>$vars['id'],"deleted"=>0]);
            if($data>1){
                if($data>1){
                    if($data['status']==='A'){
                        $status = "D";
                    } 
                    elseif($data['status']==='D'){
                        $status = "A";
                    }
                    $app->update("plugins",["status"=>$status],["id"=>$data['id']]);
                    $jatbi->logs('plugins','plugins-status',$data);
                    echo json_encode(['status'=>'success','content'=>$jatbi->lang("Cập nhật thành công")]);
                }
                else {
                    echo json_encode(['status'=>'error','content'=>$jatbi->lang("Cập nhật thất bại"),]);
                }
            }
            else {
                echo json_encode(["status"=>"error","content"=>$jatbi->lang("Không tìm thấy dữ liệu")]);
            }
        })->setPermissions(['plugins.edit']);

        $app->router("/plugins-deleted", ['GET','POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Xóa Tài khoản");
            if($app->method()==='GET'){
                echo $app->render($setting['template'].'/common/deleted.html', $vars, $jatbi->ajax());
            }
            elseif($app->method()==='POST'){
                $app->header([
                    'Content-Type' => 'application/json',
                ]);
                $boxid = explode(',', $app->xss($_GET['box']));
                $datas = $app->select("plugins","*",["active"=>$boxid,"deleted"=>0]);
                if(count($datas)>0){
                    foreach($datas as $data){
                        $app->delete("plugins",["id"=>$data['id']]);
                        $jatbi->deleteFolder($data['plugins']);
                    }
                    $jatbi->logs('plugins','plugins-deleted',$datas);
                    echo json_encode(['status'=>'success',"content"=>$jatbi->lang("Cập nhật thành công")]);
                }
                else {
                    echo json_encode(['status'=>'error','content'=>$jatbi->lang("Có lỗi xẩy ra")]);
                }
            }
        })->setPermissions(['blockip.deleted']);

        //transpost start
        $app->router('/transport', ['GET', 'POST'], function($vars) use ($app, $jatbi, $setting) {
            $jatbi->permission('transport');
            $vars['title'] = $jatbi->lang("Quản lý Vận chuyển");

            if ($app->method() === 'GET') {
                echo $app->render($setting['template'] . '/admin/transport.html', $vars);
            } 
            elseif ($app->method() === 'POST') {
                $app->header(['Content-Type' => 'application/json']);
                $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
                $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
                $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
                $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
                $statusValue = isset($_POST['status']) ? $_POST['status'] : '';

                $orderName = isset($_POST['order'][0]['name']) ? $_POST['order'][0]['name'] : 'id';
                $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';

                $where = [
                    "AND" => [
                        "deleted" => 0,
                    ],
                    "LIMIT" => [$start, $length],
                    "ORDER" => [$orderName => strtoupper($orderDir)]
                ];

                if ($searchValue != '') {
                    $where['AND']['name[~]'] = $searchValue;
                }

                if ($statusValue != '') {
                    $where['AND']['status'] = $statusValue;
                } else {
                    $where['AND']['status'] = ['A', 'D'];
                }

                $count = $app->count("transport", ["AND" => $where['AND']]);
                
                $datas = [];
                $app->select("transport", "*", $where, function ($data) use (&$datas, $jatbi, $app,$setting) {
                    $datas[] = [
                        "checkbox" => $app->component("box", ["data" => $data['id']]),
                        "name" => $data['name'],
                        // "type" => $api_transports[$data['type']]['name'],
                        "type" => $setting["api"][$data['type']]['name'],
                        "notes" => $data['notes'],
                        "status" => $app->component("status", [
                            "url" => "/admin/transport-status/" . $data['id'],
                            "data" => $data['status'],
                            "permission" => ['transport.edit']
                        ]),
                        "action" => $app->component("action", [
                            "button" => [
                                [
                                    'type' => 'button',
                                    'name' => $jatbi->lang("Sửa"),
                                    'permission' => ['transport.edit'],
                                    'action' => ['data-url' => '/admin/transport-edit/' . $data['id'], 'data-action' => 'modal']
                                ],
                                [
                                    'type' => 'button',
                                    'name' => $jatbi->lang("Xóa"),
                                    'permission' => ['transport.deleted'],
                                    'action' => ['data-url' => '/admin/transport-deleted?box=' . $data['id'], 'data-action' => 'modal']
                                ],
                            ]
                        ]),
                    ];
                });
                echo json_encode([
                    "draw" => $draw,
                    "recordsTotal" => $count,
                    "recordsFiltered" => $count,
                    "data" => $datas ?? []
                ]);
            }
        })->setPermissions(['transport']);
        //tranpost end

        //flood start
    $app->router('/flood', ['GET', 'POST'], function($vars) use ($app, $jatbi, $setting) {
        $jatbi->permission('flood');
        $vars['title'] = $jatbi->lang("Danh sách chặn");
        if ($app->method() === 'GET') {
            echo $app->render($setting['template'] . '/admin/flood.html', $vars);
        } 
        elseif ($app->method() === 'POST') {
            $app->header(['Content-Type' => 'application/json']);

            $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
            $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
            $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
            $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
            
            $dateRange = isset($_POST['date']) ? $_POST['date'] : '';
            $orderName = isset($_POST['order'][0]['name']) ? $_POST['order'][0]['name'] : 'id';
            $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';

            $where = [
                "AND" => [
                    "OR" => [
                        'ip[~]' => $searchValue,
                        'browsers[~]' => $searchValue,
                        ],
                    ],
                    "LIMIT" => [$start, $length],
                    "ORDER" => [$orderName => strtoupper($orderDir)]
                ];


            if ($dateRange != '') {
                $date = explode(' - ', $dateRange);
                $date_from = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $date[0])));
                $date_to = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $date[1])));
                $where['AND']["date[<>]"] = [$date_from, $date_to];
            }
            

            $count = $app->count("flood", ["AND" => $where['AND']]);
            $datas = [];
            $app->select("flood", "*", $where, function ($data) use (&$datas, $jatbi, $app) {
                $datas[] = [
                    "checkbox" => $app->component("box", ["data" => $data['id']]),
                    "date" => date("d/m/Y H:i:s", strtotime($data['date'])),
                    "ip" => $data['ip'],
                    "browsers" => $data['browsers'],
                    "action" => $app->component("action", [
                        "button" => [
                            [
                                'type' => 'button',
                                'name' => $jatbi->lang("Xem"),
                                'permission' => ['flood'], 
                                'action' => ['data-url' => '/admin/flood-views/' . $data['id'], 'data-action' => 'modal']
                            ],
                            [
                                'type' => 'button',
                                'name' => $jatbi->lang("Xóa"),
                                'permission' => ['flood.delete'], 
                                'action' => ['data-url' => '/admin/flood-delete?box=' . $data['id'], 'data-action' => 'modal']
                            ],
                        ]
                    ]),
                ];
            });

            echo json_encode([
                "draw" => $draw,
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "data" => $datas ?? []
            ]);
        }
    })->setPermissions(['flood']); 
        //flood end


        $app->router("/blockip", ['GET','POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Chặn truy cập");
            if($app->method()==='GET'){
                echo $app->render($setting['template'].'/admin/blockip.html', $vars);
            }
            elseif($app->method()==='POST'){
                $app->header([
                    'Content-Type' => 'application/json',
                ]);
                $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
                $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
                $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
                $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
                $orderName = isset($_POST['order'][0]['name']) ? $_POST['order'][0]['name'] : 'id';
                $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';
                $status = isset($_POST['status']) ? [$_POST['status'],$_POST['status']] : '';
                $where = [
                    "AND" => [
                        "OR" => [
                            "ip[~]" => $searchValue,
                            "notes[~]" => $searchValue,
                        ],
                        "status[<>]" => $status,
                        "deleted" => 0,
                    ],
                    "LIMIT" => [$start, $length],
                    "ORDER" => [$orderName => strtoupper($orderDir)]
                ];
                $count = $app->count("blockip",["AND" => $where['AND']]);
                $app->select("blockip", "*", $where, function ($data) use (&$datas, $jatbi,$app) {
                    $datas[] = [
                        "checkbox" => $app->component("box",["data"=>$data['active']]),
                        "ip" => $data['ip'],
                        "date" => $jatbi->datetime($data['date']),
                        "status" => $app->component("status",["url"=>"/admin/blockip-status/".$data['active'],"data"=>$data['status'],"permission"=>['blockip.edit']]),
                        "action" => $app->component("action",[
                                    "button" => [
                                        [
                                            'type' => 'button',
                                            'name' => $jatbi->lang("Sửa"),
                                            'permission' => ['blockip.edit'],
                                            'action' => ['data-url' => '/admin/blockip-edit/'.$data['active'], 'data-action' => 'modal']
                                        ],
                                        [
                                            'type' => 'button',
                                            'name' => $jatbi->lang("Xóa"),
                                            'permission' => ['blockip.deleted'],
                                            'action' => ['data-url' => '/admin/blockip-deleted?box='.$data['active'], 'data-action' => 'modal']
                                        ],
                                    ]
                                ]),
                    ];
                });
                echo json_encode([
                    "draw" => $draw,
                    "recordsTotal" => $count,
                    "recordsFiltered" => $count,
                    "data" => $datas ?? []
                ]);
            }
        })->setPermissions(['blockip']);

        $app->router("/blockip-add", ['GET','POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Thêm Chặn truy cập");
            if($app->method()==='GET'){
                $vars['data'] = [
                    "status" => 'A',
                ];
                echo $app->render($setting['template'].'/admin/blockip-post.html', $vars, $jatbi->ajax());
            }
            elseif($app->method()==='POST'){
                $app->header([
                    'Content-Type' => 'application/json',
                ]);
                $jatbi->verifyCsrfToken();
                if($app->xss($_POST['ip'])=='' || $app->xss($_POST['status'])==''){
                    $error = ["status"=>"error","content"=>$jatbi->lang("Vui lòng không để trống")];
                }
                if(empty($error)){
                    $insert = [
                        "ip"          => $app->xss($_POST['ip']),
                        "status"        => $app->xss($_POST['status']),
                        "notes"         => $app->xss($_POST['notes']),
                        "date"          => date("Y-m-d H:i:s"),
                        "active"        => $jatbi->active(),
                    ];
                    $app->insert("blockip",$insert);
                    echo json_encode(['status'=>'success','content'=>$jatbi->lang("Cập nhật thành công")]);
                    $jatbi->logs('blockip','blockip-add',$insert);
                }
                else {
                    echo json_encode($error);
                }
            }
        })->setPermissions(['blockip.add']);

        $app->router("/blockip-edit/{id}", ['GET','POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Sửa Chặn truy cập");
            if($app->method()==='GET'){
                $vars['data'] = $app->get("blockip","*",["active"=>$vars['id'],"deleted"=>0]);
                if($vars['data']>1){
                    echo $app->render($setting['template'].'/admin/blockip-post.html', $vars, $jatbi->ajax());
                }
                else {
                    echo $app->render($setting['template'].'/error.html', $vars, $jatbi->ajax());
                }
            }
            elseif($app->method()==='POST'){
                $app->header([
                    'Content-Type' => 'application/json',
                ]);
                $jatbi->verifyCsrfToken();
                $data = $app->get("blockip","*",["active"=>$vars['id'],"deleted"=>0]);
                if($data>1){
                    if($app->xss($_POST['ip'])=='' || $app->xss($_POST['status'])==''){
                        $error = ["status"=>"error","content"=>$jatbi->lang("Vui lòng không để trống")];
                    }
                    if(empty($error)){
                        $insert = [
                            "ip"          => $app->xss($_POST['ip']),
                            "status"        => $app->xss($_POST['status']),
                            "notes"         => $app->xss($_POST['notes']),
                            "date"          => date("Y-m-d H:i:s"),
                        ];
                        $app->update("blockip",$insert,["id"=>$data['id']]);
                        echo json_encode(['status'=>'success','content'=>$jatbi->lang("Cập nhật thành công")]);
                        $jatbi->logs('blockip','blockip-edit',$insert);
                    }
                    else {
                        echo json_encode($error);
                    }
                }
                else {
                    echo json_encode(["status"=>"error","content"=>$jatbi->lang("Không tìm thấy dữ liệu")]);
                }
            }
        })->setPermissions(['blockip.edit']);

        $app->router("/blockip-status/{id}", 'POST', function($vars) use ($app, $jatbi,$setting) {
            $app->header([
                'Content-Type' => 'application/json',
            ]);
            $data = $app->get("blockip","*",["active"=>$vars['id'],"deleted"=>0]);
            if($data>1){
                if($data>1){
                    if($data['status']==='A'){
                        $status = "D";
                    } 
                    elseif($data['status']==='D'){
                        $status = "A";
                    }
                    $app->update("blockip",["status"=>$status],["id"=>$data['id']]);
                    $jatbi->logs('blockip','blockip-status',$data);
                    echo json_encode(['status'=>'success','content'=>$jatbi->lang("Cập nhật thành công")]);
                }
                else {
                    echo json_encode(['status'=>'error','content'=>$jatbi->lang("Cập nhật thất bại"),]);
                }
            }
            else {
                echo json_encode(["status"=>"error","content"=>$jatbi->lang("Không tìm thấy dữ liệu")]);
            }
        })->setPermissions(['blockip.edit']);

        $app->router("/blockip-deleted", ['GET','POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Xóa Tài khoản");
            if($app->method()==='GET'){
                echo $app->render($setting['template'].'/common/deleted.html', $vars, $jatbi->ajax());
            }
            elseif($app->method()==='POST'){
                $app->header([
                    'Content-Type' => 'application/json',
                ]);
                $boxid = explode(',', $app->xss($_GET['box']));
                $datas = $app->select("blockip","*",["active"=>$boxid,"deleted"=>0]);
                if(count($datas)>0){
                    foreach($datas as $data){
                        $app->update("blockip",["deleted"=> 1],["id"=>$data['id']]);
                        $name[] = $data['ip'];
                    }
                    $jatbi->logs('blockip','blockip-deleted',$datas);
                    $jatbi->trash('/admin/blockip-restore',"Chặn truy cập với ip: ".implode(', ',$name),["database"=>'blockip',"data"=>$boxid]);
                    echo json_encode(['status'=>'success',"content"=>$jatbi->lang("Cập nhật thành công")]);
                }
                else {
                    echo json_encode(['status'=>'error','content'=>$jatbi->lang("Có lỗi xẩy ra")]);
                }
            }
        })->setPermissions(['blockip.deleted']);

        $app->router("/blockip-restore/{id}", ['GET','POST'], function($vars) use ($app, $jatbi,$setting) {
            if($app->method()==='GET'){
                $vars['data'] = $app->get("trashs","*",["active"=>$vars['id'],"deleted"=>0]);
                if($vars['data']>1){
                    echo $app->render($setting['template'].'/common/restore.html', $vars, $jatbi->ajax());
                }
                else {
                    echo $app->render($setting['template'].'/error.html', $vars, $jatbi->ajax());
                }
            }
            elseif($app->method()==='POST'){
                $app->header([
                    'Content-Type' => 'application/json',
                ]);
                $trash = $app->get("trashs","*",["active"=>$vars['id'],"deleted"=>0]);
                if($trash>1){
                    $datas = json_decode($trash['data']);
                    foreach($datas->data as $active) {
                        $app->update("blockip",["deleted"=>0],["active"=>$active]);
                    }
                    $app->delete("trashs",["id"=>$trash['id']]);
                    $jatbi->logs('blockip','blockip-restore',$datas);
                    echo json_encode(['status'=>'success',"content"=>$jatbi->lang("Cập nhật thành công")]);
                }
                else {
                    echo json_encode(['status'=>'error','content'=>$jatbi->lang("Có lỗi xẩy ra")]);
                }
            }
        })->setPermissions(['blockip.deleted']);

        $app->router("/logs", ['GET','POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Nhật ký");
            if($app->method()==='GET'){
                echo $app->render($setting['template'].'/admin/logs.html', $vars);
            }
            elseif($app->method()==='POST'){
                $app->header([
                    'Content-Type' => 'application/json',
                ]);
                $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
                $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
                $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
                $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
                $orderName = isset($_POST['order'][0]['name']) ? $_POST['order'][0]['name'] : 'id';
                $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'desc';
                $dateRange = isset($_GET['date']) ? $_GET['date'] : null;
                $date_from = null;
                $date_to = null;
                if ($dateRange) {
                    if (is_array($dateRange) && count($dateRange) == 2) {
                        $date_from = date('Y-m-d 00:00:00', strtotime($dateRange[0]));
                        $date_to = date('Y-m-d 23:59:59', strtotime($dateRange[1]));
                    } elseif (is_string($dateRange)) {
                        $date_from = date('Y-m-d 00:00:00', strtotime($dateRange));
                        $date_to = date('Y-m-d 23:59:59', strtotime($dateRange));
                    }
                }
                $where = [
                    "AND" => [
                        "OR" => [
                            "logs.dispatch[~]" => $searchValue,
                            "logs.action[~]" => $searchValue,
                            "logs.content[~]" => $searchValue,
                            "logs.url[~]" => $searchValue,
                            "logs.ip[~]" => $searchValue,
                            "accounts.name[~]" => $searchValue,
                        ],
                    ],
                    "LIMIT" => [$start, $length],
                    "ORDER" => [$orderName => strtoupper($orderDir)]
                ];
                if ($date_from && $date_to) {
                    $where['AND']["logs.date[<>]"] = [$date_from, $date_to];
                }
                $count = $app->count("logs", [
                    "[>]accounts" => ["user" => "id"]
                ], [
                    "logs.id"
                ], $where['AND']);
                $app->select("logs", [
                        "[>]accounts" => ["user" => "id"]
                    ], 
                    [
                    'logs.id',
                    'logs.dispatch',
                    'logs.action',
                    'logs.url',
                    'logs.ip',
                    'logs.date',
                    'logs.user',
                    // 'logs.active',
                    'accounts.name',
                    'accounts.avatar',
                    ], $where, function ($data) use (&$datas,$jatbi) {
                        $datas[] = [
                            "user" => '<img data-src="/' . $data['id'] . '?type=thumb" class="width rounded-circle lazyload me-2" style="--width:40px"> '.$data['name'],
                            "dispatch" => $data['dispatch'],
                            "action" => $data['action'],
                            "url" => $data['url'],
                            "ip" => $data['ip'],
                            "date" => $jatbi->datetime($data['date']),
                            "views" => '<button data-action="modal" data-url="/admin/logs-views/'.$data['id'].'" class="btn btn-eclo-light btn-sm border-0 py-1 px-2 rounded-3" aria-label="'.$jatbi->lang('Xem').'"><i class="ti ti-eye"></i></button>',
                        ];
                });
                echo json_encode([
                    "draw" => $draw,
                    "recordsTotal" => $count,
                    "recordsFiltered" => $count,
                    "data" => $datas ?? []
                ]);
            }
        })->setPermissions(['logs']);

        $app->router("/logs-views/{id}", 'GET', function($vars) use ($app, $jatbi,$setting) {
            $vars['data'] = $app->get("logs","*",["id"=>$vars['id'],"deleted"=>0]);
            if($vars['data']>1){
                echo $app->render($setting['template'].'/admin/logs-views.html', $vars, $jatbi->ajax());
            }
            else {
                echo $app->render($setting['template'].'/error.html', $vars, $jatbi->ajax());
            }
        })->setPermissions(['blockip.edit']);

        $app->router("/trash", ['GET','POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Thùng rác");
            if($app->method()==='GET'){
                echo $app->render($setting['template'].'/admin/trash.html', $vars);
            }
            elseif($app->method()==='POST'){
                $app->header([
                    'Content-Type' => 'application/json',
                ]);
                $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
                $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
                $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
                $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
                $orderName = isset($_POST['order'][0]['name']) ? $_POST['order'][0]['name'] : 'id';
                $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'desc';
                $dateRange = isset($_GET['date']) ? $_GET['date'] : null;
                $date_from = null;
                $date_to = null;
                if ($dateRange) {
                    if (is_array($dateRange) && count($dateRange) == 2) {
                        $date_from = date('Y-m-d 00:00:00', strtotime($dateRange[0]));
                        $date_to = date('Y-m-d 23:59:59', strtotime($dateRange[1]));
                    } elseif (is_string($dateRange)) {
                        $date_from = date('Y-m-d 00:00:00', strtotime($dateRange));
                        $date_to = date('Y-m-d 23:59:59', strtotime($dateRange));
                    }
                }
                $where = [
                    "AND" => [
                        "OR" => [
                            "trashs.content[~]" => $searchValue,
                            "accounts.name[~]" => $searchValue,
                        ],
                    ],
                    "LIMIT" => [$start, $length],
                    "ORDER" => [$orderName => strtoupper($orderDir)]
                ];
                if ($date_from && $date_to) {
                    $where['AND']["trashs.date[<>]"] = [$date_from, $date_to];
                }
                $count = $app->count("trashs", [
                    "[>]accounts" => ["account" => "id"]
                ], [
                    "trashs.id"
                ], $where['AND']);
                $app->select("trashs", [
                        "[>]accounts" => ["account" => "id"]
                    ], 
                    [
                    'trashs.id',
                    'trashs.content',
                    'trashs.url',
                    'trashs.ip',
                    'trashs.date',
                    'trashs.active',
                    'trashs.router',
                    'accounts.name',
                    'accounts.avatar',
                    ], $where, function ($data) use (&$datas,$jatbi,$app) {
                        $datas[] = [
                            "checkbox" => $app->component("box",["data"=>$data['active']]),
                            "user" => '<img data-src="/' . $data['avatar'] . '?type=thumb" class="width rounded-circle me-2 lazyload" style="--width:40px"> '.$data['name'],
                            "content" => $data['content'],
                            "ip" => $data['ip'],
                            "date" => $jatbi->datetime($data['date']),
                            "action" => $app->component("action",[
                                "button" => [
                                    [
                                        'type' => 'button',
                                        'name' => $jatbi->lang("Phục hồi"),
                                        'permission' => ['trash'],
                                        'action' => ['data-url' => $data['router'].'/'.$data['active'], 'data-action' => 'modal']
                                    ],
                                    [
                                        'type' => 'button',
                                        'name' => $jatbi->lang("Xóa vĩnh viễn"),
                                        'permission' => ['trash'],
                                        'action' => ['data-url' => '/admin/trash-deleted?box='.$data['active'], 'data-action' => 'modal']
                                    ],
                                ]
                            ]),
                        ];
                });
                echo json_encode([
                    "draw" => $draw,
                    "recordsTotal" => $count,
                    "recordsFiltered" => $count,
                    "data" => $datas ?? []
                ]);
            }
        })->setPermissions(['trash']);

        $app->router("/trash-deleted", ['GET','POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Xóa thùng rác");
            if($app->method()==='GET'){
                echo $app->render($setting['template'].'/common/deleted.html', $vars, $jatbi->ajax());
            }
            elseif($app->method()==='POST'){
                $app->header([
                    'Content-Type' => 'application/json',
                ]);
                $boxid = explode(',', $app->xss($_GET['box']));
                $datas = $app->select("trashs","*",["active"=>$boxid,"deleted"=>0]);
                if(count($datas)>0){
                    foreach($datas as $data){
                        $app->delete("trashs",["id"=>$data['id']]);
                    }
                    $jatbi->logs('trash','deleted',$datas);
                    echo json_encode(['status'=>'success',"content"=>$jatbi->lang("Cập nhật thành công")]);
                }
                else {
                    echo json_encode(['status'=>'error','content'=>$jatbi->lang("Có lỗi xẩy ra")]);
                }
            }
        })->setPermissions(['trash']);

        $app->router("/config", ['GET','POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Cấu hình");
            if($app->method()==='GET'){
                $vars['data'] = $app->get("config","*") ?? ["logo"=>''];
                echo $app->render($setting['template'].'/admin/config.html', $vars, $jatbi->ajax());
            }
            elseif($app->method()==='POST'){
                $app->header([
                    'Content-Type' => 'application/json',
                ]);
                if($app->xss($_POST['name'])==''){
                    $error = ["status"=>"error","content"=>$jatbi->lang("Vui lòng không để trống")];
                }
                if(empty($error)){
                    $getConfig = $app->get("config","*");
                    $insert = [
                        "name"          => $app->xss($_POST['name']),
                        "email"         => $app->xss($_POST['email']),
                        "phone"         => $app->xss($_POST['phone']),
                        "address"       => $app->xss($_POST['address']),
                        "date"          => $app->xss($_POST['date']),
                        "time"          => $app->xss($_POST['time']),
                        "page"          => $app->xss($_POST['page']),
                        "url"           => $app->xss($_POST['url']),
                        "logo"          => $app->xss($_POST['logo']),
                    ];
                    if($getConfig){
                        $app->update("config",$insert);
                    }
                    else {
                        $app->insert("config",$insert);
                    }
                    
                    echo json_encode(['status'=>'success','content'=>$jatbi->lang("Cập nhật thành công")]);
                    $jatbi->logs('admin','config',$insert);
                }
                else {
                    echo json_encode($error);
                }
            }
        })->setPermissions(['config']);
        
        $app->router("/build-assets", 'GET', function($vars) use ($app, $jatbi,$setting) {
            $commonJs = $app->getValueData('commonJs');
            $commonCss = $app->getValueData('commonCss');
            $app->minifyCSS($commonCss,'css/style.bundle.css');
            $app->minifyJS($commonJs,'js/main.bundle.js');
            $jsVersion = '';
            $cssVersion = '';
            $jatbi->updateVersionInFile($setting['template'].'/components/footer.html', 'js', $jsVersion);
            $jatbi->updateVersionInFile($setting['template'].'/components/header.html', 'css', $cssVersion);
            $log = [];
            $logFile = 'version.json';
            if (file_exists($logFile)) {
                $json = file_get_contents($logFile);
                $log = json_decode($json, true);
                if (!is_array($log)) $log = [];
            }
            $log[] = [
                'time' => date('Y-m-d H:i:s'),
                'js' => $jsVersion,
                'css' => $cssVersion
            ];
            file_put_contents($logFile, json_encode($log, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            // $app->redirect($_SERVER['HTTP_REFERER']);

        });
        $app->router("/coupons", ['GET', 'POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['title'] = $jatbi->lang("Thẻ quà tặng");
            if ($app->method() === 'GET') {
                echo $app->render($setting['template'].'/admin/coupons.html', $vars);
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
                            "coupons.name[~]" => $searchValue,
                            "coupons.code[~]" => $searchValue,
                        ],
                        "coupons.deleted" => 0,
                        "coupons.status[<>]" => $status,
                    ],
                    "LIMIT" => [$start, $length],
                    "ORDER" => [$orderName => strtoupper($orderDir)],
                ];
                // if ($status != '') {
                //     $where['AND']['status'] = $status; // lọc đúng theo trạng thái chọn
                // }

                // Đếm tổng số bản ghi
                $count = $app->count("coupons", [
                    "AND" => $where['AND'],
                ]);

                // Lấy dữ liệu phiếu giảm giá
                $datas = [];
                $app->select("coupons", "*", $where, function ($data) use (&$datas, $jatbi, $app) {
                    //Xử lý dữ liệu thời hạn
                    $start = new DateTime($data['date_start']);
                    $end = new DateTime($data['date_end']);
                    $diff = $end->diff($start);

                    $dueDate = "{$diff->y} Năm {$diff->m} Tháng {$diff->d} Ngày {$diff->h} Giờ {$diff->i} Phút";
                    // Xử lý dữ liệu áp dụng
                    $apply = '';

                    if ($data['multi'] == 1) {
                        $apply .= $jatbi->lang('Áp dụng chung cho nhiều thẻ khác') . '<br>';
                    }

                    $categorys = @unserialize($data['categorys']);
                    if (is_array($categorys) && count($categorys) > 0) {
                        $apply .= '<div class="d-flex mb-2">';
                        $apply .= '<strong class="me-2">' . $jatbi->lang('Danh mục') . '</strong>';
                        foreach ($categorys as $category) {
                            $name = $app->get("categorys", "name", ["id" => $category]);
                            $apply .= '<span class="badge bg-primary me-2 p-1">' . $name . '</span>';
                        }
                        $apply .= '</div>';
                    }

                    $products = @unserialize($data['products']);
                    if (is_array($products) && count($products) > 0) {
                        $apply .= '<div class="d-flex mb-2">';
                        $apply .= '<strong class="me-2">' . $jatbi->lang('Sản phẩm') . '</strong>';
                        foreach ($products as $product) {
                            $name = $app->get("products", "name", ["id" => $product]);
                            $apply .= '<span class="badge bg-primary me-2 p-1">' . $name . '</span>';
                        }
                        $apply .= '</div>';
                    }

                    $customers = @unserialize($data['customers']);
                    if (is_array($customers) && count($customers) > 0) {
                        $apply .= '<div class="d-flex mb-2">';
                        $apply .= '<strong class="me-2">' . $jatbi->lang('Khách hàng') . '</strong>';
                        foreach ($customers as $customer) {
                            $name = $app->get("customers", "name", ["id" => $customer]);
                            $apply .= '<span class="badge bg-primary me-2 p-1">' . $name . '</span>';
                        }
                        $apply .= '</div>';
                    }
                    $datas[] = [
                        "checkbox" => $app->component("box", ["data" => $data['id']]),
                        "name" => $data['name'],
                        "code" => $data['code'] ?? '',
                        "count_used" => $data['count_used'] . '/' . ($data['count']),
                        "dueDate" => date("d/m/Y", strtotime($data['date_start'])) .'-'. date("d/m/Y", strtotime($data['date_end'])) . '<br>' . $dueDate,
                        "discount" => ($data['type'] == 1 ? $data['percent'] . '%' : number_format($data['price'])),
                        "apply" => $apply,
                        "notes" => $data['notes'] ?? '',
                        "date" => date("d/m/Y H:i:s", strtotime($data['date'])),
                        "accounts" => $app->get("accounts","name",["id"=>$data['user']]),
                        "status" => $app->component("status", [
                            "url" => "/coupons/coupons-status/" . $data['id'],
                            "data" => $data['status'],
                            "permission" => ['coupons.edit']
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
                                    'permission' => ['coupons.edit'],
                                    'action' => ['data-url' => '/coupons/coupons-edit/' . $data['id'], 'data-action' => 'modal']
                                ],
                                [
                                    'type' => 'button',
                                    'name' => $jatbi->lang("Xóa"),
                                    'permission' => ['coupons.deleted'],
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
        })->setPermissions(['coupons']);
        //type-payments start
        $app->router('/type-payments', ['GET', 'POST'], function($vars) use ($app, $jatbi, $setting) {
            $jatbi->permission('type-payments');
            $vars['title'] = $jatbi->lang("Hình thức thanh toán");

            if ($app->method() === 'GET') {
                echo $app->render($setting['template'] . '/admin/type-payments.html', $vars);
            } 
            elseif ($app->method() === 'POST') {
                // Đặt header chuẩn UTF-8 để tránh lỗi JSON
                $app->header(['Content-Type' => 'application/json; charset=utf-8']);
                
                // Lấy các tham số từ DataTables
                $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
                $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
                $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
                
                $globalSearch = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
                $nameFilter = isset($_POST['name_filter']) ? $_POST['name_filter'] : '';
                $statusValue = isset($_POST['status']) ? $_POST['status'] : '';

                $orderName = isset($_POST['order'][0]['name']) ? $_POST['order'][0]['name'] : 'id';
                $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';

                // Xây dựng điều kiện WHERE
                $where = [
                    "AND" => [
                        "deleted" => 0,
                    ]
                ];

                // Xử lý tìm kiếm
                if ($nameFilter != '') {
                    $where['AND']['name[~]'] = $nameFilter;
                } elseif ($globalSearch != '') {
                    $where['AND']['OR'] = [
                        'name[~]' => $globalSearch,
                        'notes[~]' => $globalSearch,
                    ];
                }

                // Lọc theo trạng thái
                $where['AND']['status'] = ($statusValue != '') ? $statusValue : ['A', 'D'];

                // Đếm tổng số bản ghi
                $count = $app->count("type_payments", $where);
                
                // Thêm LIMIT và ORDER để phân trang
                $where["LIMIT"] = [$start, $length];
                $where["ORDER"] = [$orderName => strtoupper($orderDir)]; 
                $datas = [];
                $allData = $app->select("type_payments", "*", $where);



        
                foreach ($allData as $data) {
                    // Lấy thông tin tài khoản có
                    $credit_info = $app->get("accountants_code", ["code", "name"], ["code" => $data['has']]);
                    $credit_account_text = $credit_info ? ($credit_info['code'] . ' - ' . $credit_info['name']) : ($data['has'] ?? '');

                    // Lấy thông tin tài khoản nợ
                    $debit_info = $app->get("accountants_code", ["code", "name"], ["code" => $data['debt']]);
                    $debit_account_text = $debit_info ? ($debit_info['code'] . ' - ' . $debit_info['name']) : ($data['debt'] ?? '');

                    $datas[] = [
                        "checkbox" => $app->component("box", ["data" => $data['active'] ?? '']),
                        "name" => $data['name'] ?? '',
                        "credit_account" => $credit_account_text,
                        "debit_account" => $debit_account_text,
                        "main" => $app->get("type_payments", "name", ["id" => $data['main']]),
                        "payment_type" => $setting["type-payment"][$data['type']]['name'] ?? 'Không xác định',
                        "notes" => $data['notes'] ?? '',
                        "status" => $app->component("status", [
                            "url" => "/admin/type-payments-status/" . ($data['active'] ?? ''),
                            "data" => $data['status'] ?? '',
                            "permission" => ['type-payments.edit']
                        ]),
                        "action" => $app->component("action", [
                            "button" => [
                                [
                                    'type' => 'button',
                                    'name' => $jatbi->lang("Sửa"),
                                    'permission' => ['type-payments.edit'],
                                    'action' => ['data-url' => '/admin/type-payments-edit/' . ($data['active'] ?? ''), 'data-action' => 'modal']
                                ],
                                [
                                    'type' => 'button',
                                    'name' => $jatbi->lang("Xóa"),
                                    'permission' => ['type-payments.deleted'],
                                    'action' => ['data-url' => '/admin/type-payments-deleted?box=' . ($data['active'] ?? ''), 'data-action' => 'modal']
                                ],
                            ]
                        ]),
                    ];
                }

                // Trả về dữ liệu JSON
                echo json_encode(
                    [
                        "draw" => $draw,
                        "recordsTotal" => $count,
                        "recordsFiltered" => $count,
                        "data" => $datas ?? []
                    ],
        
                );
                
            }
        })->setPermissions(['type-payments']);
        //type-payments end

    })->middleware('login');
?>