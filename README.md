# StudentManagement

Ứng dụng quản lý lớp học, sinh viên, Menu, User và hồ sơ cá nhân.
Sử dụng Laravel 13, PHP 8.3+, MySQL, Blade và Bootstrap 5.

## 1. Cài đặt và chạy lần đầu

Máy cần có PHP 8.3+ (bật extension `pdo_mysql`), Composer và MySQL đang chạy.
Mở terminal tại thư mục có file `artisan` rồi chạy:

```bash
composer install
```

Nếu chưa có `.env`, sao chép `.env.example` và đổi tên bản sao thành `.env`.
Tạo database bằng phpMyAdmin hoặc MySQL:

```sql
CREATE DATABASE student_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Sửa kết nối trong `.env` theo MySQL trên máy:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=student_management
DB_USERNAME=root
DB_PASSWORD=
```

Sau đó chạy:

```bash
php artisan config:clear
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Mở địa chỉ được terminal thông báo, mặc định là http://127.0.0.1:8000.
Không cần chạy npm để sử dụng giao diện. Bootstrap và icon được tải qua CDN nên cần Internet để hiển thị đầy đủ.

## 2. Đăng nhập và kiểm tra chức năng

Với database mới, seeder tạo các tài khoản:

| Vai trò | Tên đăng nhập | Mật khẩu |
|---|---|---|
| Admin | admin | Admin@123456 |
| Giảng viên | lecturer | Lecturer@123456 |
| Giảng viên | lecturer2 | Lecturer@123456 |

Seeder tạo khoảng 10 lớp, 50 sinh viên và các Menu. Chạy lại không ghi đè thông tin hoặc mật khẩu tài khoản đã tồn tại. Nếu dùng database cũ, đăng nhập bằng mật khẩu cũ.

- Admin: quản lý lớp học, sinh viên, Menu, User và tài khoản cá nhân.
- Giảng viên: xem/thêm/sửa/xóa mềm lớp học, sinh viên; cập nhật hồ sơ và đổi mật khẩu bản thân.
- Giảng viên không được quản lý Menu hoặc User. Không có đăng ký tài khoản công khai.
- Sau khi đăng nhập, mở **API Playground** để thử API `/api/lop-hocs` và `/api/sinh-viens`. API dùng session và CSRF, không cần Bearer token.

Những lần chạy sau chỉ cần bật MySQL và chạy `php artisan serve`. Không dùng `migrate:fresh` với database đang có dữ liệu.

## 3. Chạy test tự động (không bắt buộc)

Bộ test chức năng nằm trong `tests/Feature/StudentManagementTest.php`.
Test dùng MySQL riêng, không sử dụng database chính. Tạo database test:

```sql
CREATE DATABASE student_management_codex_test_20261015 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Trong PowerShell tại thư mục dự án, tạo bảng cho database test rồi chạy test:

```powershell
$env:DB_DATABASE = 'student_management_codex_test_20261015'
php artisan migrate
Remove-Item Env:DB_DATABASE
php artisan test
```

Thông tin kết nối MySQL dùng chung với `.env`; tên database test được cố định trong `phpunit.xml`. Các bài test dùng transaction và rollback dữ liệu sau mỗi bài.
