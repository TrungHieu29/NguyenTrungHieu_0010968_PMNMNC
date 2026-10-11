# StudentManagement

Bài tập Laravel quản lý lớp học, sinh viên và Menu. Dự án dùng Laravel 13, PHP, MySQL, Blade và Bootstrap.

## Chức năng chính

- Đăng ký, đăng nhập, đăng xuất; tài khoản mới mặc định là staff.
- Admin quản lý lớp học, sinh viên và Menu. Staff được xem, thêm, sửa lớp học và sinh viên nhưng không được xóa hoặc quản lý Menu.
- Dashboard hiển thị số lượng lớp học và sinh viên.
- Sidebar lấy mục từ bảng `menus`. Admin có thể thêm trang “đang phát triển” như `/pages/lich-thi` ngay trong Quản lý Menu.
- RESTful API cho lớp học, sinh viên và trang API Playground để thử API sau khi đăng nhập.

## Cài đặt

Cần PHP 8.3 trở lên, Composer và MySQL. Tạo database rỗng trước khi chạy migration.

```bash
composer install
```

Sao chép `.env.example` thành `.env`, sau đó sửa `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` cho đúng MySQL trên máy. Tiếp tục chạy:

```bash
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

Mở `http://127.0.0.1:8000`. Seeder tạo admin demo và dữ liệu mẫu khi bảng lớp/sinh viên còn trống; chạy lại không tạo trùng Menu mặc định. Nếu nâng cấp từ bản dùng email để đăng nhập, migration gán username `user<ID>` cho tài khoản cũ (ví dụ ID 7 thành `user7`); mật khẩu cũ được giữ nguyên.

## Admin demo

- Username: `admin`
- Mật khẩu: `123456`

Tài khoản này chỉ dùng để kiểm thử ở **local**. Seeder không tạo admin demo trên production. Để thử quyền staff, vào trang Đăng ký và tạo tài khoản mới. Mật khẩu đăng ký cần ít nhất 6 ký tự; email không bắt buộc.

## Thử API

Sau khi đăng nhập, mở **API Playground** ở Sidebar. Chọn endpoint, nhập ID hoặc query nếu cần, nhập JSON body cho lệnh ghi rồi nhấn **Execute**. Trang dùng session hiện tại và tự gửi CSRF token. Admin dùng được tất cả lệnh; staff không được xóa.

Với Postman, có thể dùng cùng session: gọi `GET /login`, giữ cookie và lấy `_token` trong form; gửi `POST /login` với `username`, `password`, `_token`. Sau khi đăng nhập, gọi `GET /api-playground` để lấy CSRF token mới trong thẻ meta. Giữ cookie phiên và gửi header `X-CSRF-TOKEN` cho POST, PUT, PATCH, DELETE; thêm `Accept: application/json` và `Content-Type: application/json` khi gửi JSON. Không cần Bearer token.

Chạy `php artisan test` để kiểm tra các luồng chính. Dự án không dùng `migrate:fresh` khi database đã có dữ liệu.
