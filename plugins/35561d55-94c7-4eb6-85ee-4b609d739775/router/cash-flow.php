<?php 
    $jatbi = $app->getValueData('jatbi');
    $setting = $app->getValueData('setting');
    $template = __DIR__.'/../templates';
    $app->group("/cash-flow",function($app) use($setting,$jatbi) {
        $app->router("/private",'GET', function($vars) use($app,$jatbi) {
            $template = __DIR__.'/../templates';
            // echo $template;
            echo $app->render($template.'/plugin.html', $vars);
        });
    });
 ?>