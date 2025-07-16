<?php
if (!defined('ECLO')) die("Hacking attempt");

class plugin {
    private $pluginDir;
    protected $app;
    protected $jatbi;
    public function __construct($pluginDir, $app, $jatbi) {
        $this->pluginDir = realpath($pluginDir);
        $this->app = $app;
        $this->jatbi = $jatbi;
        if (!$this->pluginDir || !is_dir($this->pluginDir)) {
            throw new Exception("Thư mục plugin không tồn tại hoặc không có quyền truy cập.");
        }
    }
    public function getPlugins() {
        $plugins = [];
        $pluginRecords = $this->app->select("plugins", "*", ["status" => 'A', 'install' => 1]);

        foreach ($pluginRecords as $record) {
            $pluginSlug = basename($record['plugins']);
            $potentialPath = $this->pluginDir . DIRECTORY_SEPARATOR . $pluginSlug;
            $pluginPath = realpath($potentialPath);
            if (!$pluginPath || !is_dir($pluginPath) || strpos($pluginPath, $this->pluginDir) !== 0) {
                error_log("Cảnh báo bảo mật: Cố gắng truy cập plugin ngoài thư mục cho phép. Plugin slug: " . $pluginSlug);
                continue;
            }
            $configFile = $pluginPath . '/config.json';
            if (file_exists($configFile)) {
                $configContent = file_get_contents($configFile);
                $config = json_decode($configContent, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $plugins[$pluginPath] = $config;
                } else {
                    error_log("Lỗi JSON trong plugin: $pluginPath - " . json_last_error_msg());
                }
            }
        }
        return $plugins;
    }
    public function loadRequests(&$requests) {
        if (!is_array($requests)) {
            $requests = [];
        }
        $loader = function($file, $app, $jatbi) {
            return require $file;
        };
        foreach ($this->getPlugins() as $pluginPath => $config) {
            $requestFile = $pluginPath . '/requests.php';
            if (file_exists($requestFile)) {
                $pluginRequest = $loader($requestFile, $this->app, $this->jatbi);
                if (is_array($pluginRequest)) {
                    $requests = array_replace_recursive($requests, $pluginRequest);
                }
                foreach (glob($pluginPath.'/controllers/*.php') as $routeFile) {
                    $loader($routeFile, $this->app, $this->jatbi);
                }
                foreach (glob($pluginPath.'/router/*.php') as $routeFile) {
                    $loader($routeFile, $this->app, $this->jatbi);
                }
            }
        }
    }
}
?>
