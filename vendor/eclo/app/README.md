# ECLO Framework (eclo/app)

**ECLO App** là một micro-framework library được viết bằng PHP, cung cấp một bộ công cụ mạnh mẽ để xây dựng các ứng dụng web một cách nhanh chóng. Thư viện quản lý các tác vụ cốt lõi như định tuyến (routing), middleware, tương tác cơ sở dữ liệu, bảo mật, và nhiều tiện ích khác trong một lớp `App` duy nhất.

## Yêu cầu

  * PHP \>= 8.1

## Cài đặt

Sử dụng [Composer](https://getcomposer.org/) để cài đặt:

```bash
composer require eclo/app
```

## Khởi tạo

Đây là một ví dụ "Hello World" đơn giản để bắt đầu.

```php
// index.php
require 'vendor/autoload.php';

// Khởi tạo ứng dụng
$app = new ECLO\App([
    'database_type' => 'mysql',
    'database_name' => 'ten_database',
    'server' => 'localhost',
    'username' => 'root',
    'password' => ''
]);

// Định nghĩa một route cho trang chủ
$app->router('/', 'GET', function() {
    echo 'Hello, World!';
});

// Chạy ứng dụng
$app->run();
```

-----

## Định tuyến (Routing)

Hệ thống routing cho phép bạn ánh xạ các URL tới các hàm hoặc phương thức xử lý.

### Định tuyến cơ bản

Sử dụng phương thức `router()` để định nghĩa một route.

```php
// Route cho phương thức GET
$app->router('/about', 'GET', function() {
    echo 'Đây là trang giới thiệu.';
});

// Route cho phương thức POST
$app->router('/contact', 'POST', function() {
    // Xử lý dữ liệu form
});

// Route cho nhiều phương thức
$app->router('/news', ['GET', 'POST'], function() {
    // ...
});
```

### Route với tham số

Bạn có thể định nghĩa các tham số trong URL bằng cách đặt chúng trong dấu ngoặc nhọn `{}`.

```php
$app->router('/users/{id}', 'GET', function($params) {
    $userId = $params['id'];
    echo "Thông tin người dùng có ID: " . $app->xss($userId);
});
```

### Nhóm Route (Group)

Nhóm các route có cùng một tiền tố chung.

```php
$app->group('/admin', function($app) {
    
    $app->router('', 'GET', function() {
        echo 'Trang chủ Admin'; // URL: /admin
    });

    $app->router('/products', 'GET', function() {
        echo 'Quản lý sản phẩm'; // URL: /admin/products
    });

    // Group lồng nhau
    $app->group('/settings', function($app) {
        $app->router('/general', 'GET', function() {
            echo 'Cài đặt chung'; // URL: /admin/settings/general
        });
    });
});
```

### Route xử lý lỗi

Bạn có thể định nghĩa một route đặc biệt để xử lý các lỗi 404 hoặc lỗi truy cập trong một group.

```php
// Định nghĩa trang lỗi 404 chung cho toàn bộ trang web
$app->router('::404', 'GET', function($params) use ($app) {
    echo $app->render('views/errors/404.php', ['path' => $params['path']]);
});

// Định nghĩa trang lỗi 500 chung
$app->router('::500', 'GET', function() use ($app) {
    echo $app->render('views/errors/500.php');
});

// Tạo một group cho khu vực admin
$app->group('/admin', function($app) {
    
    // Các route admin bình thường
    $app->router('/dashboard', 'GET', function() { echo 'Admin Dashboard'; });

    // Ghi đè lại trang lỗi 404 chỉ riêng cho khu vực /admin
    $app->router('::404', 'GET', function() use ($app) {
        echo $app->render('views/admin/error_404.php');
    });

    // Định nghĩa trang lỗi 403 (cấm truy cập) cho khu vực admin
    $app->router('::403', 'GET', function() use ($app) {
        echo '<h1>ACCESS DENIED FOR ADMIN AREA</h1>';
    });
});
```
Kích hoạt lỗi một cách chủ động
```php
$app->router('/critical-operation', 'GET', function() use ($app) {
    try {
        // Một thao tác có thể gây lỗi nghiêm trọng
        $result = $app->get('some_table', '*');
        if (!$result) {
            // Giả sử đây là một lỗi không mong muốn
            throw new Exception("Cannot get data.");
        }
        echo "Operation successful!";
    } catch (Exception $e) {
        // Ghi log lỗi
        // error_log($e->getMessage());
        
        // Hiển thị trang lỗi 500 cho người dùng
        $app->triggerError(500);
    }
});
$app->router('/admin/secret-data', 'GET', function() use ($app) {
    $userRole = $app->getSession('role'); // Giả sử 'guest'
    
    if ($userRole !== 'admin') {
        // Kích hoạt trang lỗi 403 (Forbidden)
        $app->triggerError(403);
    }
    
    echo "Here is the secret data.";
})->middleware('auth');
```
-----

## Middleware

Middleware cho phép bạn thực thi một logic nào đó (ví dụ: kiểm tra xác thực) trước khi request được xử lý bởi route.

### Đăng ký Middleware

Sử dụng `setMiddleware()` để đăng ký. Hàm callback của middleware phải trả về `true` để tiếp tục, hoặc `false` để dừng lại.

```php
// Đăng ký middleware kiểm tra đăng nhập
$app->setMiddleware('auth', function() use ($app) {
    if (!$app->getSession('user_id')) {
        $app->redirect('/login');
        return false; // Dừng xử lý
    }
    return true; // Cho phép tiếp tục
});
```

### Áp dụng Middleware

Sử dụng phương thức `middleware()` ngay sau khi định nghĩa route hoặc group.

```php
// Áp dụng cho một route
$app->router('/dashboard', 'GET', function() { ... })->middleware('auth');

// Áp dụng cho cả một group
$app->group('/admin', function($app) {
    // Tất cả các route trong này sẽ được bảo vệ bởi middleware 'auth'
    $app->router('/posts', 'GET', function() { ... });
    $app->router('/users', 'GET', function() { ... });
})->middleware('auth');
```

-----

## Tương tác Cơ sở dữ liệu (Medoo)

ECLO App tích hợp sẵn [Medoo](https://medoo.in/) để làm việc với CSDL. Bạn có thể gọi các phương thức của Medoo trực tiếp từ đối tượng `$app`.

```php
// Lấy tất cả người dùng
$users = $app->select('users', '*');

// Lấy một người dùng theo điều kiện
$user = $app->get('users', '*', ['id' => 1]);

// Chèn dữ liệu
$app->insert('users', [
    'username' => 'eclo',
    'email' => 'info@eclo.vn'
]);

// Cập nhật
$app->update('users', ['email' => 'new.email@eclo.vn'], ['id' => 1]);

// Xóa
$app->delete('users', ['id' => 1]);

// Sử dụng cú pháp tĩnh
$count = ECLO\App::table('users')->count();
```

Để biết thêm các phương thức truy vấn, vui lòng tham khảo [tài liệu của Medoo](https://medoo.in/api/where).

-----

## Bảo mật

### Lọc XSS

Sử dụng phương thức `xss()` để làm sạch dữ liệu đầu vào từ người dùng.

```php
/**
 * @param string      $string        Chuỗi cần làm sạch.
 * @param bool        $allowHtml     Mặc định là false (loại bỏ toàn bộ HTML). Đặt là `true` để cho phép HTML an toàn.
 * @param array|null  $customConfig  Mảng cấu hình HTMLPurifier tùy chỉnh.
 */
public function xss($string, $allowHtml = false, $customConfig = null)
```

**Ví dụ:**

```php
// 1. Mặc định: Loại bỏ tất cả HTML (an toàn nhất)
$username = "<h1>Admin</h1>";
$safeUsername = $app->xss($username); // Kết quả: "&lt;h1&gt;Admin&lt;/h1&gt;"

// 2. Cho phép HTML: Sử dụng cấu hình mặc định trong __construct
$comment = "<p onclick='alert(1)'>Nội dung <b>an toàn</b></p>";
$safeComment = $app->xss($comment, true); // Kết quả: "<p>Nội dung <b>an toàn</b></p>"

// 3. Cho phép HTML với cấu hình tùy chỉnh
$articleContent = '<h2>Bài viết</h2>';
$config = ['HTML.Allowed' => 'h2,p'];
$safeArticle = $app->xss($articleContent, true, $config); // Kết quả: "<h2>Bài viết</h2>"
```

### JSON Web Tokens (JWT)

Dễ dàng tạo và xác thực JWT.

```php
// Cấu hình key bí mật một lần
$app->JWT('your-secret-key');

// Tạo token
$payload = [
    'iss' => 'https://eclo.vn',
    'aud' => 'https://eclo.vn',
    'iat' => time(),
    'exp' => time() + 3600, // Hết hạn sau 1 giờ
    'user_id' => 123
];
$token = $app->addJWT($payload);

// Giải mã và xác thực token
$decoded = $app->decodeJWT($token);
if ($decoded) {
    echo "Xin chào user " . $decoded->user_id;
} else {
    echo "Token không hợp lệ hoặc đã hết hạn.";
}
```

### Giới hạn truy cập (Rate Limiting)

Chống brute-force bằng cách giới hạn số lần request từ một IP.

```php
// Giới hạn việc đăng nhập: 5 lần mỗi 60 giây
$app->rateLimit('login_attempt', 5, 60);

// Nếu vượt quá, ứng dụng sẽ tự động dừng và trả về lỗi 429
```

-----

## Views & Templates

### Render một View

Sử dụng `render()` để hiển thị một file view và truyền dữ liệu vào nó.

```php
// Trong route của bạn
$app->router('/profile', 'GET', function() use ($app) {
    $data = [
        'title' => 'Trang cá nhân',
        'user' => ['name' => 'ECLO']
    ];
    // Sẽ render file views/profile.php
    echo $app->render('views/profile.php', $data);
});

// Trong views/profile.php
// <h1><?php echo $title; ?></h1>
// <p>Xin chào, <?php echo $user['name']; ?></p>
```

### Sử dụng Layout chung

Bạn có thể định nghĩa một file layout chung để bao bọc các view.

```php
// Thiết lập file layout
$app->setGlobalFile('views/layouts/main.php');

// Trong views/layouts/main.php
// <html>
// <head><title><?php echo $title ?? 'Trang web'; ?></title></head>
// <body>
//   <header>...</header>
//   <?php include $templatePath; // Dòng này sẽ nạp view con ?>
//   <footer>...</footer>
// </body>
// </html>
```

### Components

Tạo các thành phần view có thể tái sử dụng.

```php
// Đăng ký component
$app->setComponent('userCard', function($vars) {
    $user = $vars['user'];
    echo "<div class='card'><h3>{$user['name']}</h3><p>{$user['email']}</p></div>";
});

// Render component trong view
// echo $app->component('userCard', ['user' => ['name' => 'ECLO', 'email' => 'info@eclo.vn']]);
```

-----

## Tiện ích khác

### Gửi Email (PHPMailer)

```php
$mailConfig = [
    'host' => 'smtp.example.com',
    'username' => 'user@example.com',
    'password' => 'password',
    'encryption' => 'tls', // tls hoặc ssl
    'port' => 587,
    'from_email' => 'noreply@example.com',
    'from_name' => 'ECLO App'
];

try {
    $mailer = $app->Mail($mailConfig);
    $mailer->addAddress('recipient@example.net', 'Joe User');
    $mailer->isHTML(true);
    $mailer->Subject = 'Here is the subject';
    $mailer->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mailer->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mailer->ErrorInfo}";
}
```

### Tải lên file (Upload)

```php
if (!empty($_FILES['my_file'])) {
    $handle = $app->upload($_FILES['my_file']);
    if ($handle->uploaded) {
        $handle->process('/path/to/save/');
        if ($handle->processed) {
            echo 'File uploaded: ' . $handle->file_dst_name;
            $handle->clean();
        } else {
            echo 'Error: ' . $handle->error;
        }
    }
}
```

### Nén file (Minify)

```php
// Nén CSS
$app->minifyCSS('path/to/style.css', 'path/to/style.min.css');

// Nén JS
$app->minifyJS(['path/to/script1.js', 'path/to/script2.js'], 'path/to/all.min.js');
```

### Session và Cookie

```php
// Session
$app->setSession('user_id', 123);
$userId = $app->getSession('user_id');
$app->deleteSession('user_id');

// Cookie
$app->setCookie('remember_me', 'some_token', time() + (86400 * 30)); // 30 ngày
$token = $app->getCookie('remember_me');
$app->deleteCookie('remember_me');
```

### Điều hướng (Redirect)

```php
// Chuyển hướng đến URL cụ thể
$app->redirect('/login');

// Quay lại trang trước đó
$app->back();
```

-----

## Bản quyền

Thư viện này được phát hành dưới giấy phép **MIT**. Xem file `LICENSE` để biết thêm chi tiết.
