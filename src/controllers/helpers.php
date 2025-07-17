<?php 
	if (!defined('ECLO')) die("Hacking attempt");
	class Jatbi {
		protected $app;
	    public function __construct($app) {
	        $this->app = $app;
	    }
		
	    public function date($date){
	    	$getConfig = $this->app->get("config","*");
	    	return date($getConfig['date'],strtotime($date));
	    }
	    public function time($time){
	    	$getConfig = $this->app->get("config","*");
	    	return date($getConfig['time'],strtotime($time));
	    }
	    public function datetime($date){
	    	$getConfig = $this->app->get("config","*") ?? ["date" => "d/m/Y" ,"time"=>"H:i:s"];
	    	return date(($getConfig['time'] ?? 'H:i:s').' '.($getConfig['date'] ?? 'd/m/Y'),strtotime($date));
	    }
		public function ajax($type = null){
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){
				return 'global';
			}
			if(isset($type)){
				if($type=='show'){
					return ' show';
				}
				else {
					return ' show d-block position-static';
				}
			}
		}
	    public function csrfToken() {
	        return $_SESSION['csrf_token'] ?? '';
	    }
	    public function csrfField() {
	        return '<input type="hidden" name="csrf_token" value="' . $this->csrfToken() . '">';
	    }
	    public function verifyCsrfToken() {
	        if (!isset($_POST['csrf_token']) || !hash_equals($this->app->getSession('csrf_token'), $_POST['csrf_token'])) {
	            http_response_code(403);
	            die('Invalid CSRF token.');
	        }
	    }
	    public function lang($key) {
		    global $lang;
		    return isset($lang[$key]) ? $lang[$key] : $key;
		}
	    public function url($path = '') {
		    $setting = $this->app->getValueData('setting');
		    $manager = rtrim($setting['manager'] ?? '', '/');
		    $path = ($path === '/') ? '' : $path;

		    if ($manager === '' && $path === '') {
		        return '/';
		    }

		    return $manager . $path;
		}
		public function totalStorage(){
		    $account = $this->app->get("accounts","*",["id"=>$this->app->getSession("accounts")['id']]);
		    $totalStorage = 5 * 1024 * 1024 * 1024; // 5GB = 5 * 1024^3 bytes
		    $where = [
		        "AND" => [
		            "account" => $account['id'],
		            "deleted" => 0,
		        ]
		    ];
		    // Đảm bảo $sum luôn là số
		    $sum = floatval($this->app->sum("files", "size", ["AND" => $where['AND']]) ?? 0);
		    $data['total_size'] = $this->formatFileSize($sum);
		    $usedPercentage = ($sum / $totalStorage) * 100;
		    $data['used_percentage'] = round($usedPercentage, 2);
		    
		    return $data;
		}
		public function checkFiles($active) {
		    $account = $this->app->get("accounts", "*", ["id" => $this->app->getSession("accounts")['id']]);
		    $files = $this->app->get("files", "*", [
		        "active" => $active,
		        // "deleted" => 0,
		    ]);
		    if (!$files) {
		        return false; // Trả về 0 nếu file không tồn tại
		    }
		    $getPermission = $this->app->count("files_accounts", "id", [
		        "data" => $files['id'],
		        "type" => 'files',
		        "account" => $account['id'],
		        // "deleted" => 0,
		    ]);
		    if ($files['account'] == $account['id']) { // Nếu là chủ sở hữu
			    $viewer = true; // Chủ sở hữu luôn có quyền
			} else { // Nếu không phải chủ sở hữu
			    if ($files['permission'] == 0) {
			        $viewer = false; // Không chia sẻ, chỉ chủ sở hữu có quyền
			    } else if ($files['permission'] == 1 && $getPermission > 0) {
			        $viewer = false; // Private và có quyền trong files_accounts => Không có quyền
			    } else if ($files['permission'] == 2 && $getPermission == 0) {
			        $viewer = false; // Public và không có quyền trong files_accounts => Không có quyền
			    } else {
			        $viewer = true; // Trường hợp còn lại (Public và có quyền, Private và không có quyền) => Có quyền
			    }
			}
		    if (!$viewer && $files['category'] != 0) { // Nếu không có quyền trực tiếp và file có category
		        $folder = $this->folders($files['category']); 
		        foreach ($folder as $item) {
		            if ($item['id'] == $files['category']) {
		                $viewer = true;
		                break;
		            }
		        }
		    }
		    return $viewer;
		}
		public function checkFolder($active){
			$folder = $this->app->get("files_folders","*",[
	            "active"=>$active,
	            "deleted"=>0,
	        ]);
	        $getFolder = $this->folders($folder['id']);
	        $found = false;
	        foreach ($getFolder as $item) {
	            if ($item['id'] == $folder['id']) {
	                $found = true;
	                break;
	            }
	        }
	        return $found;
		}
		public function getParentFolders($folder_id) {
		    $parentFolders = [];
		    $currentFolderId = $folder_id;

		    while ($currentFolderId > 0) {
		        $folder = $this->app->get("files_folders", ["id", "main"], ["id" => $currentFolderId]);
		        if (!$folder) break;

		        $parentFolders[] = $folder["id"];
		        $currentFolderId = $folder["main"];
		    }

		    return array_reverse($parentFolders); // Đảo ngược để có thứ tự từ gốc đến con
		}
		public function getChildFolders($folder_id) {
		    $childFolders = [];
		    $folders = $this->app->select("files_folders", ["id"], ["main" => $folder_id]);
		    foreach ($folders as $folder) {
		        $childFolders[] = $folder["id"];
		        $childFolders = array_merge($childFolders, $this->getChildFolders($folder["id"]));
		    }
		    return $childFolders;
		}
		public function getFilesInFolder($folder_id) {
		    return $this->app->select("files","*", ["category" => $folder_id]);
		}
		public function duplicateFolderStructure($folder_id, $new_parent_id = 0) {
			$account = $this->app->get("accounts", "*", ["id" => $this->app->getSession("accounts")['id']]);
		    // Lấy thông tin thư mục gốc
		    $originalFolder = $this->app->get("files_folders", ["name"], ["id" => $folder_id]);
		    if (!$originalFolder) return false;

		    // Tạo thư mục mới
		    $this->app->insert("files_folders", [
		        "name" => $originalFolder["name"],
		        "main" => $new_parent_id,
		        "account" => $account['id'],
		        "active" => $this->active(),
		       	"modify" => date("Y-m-d H:i:s"),
                "date" => date("Y-m-d H:i:s"),
                "permission" => 0,
		    ]);
		    $newFolderId = $this->app->id();
		    // Lấy tất cả file của thư mục gốc và nhân bản
		    $files = $this->getFilesInFolder($folder_id);
		    foreach ($files as $data) {
		        $geturl = $data['url']; // URL của file cần sao chép
                $folder_path = 'datas/'.$account['active']; 
                $name_file = $this->active();
                $destination = $folder_path.'/'.$name_file.'.'.$data['extension']; // Đổi tên file khi lưu
                if (copy($geturl, $destination)) {
                    $url = $destination;
                }
		        $insert = [
                    "category" => $newFolderId,
                    "account" => $account['id'],
                    "name" => $data['name'],
                    "extension" => $data['extension'],
                    "url" => $url,
                    "data" => $data['data'],
                    "size" => $data['size'],
                    "mime" => $data['mime'],
                    "permission" => 0,
                    "modify" => date("Y-m-d H:i:s"),
                    "date" => date("Y-m-d H:i:s"),
                    "active" => $this->active(),
                ];
		        $this->app->insert("files", $insert);
		    }
		    $childFolders = $this->getChildFolders($folder_id);
		    foreach ($childFolders as $childFolderId) {
		        $this->duplicateFolderStructure($childFolderId, $newFolderId);
		    }
		    return $newFolderId;
		}
		public function folders($folder_id) {
		    $breadcrumb = [];
		    $account = $this->app->get("accounts", "*", ["id" => $this->app->getSession("accounts")]);
		    $parentFolders = $this->getParentFolders($folder_id);
		    // Kiểm tra xem người dùng có phải là chủ sở hữu không
		    $isOwner = false;
		    if (!empty($parentFolders)) {
		        $firstFolder = $this->app->get("files_folders", ["account"], ["id" => $parentFolders[0]]);
		        if ($firstFolder && $firstFolder["account"] == $account['id']) {
		            $isOwner = true;
		        }
		    }
		    if ($isOwner) {
		        // Nếu là chủ sở hữu, hiển thị breadcrumb đầy đủ
		        foreach ($parentFolders as $id) {
		            $folder = $this->app->get("files_folders", ["id", "name","active"], ["id" => $id]);
		            if ($folder) {
		                $breadcrumb[] = ["id" => $folder["id"], "name" => $folder["name"], "active"=>$folder['active']];
		            }
		        }
		    } else {
		        // Nếu không phải chủ sở hữu, áp dụng logic chia sẻ
		        $sharedFolderIndex = -1;
		        foreach ($parentFolders as $index => $id) {
		            $folder = $this->app->get("files_folders", ["id", "name", "permission", "account", "active"], ["id" => $id]);
		            if (!$folder) continue;

		            $hasPermission = $this->app->count("files_accounts", "id", [
		                "data" => $folder["id"],
		                "type" => "folder",
		                "account" => $account['id'],
		                "deleted" => 0
		            ]);

		            $viewer = ($folder["account"] == $account['id']); // Chủ sở hữu luôn có quyền
		            if (!$viewer) {
		                $viewer = ($folder["permission"] == 1 && $hasPermission == 0) || ($folder["permission"] == 2 && $hasPermission > 0);
		            }

		            if ($viewer && $folder["permission"] > 0) {
		                $sharedFolderIndex = $index;
		            }
		        }

		        if ($sharedFolderIndex >= 0) {
		            for ($i = $sharedFolderIndex; $i < count($parentFolders); $i++) {
		                $folder = $this->app->get("files_folders", ["id", "name","active"], ["id" => $parentFolders[$i]]);
		                if ($folder) {
		                    $breadcrumb[] = ["id" => $folder["id"], "name" => $folder["name"],"active"=>$folder['active']];
		                }
		            }
		        }
		    }

		    return $breadcrumb;
		}
		public function formatResponse($response) {
		    $response = htmlspecialchars($response, ENT_QUOTES, 'UTF-8');
		    if (substr_count($response, '```') % 2 != 0) {
		        $response .= "\n```";
		    }
		    $pattern = '/```(\w*)\n([\s\S]*?)```/';
		    $replacement = '<pre><code class="language-$1">$2</code></pre>';
		    $formattedResponse = preg_replace($pattern, $replacement, $response);
		    $formattedResponse = nl2br($formattedResponse);
		    return $formattedResponse;
		}
		public function active() {
		    $uuid = '';
		    for ($i = 0; $i < 8; $i++) {
		        $uuid .= dechex(mt_rand(0, 15));
		    }
		    $uuid .= '-';
		    for ($i = 0; $i < 4; $i++) {
		        $uuid .= dechex(mt_rand(0, 15));
		    }
		    $uuid .= '-4';
		    for ($i = 0; $i < 3; $i++) {
		        $uuid .= dechex(mt_rand(0, 15));
		    }
		    $uuid .= '-';
		    $uuid .= dechex(mt_rand(8, 11));
		    for ($i = 0; $i < 3; $i++) {
		        $uuid .= dechex(mt_rand(0, 15));
		    }
		    $uuid .= '-';
		    for ($i = 0; $i < 12; $i++) {
		        $uuid .= dechex(mt_rand(0, 15));
		    }
		    return $uuid;
		}
		public function searchFiles($getType){
			$mimeTypes = [];
			switch ($getType) {
			    case 'images':
			        $mimeTypes = [
			            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 
			            'image/bmp', 'image/tiff', 'image/svg+xml', 'image/x-icon'
			        ];
			        break;
			    
			    case 'doc':
			        $mimeTypes = [
			            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			            'application/pdf', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			            'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			            'text/plain', 'application/rtf'
			        ];
			        break;
			    
			    case 'audio':
			        $mimeTypes = [
			            'audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/aac', 
			            'audio/mp4', 'audio/webm', 'audio/flac'
			        ];
			        break;
			}
			return $mimeTypes;
		}
		public function getFileIcon($filePath) {
		    $files = $this->app->get("files","*",["active"=>$this->app->xss($filePath)]);
		    $fileName = strtolower($files['url']);
		    $iconsPath = "assets/icons/";

		    // Định dạng ảnh -> Trả về chính URL của file
		    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
		    if ($this->hasExtension($fileName, $imageExtensions)) {
		        return '/files/views/'.$files['active']; // Trả về chính URL file hình ảnh
		    }

		    // Định dạng PDF
		    if ($this->hasExtension($fileName, ['pdf'])) {
		        return '/'.$iconsPath . "pdf.png";
		    }

		    // Định dạng văn bản
		    if ($this->hasExtension($fileName, ['txt'])) {
		        return '/'.$iconsPath . "files.png";
		    }

		    // Định dạng nén (RAR, ZIP)
		    if ($this->hasExtension($fileName, ['rar'])) {
		        return '/'.$iconsPath . "rar.png";
		    }
		    if ($this->hasExtension($fileName, ['zip'])) {
		        return '/'.$iconsPath . "zip.png";
		    }

		    // Định dạng âm thanh
		    $audioExtensions = ['mp3', 'wav', 'aac', 'flac', 'ogg', 'wma'];
		    if ($this->hasExtension($fileName, $audioExtensions)) {
		        return '/'.$iconsPath . "audio.png";
		    }

		    // Định dạng PowerPoint
		    $pptExtensions = ['ppt', 'pptx', 'pps', 'ppsx'];
		    if ($this->hasExtension($fileName, $pptExtensions)) {
		        return '/'.$iconsPath . "ppt.png";
		    }

		    // Định dạng Word
		    $wordExtensions = ['doc', 'docx', 'dot', 'dotx', 'rtf'];
		    if ($this->hasExtension($fileName, $wordExtensions)) {
		        return '/'.$iconsPath . "doc.png";
		    }

		    // Định dạng Excel
		    $excelExtensions = ['xls', 'xlsx', 'xlsm', 'csv'];
		    if ($this->hasExtension($fileName, $excelExtensions)) {
		        return '/'.$iconsPath . "xls.png";
		    }

		    // Trả về icon mặc định nếu không tìm thấy loại file
		    return '/'.$iconsPath . "files.png";
		}
		public function hasExtension($fileName, $extensions) {
		    foreach ($extensions as $ext) {
		        if (str_ends_with($fileName, "." . $ext)) {
		            return true;
		        }
		    }
		    return false;
		}
		public function createZipFromFolder($folder_id) {
		    $downloadDir = "download/";
		    if (!is_dir($downloadDir)) {
		        mkdir($downloadDir, 0777, true);
		    }
		    $files = $this->getAllFilesFromFolder($folder_id);
		    if (empty($files)) return false;
		    // Lấy tên thư mục gốc
		    $folder_name = $this->app->get("files_folders", "name", ["id" => $folder_id]) ?? "folder_" . $folder_id;
		    $zipFilePath = $downloadDir . $folder_name . "_" . time() . ".zip";

		    $zip = new ZipArchive();
		    if ($zip->open($zipFilePath, ZipArchive::CREATE) !== TRUE) {
		        return false;
		    }
		    // Danh sách file đã thêm để kiểm tra trùng lặp
		    $addedFiles = [];
		    // Thêm file vào ZIP giữ nguyên cấu trúc thư mục
		    foreach ($files as $file) {
		        $file_path = $file["url"];
		        if (file_exists($file_path)) {
		            $relative_path = $this->buildRelativePath($file["category"], $file["name"]);

		            // Kiểm tra nếu file bị trùng, thêm timestamp
		            if (isset($addedFiles[$relative_path])) {
		                $pathInfo = pathinfo($relative_path);
		                $relative_path = $pathInfo['dirname'] . "/" . $pathInfo['filename'] . "_" . time() . "." . $pathInfo['extension'];
		            }

		            $zip->addFile($file_path, $relative_path);
		            $addedFiles[$relative_path] = true;
		        }
		    }
		    $zip->close();
		    return $zipFilePath;
		}
		public function getAllFilesFromFolder($folder_id) {
		    $folders = [$folder_id]; // Danh sách thư mục cần lấy file
		    $allFiles = [];
		    // Lấy tất cả thư mục con đệ quy
		    $subFolders = $this->app->select("files_folders", ["id"], ["main" => $folder_id]);
		    foreach ($subFolders as $subFolder) {
		        $folders[] = $subFolder["id"];
		    }
		    // Lấy tất cả file trong các thư mục
		    foreach ($folders as $fid) {
		        $files = $this->app->select("files", ["id", "url", "name", "category"], ["category" => $fid]);
		        $allFiles = array_merge($allFiles, $files);
		    }
		    return $allFiles;
		}
		public function buildRelativePath($folder_id, $file_name) {
		    $path_parts = [];
		    while ($folder_id > 0) {
		        $folder = $this->app->get("files_folders", ["id", "name", "main"], ["id" => $folder_id]);
		        if (!$folder) break;
		        $path_parts[] = $folder["name"];
		        $folder_id = $folder["main"];
		    }
		    return implode("/", array_reverse($path_parts)) . "/" . $file_name;
		}
		public function viewsFile($filePath,$token) {
			$setting = $this->app->getValueData("setting");
		    $files = $this->app->get("files","*",["active"=>$this->app->xss($filePath)]);
		    $fileName = strtolower($files['url']);
		    $iconsPath = "assets/icons/";
		    $domain = $setting['url']."/files/data";
		    $path = $domain.'/'.$files['active'].'?token='.$token;
		    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
		    if ($this->hasExtension($fileName, $imageExtensions)) {
		        return '<img src='.$path.' class="h-100">'; // Trả về chính URL file hình ảnh
		    }

		    // Định dạng PDF
		    if ($this->hasExtension($fileName, ['pdf'])) {
		        return "<iframe src='https://docs.google.com/viewer?url=".$path."&embedded=true' width='100%' style='height: calc(100vh - 125px);'' frameborder='0'></iframe>";
		    }

		    // Định dạng âm thanh
		    $audioExtensions = ['mp3', 'wav', 'aac', 'flac', 'ogg', 'wma'];
		    if ($this->hasExtension($fileName, $audioExtensions)) {
		        return '/'.$iconsPath . "audio.png";
		    }

		    // Định dạng PowerPoint
		    $pptExtensions = ['ppt', 'pptx', 'pps', 'ppsx'];
		    if ($this->hasExtension($fileName, $pptExtensions)) {
		        return "<iframe src='https://view.officeapps.live.com/op/embed.aspx?src=".$path."' width='100%' style='height: calc(100vh - 125px);'' frameborder='0'></iframe>";
		    }

		    // Định dạng Word
		    $wordExtensions = ['doc', 'docx', 'dot', 'dotx', 'rtf'];
		    if ($this->hasExtension($fileName, $wordExtensions)) {
		       return "<iframe src='https://view.officeapps.live.com/op/embed.aspx?src=".$path."' width='100%' style='height: calc(100vh - 125px);'' frameborder='0'></iframe>";
		    }

		    // Định dạng Excel
		    $excelExtensions = ['xls', 'xlsx', 'xlsm', 'csv'];
		    if ($this->hasExtension($fileName, $excelExtensions)) {
		        return "<iframe src='https://view.officeapps.live.com/op/embed.aspx?src=".$path."' width='100%' style='height: calc(100vh - 125px);'' frameborder='0'></iframe>";
		    }

		    // Trả về icon mặc định nếu không tìm thấy loại file
		    return 'tải về';
		}
		public function formatFileSize($sizeInKB) {
		    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
			$size = is_numeric($sizeInKB) ? floatval($sizeInKB) : 1000;
		    $unitIndex = 0;
		    while ($size >= 1024 && $unitIndex < count($units) - 1) {
		        $size /= 1024;
		        $unitIndex++;
		    }
		    return round($size ?? 0, 2) . " " . $units[$unitIndex];
		}
		public function getFileExtension($filename) {
		    return pathinfo($filename, PATHINFO_EXTENSION);
		}
		public function deleteFolder($folderPath) {
		    if (!is_dir($folderPath)) {
		        return false;
		    }
		    $files = array_diff(scandir($folderPath), ['.', '..']);
		    foreach ($files as $file) {
		        $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;
		        if (is_dir($filePath)) {
		            $this->deleteFolder($filePath);
		        } else {
		            unlink($filePath);
		        }
		    }
		    return rmdir($folderPath);
		}
	    public function checkAuthenticated($requests,$setting) {
	        if (empty($_SESSION['csrf_token'])) {
	            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	        }
	        if ($this->app->getCookie('token') && empty($this->app->getSession("accounts"))) {
		        $decoded = $this->app->decodeJWT($this->app->getCookie('token'), '');
		        if ($decoded) {
		            $accounts_login = $this->app->get("accounts_login","*",[
		                "accounts"  => $this->app->get("accounts","id",["active"=>$decoded->id]),
		                "token"     => $decoded->token,
		                "agent"     => $decoded->agent,
		                "deleted"   => 0,
		                "ORDER"     => [
		                    "id"    => "DESC",  
		                ]
		            ]);
		            if($accounts_login>1){
		                $checkUser = $this->app->get("accounts","*",["id"=>$accounts_login['accounts'],"status"=>"A","deleted"=>0]);
		                if($checkUser>1){
		                	$gettoken = $this->app->randomString(256);
			                $payload = [
			                    "ip"        => $this->app->xss($_SERVER['REMOTE_ADDR']),
			                    "id"        => $checkUser['active'],
			                    "email"     => $checkUser['email'],
			                    "token"     => $gettoken,
			                    "agent"     => $_SERVER["HTTP_USER_AGENT"],
			                ];
			                $token = $this->app->addJWT($payload);
			                $getLogins = $this->app->get("accounts_login","*",[
			                    "accounts"  => $checkUser['id'],
			                    "agent"     => $payload['agent'],
			                    "deleted"   => 0,
			                ]);
			                if($getLogins>1){
			                    $this->app->update("accounts_login",[
			                        "accounts" => $checkUser['id'],
			                        "ip"    =>  $payload['ip'],
			                        "token" =>  $payload['token'],
			                        "agent" =>  $payload["agent"],
			                        "date"  => date("Y-m-d H:i:s"),
			                    ],["id"=>$getLogins['id']]);
			                }
			                else {
			                    $this->app->insert("accounts_login",[
			                        "accounts" => $checkUser['id'],
			                        "ip"    =>  $payload['ip'],
			                        "token" =>  $payload['token'],
			                        "agent" =>  $payload["agent"],
			                        "date"  => date("Y-m-d H:i:s"),
			                    ]);
			                }
		                    $this->app->setSession('accounts',[
		                        "id" 		=> $checkUser['id'],
		                        "agent"     => $payload['agent'],
		                        "token"     => $payload['token'],
		                        "active" 	=> $checkUser['active'],
		                    ]);		
		                    $this->app->setCookie('token', $token,time()+((3600 * 24 * 30)*12),'/');                
		                }
		                else {
		                	$this->app->deleteSession('accounts');
		    				$this->app->deleteCookie('token');
		                }
		            }
		        }
		    }
		    $checkuser = $this->app->getSession("accounts");
		    if($checkuser){
		    	$getuser = $this->app->get("accounts",["id","type","permission"],["deleted"=>0,"status"=>'A',"id"=>$checkuser['id']]) ?? ["id"=>0];
		    	if($getuser['id']>0){
		    		$getPermission = $this->app->get("permission",["id","group"],[
		    			"deleted"=>0,
		    			"status"=>'A',
		    			"id"=>$getuser['permission']
		    		]);
			    	$getLogins = $this->app->get("accounts_login","id",[
	                    "accounts"  => $getuser['id'],
			            "token"     => $checkuser['token'],
	                    "agent"     => $checkuser['agent'],
	                    "deleted"   => 0,
	                ]);
	                if($getLogins>0 && $getPermission>1){
		    			$setPermission = ["login" => 'login'];
	                	$getPermission = unserialize($getPermission['group']);
	                	if (is_array($getPermission)) {
						    $setPermission = array_merge($setPermission, $getPermission);
						}
	                	$this->app->setUserPermissions($setPermission);
	                	foreach ($requests as $key => $menus) {
						    $main_names[$key]["name"] = $menus['name'];
						    $main_names[$key]["items"] = [];
						    if (!isset($menus['item']) || !is_array($menus['item'])) {
						        continue;
						    }
						    foreach ($menus['item'] as $key_item => $item) {
						        $url = $item['url'] ?? '';
						        if ($url !== '') {
						            $url = $this->url($url);
						        }
						        $main_names[$key]['items'][$key_item]["menu"] = $item['menu'] ?? '';
						        $main_names[$key]['items'][$key_item]["url"] = $url;
						        $main_names[$key]['items'][$key_item]["main"] = $item['main'] ?? '';
						        $main_names[$key]['items'][$key_item]["icon"] = $item['icon'] ?? '';
						        $main_names[$key]['items'][$key_item]["action"] = $item['action'] ?? '';
						        if (isset($item['permission'])) {
						            $hasValidSub = false;
						            if (!empty($item['sub']) && is_array($item['sub'])) {
						                foreach ($item['sub'] as $sub_key => $subs) {
						                    if (isset($item['permission'][$sub_key]) && isset($setPermission[$sub_key])) {
						                        if (isset($subs['router'])) {
						                            $subs['router'] = $this->url($subs['router']);
						                        }
						                        $main_names[$key]['items'][$key_item]["sub"][$sub_key] = $subs;
						                        $hasValidSub = true;
						                    }
						                }
						            }
						            if (!empty($item['sub']) && !$hasValidSub) {
						                unset($main_names[$key]['items'][$key_item]);
						                continue;
						            }
						            if ((empty($item['sub']) || !isset($item['sub'])) 
						                && isset($item['permission'][$key_item]) 
						                && !isset($setPermission[$key_item])) {
						                unset($main_names[$key]['items'][$key_item]);
						                continue;
						            }
						        }
						    }
						}

	                	$this->app->setValueData('menu', $main_names);
	                }
			        else {
			        	$this->app->deleteSession('accounts');
			    		$this->app->deleteCookie('token');
			        }
		    	}
		        else {
		        	$this->app->deleteSession('accounts');
		    		$this->app->deleteCookie('token');
		        }
		    }
	    }
	    public function permission($permissions) {
		    $checkuser = $this->app->getSession("accounts");
		    if ($checkuser) {
		        $getuser = $this->app->get("accounts", ["id", "type", "permission"], [
		            "deleted" => 0,
		            "status" => 'A',
		            "id" => $checkuser['id']
		        ]);
		        if (!empty($getuser['id'])) {
		            $getPermission = $this->app->get("permissions", ["id", "permissions"], [
		                "deleted" => 0,
		                "status" => 'A',
		                "id" => $getuser['permission']
		            ]);
		            $userPermissions = !empty($getPermission['permissions']) ? json_decode($getPermission['permissions'], true) : [];
		            if (empty($userPermissions)) {
		                return false;
		            }
		            if (empty($permissions)) {
		                return true;
		            }
		            return (bool) array_intersect((array) $permissions, $userPermissions);
		        }
		    }
		    return false;
		}
		public function notification($user,$account,$title,$body,$click_action,$template=null,$type=null,$data=null){
			global $setting;
			if($template==''){
				$template = 'url';
			}
			$insert = [
				"user" => $user,
				"account" => $account,
				"title" => $title,
				"content" => $body,
				"url" => $click_action,
				"date" => date("Y-m-d H:i:s"),
				"template" => $template,
				"active" => $this->active(),
				"type"=>  $type ?? 'content',
				"data" => $data,
			];
			$this->app->insert("notifications",$insert);
			// $getsetting = $this->app->get("settings","*",["account"=>$account]);
			// if($getsetting['notification']==1){
				// $cmd = 'php /www/wwwroot/ellm.io/dev/run/notification.php ' . escapeshellarg(json_encode($insert));
                // exec($cmd . ' > /dev/null 2>&1 &', $output, $return_var);
			// }
		}
		public function logs($dispatch,$action,$content,$account = null){
			$ip = $_SERVER['REMOTE_ADDR'];
		    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		        $ip = $_SERVER['HTTP_CLIENT_IP'];
		    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		    }
			$this->app->insert("logs",[
				"user" 		=> $account ?? $this->app->getSession('accounts')['id'],
				"dispatch" 	=> $dispatch,
				"action" 	=> $action,
				"date" 		=> date('Y-m-d H:i:s'),
				"url" 		=> 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"],
				"ip" 		=> $ip,
				"active"    => $this->active(),
				"browsers"	=> $_SERVER["HTTP_USER_AGENT"] ?? '',
	            "content"   => json_encode($content),
			]);
		}
		public function trash($router,$content,$data){
			$ip = $_SERVER['REMOTE_ADDR'];
		    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		        $ip = $_SERVER['HTTP_CLIENT_IP'];
		    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		    }
			$this->app->insert("trashs",[
				"account" 	=> $account ?? $this->app->getSession('accounts')['id'],
				"content" 		=> $content,
				"router"    => $router,
				"data"		=> json_encode($data),
				"date" 		=> date('Y-m-d H:i:s'),
				"url" 		=> 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"],
				"ip" 		=> $ip,
				"active"    => $this->active(),
			]);
		}
	    public function pages_ajax($count, $limit, $page, $class = null, $last = null) {
		    global $view, $lang, $router, $detect;
		    $total = ceil($count / $limit);
		    $return = null;
		    $url = $_SERVER['REQUEST_URI'];
		    $urlParts = parse_url($url);
		    parse_str($urlParts['query'] ?? '', $queryParams);
		    if ($page < $total) {
		        $queryParams['pg'] = $page + 1;
		    } else {
		        unset($queryParams['pg']);
		    }
		    $queryString = http_build_query($queryParams);
		    $return .= '<div class="'.$class.'">';
		    $return .= '<div class="pagination text-center w-100">';
		    $getlast = '';
		    
		    if ($last) {
		        $getlast = '&last='.$last;
		    }
		    
		    if ($total > 1) {
		        if ($page != $total) {
		            $return .= '<a href="'.$urlParts['path'].'?'.$queryString.$getlast.'" class="page-link next pjax-load btn border-0 bg-light text-dark mx-auto">Xem thêm</a>';
		        }
		    }
		    $return .= '</div>';
		    $return .= '</div>';
		    return $return;
		}
	    public function pages($count,$limit,$page,$name=null){
	        global $view,$lang,$router,$detect;
	        $total = ceil($count/$limit);
	        $return = null;
	        $getpage = null;
	        $name = $name==''?'&pg':$name;
	        $return .= '<ul class="pagination">';
	        if($total>1){
	            $url = $_SERVER['REQUEST_URI'];
	            if($_SERVER['QUERY_STRING']==''){
	                $view = $url.'?';
	            } else {
	                $view = '?'.$_SERVER['QUERY_STRING'].'';
	            }
	            $view = preg_replace("#(/?|&)".$name."=([0-9]{1,})#","",$view);
	            if($page!=1){
	            	$return .= '<li class="page-item mx-1"><a href="'.$view.$name.'=1" class="page-link rounded-3 bg-opacity-10 bg-secondary border-0" data-pjax >&laquo;&laquo;</a></li>';
	                $return .= '<li class="page-item mx-1 d-none d-md-block"><a href="'.$view.$name.'='.($page-1).'" class="page-link rounded-3 bg-opacity-10 bg-secondary border-0" data-pjax >&laquo;</a></li>';
	            }
	            for ($number=1; $number<=$total; $number++) { 
	                if($page>4 && $number==1 || $page<$total-1 && $number==$total){
	                    $return .= '<li class="page-item mx-1 d-none d-md-block"><a href="#" class="page-link rounded-3 bg-opacity-10 bg-secondary border-0 page-link-hide">...</a><li>';
	                }
	                if($number<$page+4 && $number>$page-4){
	                    $return .= '<li class="page-item mx-1"><a href="'.$view.$name.'='.$number.'" class="page-link rounded-3 bg-'.($page==$number?'primary text-light':'secondary bg-opacity-10').' border-0" data-pjax >'.$number.'</a></li>';
	                }
	                $getnumber = $number;
	            }
	            if($page!=$total){
	                $return .= '<li class="page-item mx-1 d-none d-md-block"><a href="'.$view.$name.'='.($page+1).'" class="page-link rounded-3 bg-opacity-10 bg-secondary border-0" data-pjax >&raquo;</a></li>';
	                $return .= '<li class="page-item mx-1"><a href="'.$view.$name.'='.$total.'" class="page-link rounded-3 bg-opacity-10 bg-secondary border-0" data-pjax >&raquo;&raquo;</a></li>';
	            }
	        }
	        $return .= '</ul>';
	        return $return;
	    }
	   	public function updateVersionInFile($filePath, $type, &$newVersion) {
		    $content = file_get_contents($filePath);

		    if ($type === 'js') {
		        $pattern = '/main\.bundle\.js\?=v(\d+)\.(\d+)/';
		    } else {
		        $pattern = '/style\.bundle\.css\?=v(\d+)\.(\d+)/';
		    }

		    $content = preg_replace_callback($pattern, function($matches) use ($type, &$newVersion) {
		        $major = (int)$matches[1];
		        $minor = (int)$matches[2];

		        if ($minor < 9) {
		            $minor++;
		        } else {
		            $minor = 0;
		            $major++;
		        }

		        $newVersion = "v{$major}.{$minor}";
		        $filename = ($type === 'js') ? 'main.bundle.js' : 'style.bundle.css';
		        return "{$filename}?={$newVersion}";
		    }, $content);

		    file_put_contents($filePath, $content);
		}
		public function minifyHtml($html) {
		    $html = preg_replace('/<!--(?!\[if).*?-->/s', '', $html);
		    $html = preg_replace('/\s+/', ' ', $html);
		    $html = preg_replace('/>\s+</', '><', $html);
		    $html = trim($html);
		    return $html;
		}

	}
?>