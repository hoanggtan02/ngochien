<?php
    if (!defined('ECLO')) die("Hacking attempt");
    $app->group($setting['manager']."/hrm" , function($app) use($jatbi,$setting){
        $app->router("/personnels", ['GET','POST'], function($vars) use ($app, $jatbi,$setting) {
            $vars['router'] = 'notification';
            if($app->method()==='GET'){
                $vars['title'] = $jatbi->lang("Thông báo");
                echo $app->render($setting['template'].'/users/profile.html', $vars);
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
                $where = [
                    "OR" => [
                        "notifications.title[~]" => $searchValue,
                        "notifications.content[~]" => $searchValue,
                        "accounts.name[~]" => $searchValue,
                    ],
                    "notifications.account" => $app->getSession("accounts")['id'],
                    "notifications.deleted" => 0,
                    "LIMIT" => [$start, $length],
                    "ORDER" => [$orderName => strtoupper($orderDir)]
                ];
                $count = $app->count("notifications",[
                    "OR" => [
                        "notifications.title[~]" => $searchValue,
                        "notifications.content[~]" => $searchValue,
                    ],
                    "notifications.account" => $app->getSession("accounts")['id'],
                    "notifications.deleted" => 0,
                ]);
                $app->select("notifications", [
                        "[>]accounts" => ["user" => "id"]
                    ], 
                    [
                    'notifications.id',
                    'notifications.template',
                    'notifications.date',
                    'notifications.title',
                    'notifications.active',
                    'notifications.views',
                    'notifications.content',
                    'notifications.user',
                    'accounts.name',
                    'accounts.avatar',
                    ], $where, function ($data) use (&$datas,$jatbi,$app) {
                        if (isset($data['data']) && $data['data'] != '') {
                            $getdata = json_decode($data['data']);
                        } else {
                            $getdata = null;
                        }
                        $content = $jatbi->lang($data['content']);
                        $content = str_replace("[account]", $data['name'], $content);
                        if ($getdata && isset($getdata->content)) {
                            $content = str_replace("[content]", number_format($getdata->content), $content);
                        } else {
                            $content = str_replace("[content]", "0", $content);
                        }
                        if($data['template']=='url'){
                            $url = '<a class="btn btn-sm btn-primary-light border-0 p-2" href="/users/notification/'.$data['active'].'" data-pjax><i class="ti ti-eye"></i></a>';
                            $content = '<a class="link-primary" href="/users/notification/'.$data['active'].'" data-pjax><span class="width height bg-'.($data['views']>0?'secondary':'danger').' rounded-circle d-inline-block me-2" style="--width:10px;--height:10px"></span>'.$content.'</a>';
                        }
                        else {
                            $url = '<a class="btn btn-sm btn-primary-light border-0 p-2" data-action="modal" data-url="/users/notification/'.$data['active'].'"><i class="ti ti-eye"></i></a>';
                            $content = '<a class="link-primary" href="/users/notification/'.$data['active'].'" data-pjax><span class="width height bg-'.($data['views']>0?'secondary':'danger').' rounded-circle d-inline-block me-2" style="--width:10px;--height:10px"></span>'.$content.'</a>';
                        }
                        $datas[] = [
                            "checkbox" => $app->component("box",["data"=>$data['id']]),
                            "content" => $content,
                            "url" => $url,
                            "date" => $jatbi->datetime($data['date']),
                        ];
                });
                echo json_encode([
                    "draw" => $draw,
                    "recordsTotal" => $count,
                    "recordsFiltered" => $count,
                    "data" => $datas ?? []
                ]);
            }
        });
    })->middleware('login');
?>