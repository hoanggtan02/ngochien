<?php
	if (!defined('ECLO')) die("Hacking attempt");
	$app->group($setting['manager'],function($app) use ($jatbi,$setting,$common){
        $app->router(($setting['manager']==''?'/':''),'GET', function($vars) use ($app,$setting,$common) {
            $account_id = $app->getSession("accounts")['id'] ?? null;
            $account = $account_id ? $app->get("accounts", "*", ["id" => $account_id]) : [];
            $month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
            $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
            $firstDayOfMonth = strtotime($year.'-'.$month.'-01');
            $dayLastMonth = date('t', $firstDayOfMonth);
            $startDay = date('w', $firstDayOfMonth);
            $startDay = $startDay==0?7:$startDay;
            $date_start = date("Y-m-01 00:00:00",strtotime($year.'-'.$month.'-01'));
            $date_end = date("Y-m-t 23:59:59",strtotime($year.'-'.$month.'-01'));
            $weeks = $common['weeks'];
            $vars = [
                "weeks" => $weeks,
                "startDay" => $startDay,
                "month" => $month,
                "year" => $year,
                "dayLastMonth" => $dayLastMonth,
                "account" => $account,
                "date_start" => $date_start,
                "date_end" => $date_end,
            ];
            
            echo $app->render($setting['template'].'/pages/home.html', $vars);
        });
    })->middleware('login');
    $app->router("::404",'GET', function($vars) use ($app,$jatbi,$setting) {
        echo $app->render($setting['template'].'/pages/error.html', $vars, $jatbi->ajax());
    });
?>