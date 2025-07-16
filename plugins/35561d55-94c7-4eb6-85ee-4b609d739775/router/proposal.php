<?php
    $template = __DIR__.'/../templates';
    $common = $app->getValueData('common');
    $app->group("/proposal",function($app) use($jatbi,$template,$common) {
        $app->router("",['GET','POST'], function($vars) use($app,$jatbi,$template) {
            if($app->method()==='GET'){
                echo $app->render($template.'/plugin.html', $vars);
            }
            else {
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
                    'logs.active',
                    'accounts.name',
                    'accounts.avatar',
                    ], $where, function ($data) use (&$datas,$jatbi) {
                        $datas[] = [
                            "user" => '<img data-src="/' . $data['avatar'] . '?type=thumb" class="width rounded-circle lazyload me-2" style="--width:40px"> '.$data['name'],
                            "dispatch" => $data['dispatch'],
                            "action" => $data['action'],
                            "url" => $data['url'],
                            "ip" => $data['ip'],
                            "date" => $jatbi->datetime($data['date']),
                            "views" => '<button data-action="modal" data-url="/admin/logs-views/'.$data['active'].'" class="btn btn-eclo-light btn-sm border-0 py-1 px-2 rounded-3" aria-label="'.$jatbi->lang('Xem').'"><i class="ti ti-eye"></i></button>',
                        ];
                });
                echo json_encode($datas);
            }
        });
        $app->router("/report",'GET', function($vars) use($app,$jatbi,$template) {
            echo $app->render($template.'/plugin.html', $vars);
        });
        $app->router("/config",'GET', function($vars) use($app,$jatbi,$template,$common) {
            $vars['title'] = $jatbi->lang("Cấu hình đề xuất");
            $vars['name'] = $jatbi->lang("Hình thức");
            $vars['template'] = 'type';
            echo $app->render($template.'/config.html', $vars);
        });
    });
 ?>