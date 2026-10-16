<?php

namespace Tests\Feature;

use App\Models\LopHoc;
use App\Models\Menu;
use App\Models\SinhVien;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function userData(array $overrides = []): array
    {
        return array_replace([
            'name' => 'Nguyễn Văn An', 'username' => 'giangvienmoi', 'email' => 'giangvienmoi@example.test',
            'password' => 'Lecturer@123456', 'password_confirmation' => 'Lecturer@123456',
            'role' => 'lecturer', 'is_active' => '1',
        ], $overrides);
    }

    private function classData(array $overrides = []): array
    {
        return array_replace([
            'ma_lop' => 'CNTT01', 'ten_lop' => 'Công nghệ thông tin', 'si_so' => 30,
            'giao_vien' => 'Nguyễn Văn Bình', 'sdt_gv' => '0901234567', 'trang_thai' => '1',
        ], $overrides);
    }

    private function studentData(LopHoc $class, array $overrides = []): array
    {
        return array_replace([
            'ma_sinh_vien' => 'SV0001', 'ho_ten' => 'Trần Minh Anh', 'email' => 'sv0001@example.test',
            'ngay_sinh' => '2005-06-20', 'gioi_tinh' => 'Nữ', 'lop_hoc_id' => $class->id, 'trang_thai' => '1',
        ], $overrides);
    }

    public function test_registration_is_removed_and_guests_are_blocked(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', $this->userData())->assertNotFound();
        foreach (['dashboard', 'lop-hocs', 'sinh-viens', 'menus', 'users', 'profile', 'api-playground'] as $path) {
            $this->get('/'.$path)->assertRedirect('/login');
        }
        foreach (['lop-hocs', 'sinh-viens'] as $path) {
            $this->getJson('/api/'.$path)->assertUnauthorized()->assertJsonPath('success', false);
        }
    }

    public function test_admin_and_lecturer_login_logout_and_session_rotation(): void
    {
        foreach (['admin', 'lecturer'] as $role) {
            $user = User::factory()->create(['role' => $role, 'password' => 'Password@123']);
            $this->get('/login')->assertOk()->assertDontSee('Đăng ký');
            $this->post('/login', ['username' => $user->username, 'password' => 'wrong'])->assertSessionHasErrors('username');
            $sessionId = session()->getId();
            $this->post('/login', ['username' => $user->username, 'password' => 'Password@123'])->assertRedirect('/dashboard');
            $this->assertAuthenticatedAs($user);
            $this->assertNotSame($sessionId, session()->getId());
            $this->get('/dashboard')->assertOk();
            $this->get('/login')->assertRedirect('/dashboard');
            $this->post('/logout')->assertRedirect('/login');
            $this->assertGuest();
        }
    }

    public function test_locked_account_cannot_login_and_existing_session_is_revoked(): void
    {
        $user = User::factory()->create(['password' => 'Password@123', 'is_active' => false]);
        $this->post('/login', ['username' => $user->username, 'password' => 'Password@123'])->assertSessionHasErrors('username');
        $this->assertGuest();
        $user->is_active = true;
        $user->save();
        $this->actingAs($user)->get('/dashboard')->assertOk();
        User::whereKey($user->id)->update(['is_active' => false]);
        $this->get('/dashboard')->assertForbidden();
        $this->assertGuest();
        $this->actingAs($user)->getJson('/api/lop-hocs')->assertForbidden()->assertJsonPath('success', false);
    }

    public function test_login_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['username' => 'unknown', 'password' => 'wrong'])->assertSessionHasErrors();
        }
        $this->post('/login', ['username' => 'unknown', 'password' => 'wrong'])->assertStatus(429);
    }

    public function test_csrf_is_required_for_session_mutations(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->app['env'] = 'local';
        $this->postJson('/api/lop-hocs', $this->classData())->assertStatus(419);
        $this->withSession(['_token' => 'csrf-test-token'])->withHeader('X-CSRF-TOKEN', 'csrf-test-token')
            ->postJson('/api/lop-hocs', $this->classData())->assertCreated();
    }

    public function test_lecturer_cannot_access_any_user_or_menu_management_route(): void
    {
        $lecturer = User::factory()->create();
        $target = User::factory()->create();
        $menu = Menu::create(['ten_hien_thi' => 'Lớp', 'slug' => 'classes', 'url' => '/lop-hocs', 'vi_tri' => 'sidebar', 'thu_tu' => 1, 'trang_thai' => true]);
        $this->actingAs($lecturer);
        foreach (['users' => $target->id, 'menus' => $menu->id] as $resource => $id) {
            foreach (['', '/create', '/'.$id, '/'.$id.'/edit'] as $suffix) {
                $this->get('/'.$resource.$suffix)->assertForbidden();
            }
            $this->post('/'.$resource, [])->assertForbidden();
            $this->put('/'.$resource.'/'.$id, [])->assertForbidden();
            $this->delete('/'.$resource.'/'.$id)->assertForbidden();
        }
        $lecturer->role = 'unknown';
        $lecturer->save();
        $this->getJson('/api/lop-hocs')->assertForbidden();
    }

    public function test_admin_can_create_view_edit_reset_password_lock_unlock_and_delete_user(): void
    {
        $this->actingAs($this->admin());
        $this->get('/users/create')->assertOk();
        $this->post('/users', $this->userData())->assertRedirect('/users');
        $user = User::where('username', 'giangvienmoi')->firstOrFail();
        $this->assertSame('lecturer', $user->role);
        $this->assertTrue(Hash::check('Lecturer@123456', $user->password));
        $this->get('/users/'.$user->id)->assertOk();
        $this->get('/users/'.$user->id.'/edit')->assertOk();
        $hash = $user->password;
        $this->put('/users/'.$user->id, $this->userData(['name' => 'Tên mới', 'password' => '', 'password_confirmation' => '', 'is_active' => '0']))->assertRedirect('/users');
        $this->assertSame($hash, $user->refresh()->password);
        $this->assertFalse($user->is_active);
        $this->put('/users/'.$user->id, $this->userData(['password' => 'NewPassword@123', 'password_confirmation' => 'NewPassword@123', 'role' => 'admin']))->assertRedirect('/users');
        $this->assertTrue($user->refresh()->is_active);
        $this->assertSame('admin', $user->role);
        $this->assertTrue(Hash::check('NewPassword@123', $user->password));
        $this->delete('/users/'.$user->id)->assertRedirect('/users');
        $this->assertModelMissing($user);
    }

    public function test_user_validation_and_last_active_admin_protection(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $this->post('/users', [])->assertSessionHasErrors(['name', 'username', 'email', 'password', 'role', 'is_active']);
        $this->post('/users', $this->userData(['username' => $admin->username, 'email' => $admin->email, 'password_confirmation' => 'wrong']))->assertSessionHasErrors(['username', 'email', 'password']);
        $data = $this->userData(['username' => $admin->username, 'email' => $admin->email, 'password' => '', 'password_confirmation' => '']);
        $this->put('/users/'.$admin->id, $data)->assertSessionHasErrors('role');
        $this->put('/users/'.$admin->id, array_replace($data, ['role' => 'admin', 'is_active' => 0]))->assertSessionHasErrors('role');
        $this->delete('/users/'.$admin->id)->assertSessionHasErrors('user');
        User::factory()->create(['role' => 'admin', 'is_active' => false]);
        $this->delete('/users/'.$admin->id)->assertSessionHasErrors('user');
        $this->assertTrue($admin->refresh()->is_active);
        $this->assertSame('admin', $admin->role);
    }

    public function test_profile_updates_only_own_details_and_requires_current_password(): void
    {
        $user = User::factory()->create(['password' => 'OldPassword@123']);
        $other = User::factory()->create();
        $this->actingAs($user)->get('/profile')->assertOk();
        $data = ['name' => 'Tên hồ sơ', 'username' => $user->username, 'email' => $user->email];
        $this->put('/profile', [...$data, 'role' => 'admin', 'is_active' => 0])->assertSessionHasErrors(['role', 'is_active']);
        $this->put('/profile', [...$data, 'username' => $other->username, 'email' => $other->email])->assertSessionHasErrors(['username', 'email']);
        $this->put('/profile', $data)->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('Tên hồ sơ', $user->refresh()->name);
        $this->assertSame('lecturer', $user->role);
        $password = ['current_password' => 'wrong', 'password' => 'NewPassword@123', 'password_confirmation' => 'NewPassword@123'];
        $this->put('/profile/password', $password)->assertSessionHasErrors('current_password');
        $this->put('/profile/password', array_replace($password, ['current_password' => 'OldPassword@123', 'password_confirmation' => 'wrong']))->assertSessionHasErrors('password');
        $this->put('/profile/password', array_replace($password, ['current_password' => 'OldPassword@123']))->assertRedirect();
        $this->assertTrue(Hash::check('NewPassword@123', $user->refresh()->password));
    }

    public function test_lecturer_web_class_and_student_crud_and_soft_deleted_class_relation(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get('/lop-hocs/create')->assertOk();
        $this->post('/lop-hocs', $this->classData())->assertRedirect('/lop-hocs');
        $class = LopHoc::where('ma_lop', 'CNTT01')->firstOrFail();
        $this->get('/lop-hocs/'.$class->id)->assertOk();
        $this->get('/lop-hocs/'.$class->id.'/edit')->assertOk();
        $this->put('/lop-hocs/'.$class->id, $this->classData(['si_so' => 0, 'trang_thai' => 0]))->assertRedirect('/lop-hocs');
        $this->assertFalse($class->refresh()->trang_thai);
        $this->get('/sinh-viens/create')->assertOk();
        $this->post('/sinh-viens', $this->studentData($class))->assertRedirect('/sinh-viens');
        $student = SinhVien::where('ma_sinh_vien', 'SV0001')->firstOrFail();
        $this->get('/sinh-viens/'.$student->id)->assertOk();
        $this->get('/sinh-viens/'.$student->id.'/edit')->assertOk();
        $this->put('/sinh-viens/'.$student->id, $this->studentData($class, ['ho_ten' => 'Tên mới']))->assertRedirect('/sinh-viens');
        $this->delete('/lop-hocs/'.$class->id)->assertRedirect('/lop-hocs');
        $this->assertSoftDeleted($class);
        $this->get('/sinh-viens/'.$student->id)->assertOk()->assertSee('Lớp đã xóa');
        $this->get('/sinh-viens/'.$student->id.'/edit')->assertOk()->assertSee('đã xóa');
        $this->put('/sinh-viens/'.$student->id, $this->studentData($class))->assertRedirect('/sinh-viens');
        $this->post('/sinh-viens', $this->studentData($class, ['ma_sinh_vien' => 'SV0002', 'email' => 'another@example.test']))->assertSessionHasErrors('lop_hoc_id');
        $this->delete('/sinh-viens/'.$student->id)->assertRedirect('/sinh-viens');
        $this->assertSoftDeleted($student);
        $this->get('/sinh-viens/'.$student->id)->assertNotFound();
    }

    public function test_class_and_student_validation_unique_values_and_inline_errors(): void
    {
        $this->actingAs($this->admin());
        $this->post('/lop-hocs', [])->assertSessionHasErrors(['ma_lop', 'ten_lop', 'si_so', 'giao_vien']);
        $this->post('/lop-hocs', $this->classData(['si_so' => -1]))->assertSessionHasErrors('si_so');
        $class = LopHoc::factory()->create();
        $this->post('/lop-hocs', $this->classData(['ma_lop' => $class->ma_lop]))->assertSessionHasErrors('ma_lop');
        $this->post('/sinh-viens', [])->assertSessionHasErrors(['ma_sinh_vien', 'ho_ten', 'email', 'lop_hoc_id']);
        $this->post('/sinh-viens', $this->studentData($class, ['email' => 'wrong', 'ngay_sinh' => '2999-01-01']))->assertSessionHasErrors(['email', 'ngay_sinh']);
        $student = SinhVien::factory()->create(['lop_hoc_id' => $class->id]);
        $this->post('/sinh-viens', $this->studentData($class, ['ma_sinh_vien' => $student->ma_sinh_vien, 'email' => $student->email]))->assertSessionHasErrors(['ma_sinh_vien', 'email']);
        $this->from('/lop-hocs/create')->followingRedirects()->post('/lop-hocs', $this->classData(['ma_lop' => $class->ma_lop]))
            ->assertOk()->assertSee($class->ma_lop)->assertSee('invalid-feedback')->assertSee('mã lớp đã được sử dụng.');
    }

    public function test_menu_crud_safe_links_and_database_navigation_updates(): void
    {
        $this->seed(MenuSeeder::class);
        $this->actingAs($this->admin());
        $this->get('/menus/create')->assertOk();
        $data = ['ten_hien_thi' => 'Lịch thi', 'slug' => 'lich-thi', 'loai_lien_ket' => 'page', 'vi_tri' => 'sidebar', 'thu_tu' => 0, 'trang_thai' => 1];
        $this->post('/menus', $data)->assertRedirect('/menus');
        $menu = Menu::where('slug', 'lich-thi')->firstOrFail();
        $this->get('/menus/'.$menu->id)->assertOk();
        $this->get('/menus/'.$menu->id.'/edit')->assertOk();
        $this->get('/dashboard')->assertOk()->assertSee('href="/pages/lich-thi"', false);
        $this->get('/pages/lich-thi')->assertOk();
        $this->put('/menus/'.$menu->id, [...$data, 'vi_tri' => 'header', 'thu_tu' => 9])->assertRedirect('/menus');
        $this->assertSame('header', $menu->refresh()->vi_tri);
        $this->put('/menus/'.$menu->id, [...$data, 'trang_thai' => 0])->assertRedirect('/menus');
        $this->get('/dashboard')->assertDontSee('href="/pages/lich-thi"', false);
        $this->get('/pages/lich-thi')->assertNotFound();
        $this->post('/menus', [...$data, 'slug' => 'unsafe', 'loai_lien_ket' => 'existing', 'url' => 'javascript:alert(1)'])->assertSessionHasErrors('url');
        $this->post('/menus', [...$data, 'slug' => 'profile'])->assertSessionHasErrors('slug');
        $this->post('/menus', $data)->assertSessionHasErrors('slug');
        $this->delete('/menus/'.$menu->id)->assertRedirect('/menus');
        $this->assertModelMissing($menu);
        $this->actingAs(User::factory()->create())->get('/dashboard')->assertDontSee('href="/menus"', false)->assertDontSee('href="/users"', false)->assertSee('href="/profile"', false);
    }

    public function test_seeding_preserves_existing_records_passwords_and_hidden_menus(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(10, LopHoc::count());
        $this->assertSame(50, SinhVien::count());
        $this->assertSame(7, Menu::count());
        $this->assertSame(3, User::count());
        $admin = User::where('username', 'admin')->firstOrFail();
        $this->assertTrue(Hash::check('Admin@123456', $admin->password));
        $this->assertTrue(Hash::check('Lecturer@123456', User::where('username', 'lecturer')->firstOrFail()->password));
        $admin->update(['name' => 'Tên đã đổi', 'password' => 'Changed@123']);
        Menu::where('slug', 'lop-hocs')->update(['trang_thai' => false]);
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(10, LopHoc::count());
        $this->assertSame(50, SinhVien::count());
        $this->assertSame(7, Menu::count());
        $this->assertSame(3, User::count());
        $this->assertSame('Tên đã đổi', $admin->refresh()->name);
        $this->assertTrue(Hash::check('Changed@123', $admin->password));
        $this->assertFalse(Menu::where('slug', 'lop-hocs')->firstOrFail()->trang_thai);
    }

    public function test_all_list_filters_sorting_pagination_and_invalid_queries(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $class = LopHoc::factory()->create(['ten_lop' => 'AAA', 'giao_vien' => 'Unique Teacher', 'trang_thai' => true, 'si_so' => 25]);
        LopHoc::factory()->count(11)->create(['giao_vien' => 'Unique Teacher', 'trang_thai' => true, 'si_so' => 25]);
        $this->get('/lop-hocs?search=Unique%20Teacher&trang_thai=1&si_so_min=20&si_so_max=30&sort=ten_lop&direction=asc&perPage=10')
            ->assertOk()->assertViewHas('lopHocs', fn ($rows) => $rows->total() === 12 && $rows->first()->id === $class->id && str_contains($rows->nextPageUrl(), 'sort=ten_lop'));
        $student = SinhVien::factory()->create(['lop_hoc_id' => $class->id, 'email' => 'unique-student@example.test', 'trang_thai' => true]);
        $this->get('/sinh-viens?search=unique-student&lop_hoc_id='.$class->id.'&trang_thai=1&sort=ho_ten&direction=asc&perPage=20')->assertOk()->assertViewHas('sinhViens', fn ($rows) => $rows->total() === 1 && $rows->first()->id === $student->id && $rows->perPage() === 20);
        $this->seed(MenuSeeder::class);
        $this->get('/menus?search=User&vi_tri=sidebar&trang_thai=1&sort=ten_hien_thi&direction=asc&perPage=50')->assertOk()->assertViewHas('menus', fn ($rows) => $rows->total() === 1 && $rows->perPage() === 50);
        $this->get('/users?search='.$admin->username.'&role=admin&is_active=1&sort=username&direction=asc&perPage=20')->assertOk()->assertViewHas('users', fn ($rows) => $rows->total() === 1);
        foreach (['lop-hocs', 'sinh-viens', 'menus', 'users'] as $path) {
            $this->get('/'.$path.'?sort=password&direction=wrong&perPage=999&page=-1')->assertSessionHasErrors(['sort', 'direction', 'perPage', 'page']);
        }
        $this->get('/lop-hocs?si_so_min=50&si_so_max=10')->assertSessionHasErrors('si_so_max');
    }

    public function test_api_crud_all_methods_validation_and_soft_deletes_for_both_roles(): void
    {
        foreach (['admin', 'lecturer'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]));
            $classData = $this->classData(['ma_lop' => 'API-'.$role]);
            $classId = $this->postJson('/api/lop-hocs', $classData)->assertCreated()->assertJsonPath('success', true)->json('data.id');
            $this->getJson('/api/lop-hocs/'.$classId)->assertOk();
            $this->putJson('/api/lop-hocs/'.$classId, [...$classData, 'si_so' => 50])->assertOk()->assertJsonPath('data.si_so', 50);
            $this->patchJson('/api/lop-hocs/'.$classId, ['trang_thai' => false])->assertOk()->assertJsonPath('data.trang_thai', false);
            $this->patchJson('/api/lop-hocs/'.$classId, ['ma_lop' => null])->assertUnprocessable();
            $class = LopHoc::findOrFail($classId);
            $studentData = $this->studentData($class, ['ma_sinh_vien' => 'API-'.$role, 'email' => $role.'@student.test']);
            $studentId = $this->postJson('/api/sinh-viens', $studentData)->assertCreated()->json('data.id');
            $this->getJson('/api/sinh-viens/'.$studentId)->assertOk()->assertJsonPath('data.lop_hoc.id', $classId);
            $this->putJson('/api/sinh-viens/'.$studentId, $studentData)->assertOk();
            $this->patchJson('/api/sinh-viens/'.$studentId, ['ho_ten' => 'Tên API mới'])->assertOk()->assertJsonPath('data.ho_ten', 'Tên API mới');
            $this->deleteJson('/api/lop-hocs/'.$classId)->assertOk();
            $this->getJson('/api/sinh-viens/'.$studentId)->assertOk()->assertJsonPath('data.lop_hoc.id', $classId);
            $this->patchJson('/api/sinh-viens/'.$studentId, ['ho_ten' => 'Sau xóa lớp'])->assertOk();
            $this->deleteJson('/api/sinh-viens/'.$studentId)->assertOk();
            $this->getJson('/api/sinh-viens/'.$studentId)->assertNotFound()->assertJsonPath('success', false);
            $this->assertSoftDeleted('lop_hocs', ['id' => $classId]);
            $this->assertSoftDeleted('sinh_viens', ['id' => $studentId]);
        }
    }

    public function test_api_search_filters_sort_pagination_and_error_envelopes(): void
    {
        $this->actingAs(User::factory()->create());
        $class = LopHoc::factory()->create(['giao_vien' => 'Find Teacher', 'si_so' => 35, 'trang_thai' => true]);
        $this->getJson('/api/lop-hocs?search=Find%20Teacher&si_so_min=30&si_so_max=40&sort=ma_lop&direction=asc&per_page=20')->assertOk()->assertJsonPath('data.total', 1)->assertJsonPath('data.per_page', 20);
        $student = SinhVien::factory()->create(['lop_hoc_id' => $class->id, 'email' => 'email-search@example.test', 'trang_thai' => false]);
        $this->getJson('/api/sinh-viens?search=email-search&lop_hoc_id='.$class->id.'&trang_thai=0&sort=ho_ten&direction=asc&per_page=50')->assertOk()->assertJsonPath('data.total', 1)->assertJsonPath('data.data.0.id', $student->id);
        foreach (['lop-hocs', 'sinh-viens'] as $resource) {
            $this->getJson('/api/'.$resource.'?sort=password&direction=bad&per_page=999')->assertUnprocessable()->assertJsonPath('success', false)->assertJsonValidationErrors(['sort', 'direction', 'per_page']);
            $this->postJson('/api/'.$resource, [])->assertUnprocessable()->assertJsonPath('success', false);
            $this->getJson('/api/'.$resource.'/99999999')->assertNotFound()->assertJsonPath('success', false);
        }
        $this->get('/api-playground')->assertOk()->assertSee('X-CSRF-TOKEN')->assertSee('PATCH');
    }

    public function test_dashboard_statistics_come_from_database_and_user_totals_are_admin_only(): void
    {
        $admin = $this->admin();
        $lecturer = User::factory()->create();
        LopHoc::factory()->create(['trang_thai' => true]);
        $class = LopHoc::factory()->create(['trang_thai' => false]);
        SinhVien::factory()->create(['lop_hoc_id' => $class->id, 'trang_thai' => true]);
        $this->actingAs($admin)->get('/dashboard')->assertOk()->assertViewHas('lopHocCount', 2)->assertViewHas('activeLopHocCount', 1)->assertViewHas('sinhVienCount', 1)->assertViewHas('userCount', 2)->assertViewHas('lecturerCount', 1);
        $this->actingAs($lecturer)->get('/dashboard')->assertOk()->assertViewHas('userCount', null)->assertDontSee('Tổng tài khoản');
    }
}
