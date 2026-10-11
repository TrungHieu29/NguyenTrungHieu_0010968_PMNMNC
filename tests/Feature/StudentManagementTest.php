<?php

namespace Tests\Feature;

use App\Models\LopHoc;
use App\Models\Menu;
use App\Models\SinhVien;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_is_always_staff_and_validated(): void
    {
        $this->post('/register', [
            'name' => 'New Staff', 'username' => 'newstaff',
            'password' => '123456', 'password_confirmation' => '123456', 'role' => 'admin',
        ])->assertRedirect('/dashboard');
        $user = User::where('username', 'newstaff')->firstOrFail();
        $this->assertSame('staff', $user->role);
        $this->assertNull($user->email);
        $this->assertTrue(Hash::check('123456', $user->password));

        auth()->logout();
        $this->post('/register', [
            'name' => 'Duplicate', 'username' => 'newstaff',
            'password' => '123456', 'password_confirmation' => '123456',
        ])->assertSessionHasErrors('username');
        $this->post('/register', [
            'name' => 'Mismatch', 'username' => 'secondstaff',
            'password' => '123456', 'password_confirmation' => '654321',
        ])->assertSessionHasErrors('password');
    }

    public function test_login_logout_and_guest_protection(): void
    {
        $user = User::factory()->create(['password' => '123456']);
        $this->get('/dashboard')->assertRedirect('/login');
        $this->post('/login', ['username' => $user->username, 'password' => 'wrong'])->assertSessionHasErrors('username');
        $this->post('/login', ['username' => $user->username, 'password' => '123456'])->assertRedirect('/dashboard');
        $this->get('/login')->assertRedirect('/dashboard');
        $this->post('/logout')->assertRedirect('/login');
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_api_playground_uses_the_current_session(): void
    {
        $this->get('/api-playground')->assertRedirect('/login');
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->get('/api-playground')->assertOk()->assertSee('X-CSRF-TOKEN');
        $this->getJson('/api/lop-hocs')->assertOk();
    }

    public function test_staff_permissions_and_web_sort_route(): void
    {
        $staff = User::factory()->create();
        $class = LopHoc::factory()->create();
        $student = SinhVien::factory()->create(['lop_hoc_id' => $class->id]);
        $this->actingAs($staff);
        $this->get('/lop-hocs?direction=asc&search='.$class->ma_lop)->assertOk()->assertSee($class->ma_lop);
        $this->assertSame('/lop-hocs?direction=asc', route('lop-hocs.index', ['direction' => 'asc'], false));
        $this->delete(route('lop-hocs.destroy', $class))->assertForbidden();
        $this->delete(route('sinh-viens.destroy', $student))->assertForbidden();
        $this->get('/menus')->assertForbidden();
        $this->assertNotSoftDeleted($class);
        $this->assertNotSoftDeleted($student);
    }

    public function test_admin_seeder_is_idempotent_and_admin_can_delete(): void
    {
        $this->seed(AdminUserSeeder::class);
        $this->seed(AdminUserSeeder::class);
        $admin = User::where('username', 'admin')->firstOrFail();
        $this->assertSame(1, User::where('username', 'admin')->count());
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('123456', $admin->password));
        $class = LopHoc::factory()->create();
        $this->actingAs($admin)->delete(route('lop-hocs.destroy', $class))->assertRedirect();
        $this->assertSoftDeleted($class);
    }

    public function test_full_seeder_can_be_rerun_without_duplicate_sample_data(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(10, LopHoc::count());
        $this->assertSame(50, SinhVien::count());
        $this->assertSame(5, Menu::count());
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(10, LopHoc::count());
        $this->assertSame(50, SinhVien::count());
        $this->assertSame(1, User::where('username', 'admin')->count());
        $this->assertSame(5, Menu::count());
    }

    public function test_database_menus_control_sidebar_and_placeholder_pages(): void
    {
        $this->seed(MenuSeeder::class);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $this->get('/menus')->assertOk()->assertSee('Dashboard')->assertSee('API Playground');
        $this->get('/dashboard')->assertOk()->assertSeeInOrder([
            'Dashboard', 'Quản lý lớp học', 'Quản lý sinh viên', 'Quản lý Menu', 'API Playground',
        ]);
        Menu::where('slug', 'lop-hocs')->update(['trang_thai' => false]);
        $this->seed(MenuSeeder::class);
        $this->assertFalse((bool) Menu::where('slug', 'lop-hocs')->firstOrFail()->trang_thai);
        $this->get('/dashboard')->assertDontSee('>Quản lý lớp học</a>', false);
        $this->post('/menus', [
            'ten_hien_thi' => 'Lịch thi', 'slug' => 'lich-thi', 'loai_lien_ket' => 'page',
            'vi_tri' => 'sidebar', 'thu_tu' => 5, 'trang_thai' => 1,
        ])->assertRedirect('/menus');
        $menu = Menu::where('slug', 'lich-thi')->firstOrFail();
        $this->assertSame('/pages/lich-thi', $menu->url);
        $this->get('/dashboard')->assertSee('href="/pages/lich-thi"', false);
        $this->get('/pages/lich-thi')->assertOk()->assertSee('Chức năng đang được phát triển');
        $this->post('/menus', [
            'ten_hien_thi' => 'Duplicate', 'slug' => 'lich-thi', 'loai_lien_ket' => 'page',
            'vi_tri' => 'sidebar', 'thu_tu' => 6,
        ])->assertSessionHasErrors('slug');
        $this->post('/menus', [
            'ten_hien_thi' => 'Reserved', 'slug' => 'login', 'loai_lien_ket' => 'page',
            'vi_tri' => 'sidebar', 'thu_tu' => 6,
        ])->assertSessionHasErrors('slug');
        $this->put('/menus/'.$menu->id, [
            'ten_hien_thi' => 'Lịch thi mới', 'slug' => 'lich-thi', 'loai_lien_ket' => 'page',
            'vi_tri' => 'sidebar', 'thu_tu' => 0, 'trang_thai' => 1,
        ])->assertRedirect('/menus');
        $this->get('/dashboard')->assertSeeInOrder(['Lịch thi mới', 'Dashboard']);
        $this->put('/menus/'.$menu->id, [
            'ten_hien_thi' => 'Lịch thi mới', 'slug' => 'lich-thi-moi', 'loai_lien_ket' => 'page',
            'vi_tri' => 'header', 'thu_tu' => 0, 'trang_thai' => 1,
        ])->assertRedirect('/menus');
        $this->get('/pages/lich-thi')->assertNotFound();
        $this->get('/pages/lich-thi-moi')->assertOk();
        $this->get('/dashboard')->assertDontSee('class="nav-link " href="/pages/lich-thi-moi"', false)
            ->assertSee('class="small text-decoration-none" href="/pages/lich-thi-moi"', false);
        $this->put('/menus/'.$menu->id, [
            'ten_hien_thi' => 'Lịch thi mới', 'slug' => 'lich-thi-moi', 'loai_lien_ket' => 'page',
            'vi_tri' => 'sidebar', 'thu_tu' => 0, 'trang_thai' => 0,
        ])->assertRedirect('/menus');
        $this->get('/pages/lich-thi-moi')->assertNotFound();
        $this->get('/dashboard')->assertDontSee('href="/pages/lich-thi-moi"', false);
        $this->delete('/menus/'.$menu->id)->assertRedirect('/menus');
        $this->get('/pages/lich-thi-moi')->assertNotFound();

        $staff = User::factory()->create();
        $this->actingAs($staff);
        $this->get('/dashboard')->assertDontSee('href="/menus"', false);
        $this->get('/menus')->assertForbidden();
    }

    public function test_api_auth_validation_permissions_and_crud(): void
    {
        $staff = User::factory()->create();
        $admin = User::factory()->create();
        $admin->role = 'admin'; $admin->save();
        $this->getJson('/api/lop-hocs')->assertUnauthorized()->assertJsonPath('success', false);
        $this->actingAs($staff);
        $this->postJson('/api/lop-hocs', [])->assertUnprocessable()->assertJsonPath('success', false);
        $class = $this->postJson('/api/lop-hocs', [
            'ma_lop' => 'API01', 'ten_lop' => 'API Class', 'si_so' => 20, 'giao_vien' => 'Teacher', 'trang_thai' => false,
        ])->assertCreated()->json('data');
        $this->assertFalse((bool) LopHoc::findOrFail($class['id'])->trang_thai);
        $this->getJson('/api/lop-hocs/'.$class['id'])->assertOk();
        $this->patchJson('/api/lop-hocs/'.$class['id'], ['ten_lop' => 'Updated'])->assertOk()->assertJsonPath('data.ten_lop', 'Updated');
        $this->deleteJson('/api/lop-hocs/'.$class['id'])->assertForbidden();
        $this->getJson('/api/lop-hocs/999999')->assertNotFound()->assertJsonPath('success', false);

        $student = $this->postJson('/api/sinh-viens', [
            'ma_sinh_vien' => 'SVAPI01', 'ho_ten' => 'Student', 'email' => 'svapi@example.test', 'lop_hoc_id' => $class['id'],
            'trang_thai' => false,
        ])->assertCreated()->json('data');
        $this->assertFalse((bool) SinhVien::findOrFail($student['id'])->trang_thai);
        $this->putJson('/api/sinh-viens/'.$student['id'], [
            'ma_sinh_vien' => 'SVAPI01', 'ho_ten' => 'Student Updated', 'email' => 'svapi@example.test', 'lop_hoc_id' => $class['id'],
        ])->assertOk();
        $this->getJson('/api/sinh-viens/'.$student['id'])->assertOk();
        $this->deleteJson('/api/sinh-viens/'.$student['id'])->assertForbidden();
        $this->actingAs($admin)->deleteJson('/api/sinh-viens/'.$student['id'])->assertOk();
        $this->actingAs($admin)->deleteJson('/api/lop-hocs/'.$class['id'])->assertOk();
    }

    public function test_web_crud_views_filters_and_menu_validation(): void
    {
        $admin = User::factory()->create();
        $admin->role = 'admin'; $admin->save();
        $this->actingAs($admin);
        $this->get('/dashboard')->assertOk();
        $this->get('/lop-hocs/create')->assertOk();
        $this->post('/lop-hocs', [])->assertSessionHasErrors(['ma_lop', 'ten_lop']);
        $this->post('/lop-hocs', [
            'ma_lop' => 'WEB01', 'ten_lop' => 'Web Class', 'si_so' => 25, 'giao_vien' => 'Teacher', 'trang_thai' => '0',
        ])->assertRedirect('/lop-hocs');
        $class = LopHoc::where('ma_lop', 'WEB01')->firstOrFail();
        $this->assertFalse((bool) $class->trang_thai);
        $this->get('/lop-hocs?search=WEB01&direction=asc&perPage=10')->assertOk()->assertSee('WEB01')->assertSee('/lop-hocs?search=WEB01');
        $this->get('/lop-hocs/'.$class->id)->assertOk();
        $this->get('/lop-hocs/'.$class->id.'/edit')->assertOk();
        $this->put('/lop-hocs/'.$class->id, [
            'ma_lop' => 'WEB01', 'ten_lop' => 'Web Class Updated', 'si_so' => 25, 'giao_vien' => 'Teacher', 'sdt_gv' => '0123456789', 'trang_thai' => '1',
        ])->assertRedirect('/lop-hocs');
        $this->assertSame('0123456789', $class->refresh()->sdt_gv);

        $this->get('/sinh-viens/create')->assertOk();
        $this->post('/sinh-viens', [
            'ma_sinh_vien' => 'SVWEB01', 'ho_ten' => 'Student', 'email' => 'webstudent@example.test',
            'lop_hoc_id' => $class->id, 'trang_thai' => '0',
        ])->assertRedirect('/sinh-viens');
        $student = SinhVien::where('ma_sinh_vien', 'SVWEB01')->firstOrFail();
        $this->assertFalse((bool) $student->trang_thai);
        $this->get('/sinh-viens?search=SVWEB01')->assertOk()->assertSee('SVWEB01');
        $this->get('/sinh-viens/'.$student->id)->assertOk();
        $this->get('/sinh-viens/'.$student->id.'/edit')->assertOk();
        $this->put('/sinh-viens/'.$student->id, [
            'ma_sinh_vien' => 'SVWEB01', 'ho_ten' => 'Updated Student', 'email' => 'webstudent@example.test',
            'lop_hoc_id' => $class->id, 'trang_thai' => '1',
        ])->assertRedirect('/sinh-viens');
        $this->assertSame('Updated Student', $student->refresh()->ho_ten);

        $this->get('/menus/create')->assertOk();
        $this->post('/menus', [
            'ten_hien_thi' => 'Unsafe', 'slug' => 'unsafe', 'url' => 'javascript:alert(1)', 'vi_tri' => 'sidebar', 'thu_tu' => 1,
        ])->assertSessionHasErrors('url');
        $this->post('/menus', [
            'ten_hien_thi' => 'Classes', 'slug' => 'classes', 'url' => '/lop-hocs', 'vi_tri' => 'sidebar', 'thu_tu' => 1, 'trang_thai' => '0',
        ])->assertRedirect('/menus');
        $menu = \App\Models\Menu::where('slug', 'classes')->firstOrFail();
        $this->assertFalse((bool) $menu->trang_thai);
        $this->get('/menus')->assertOk();
        $this->get('/menus/'.$menu->id)->assertOk();
        $this->get('/menus/'.$menu->id.'/edit')->assertOk();
        $this->put('/menus/'.$menu->id, [
            'ten_hien_thi' => 'Classes Updated', 'slug' => 'classes', 'url' => '/lop-hocs', 'vi_tri' => 'sidebar', 'thu_tu' => 2, 'trang_thai' => '1',
        ])->assertRedirect('/menus');
        $this->delete('/menus/'.$menu->id)->assertRedirect('/menus');
        $this->delete('/sinh-viens/'.$student->id)->assertRedirect('/sinh-viens');
        $this->delete('/lop-hocs/'.$class->id)->assertRedirect('/lop-hocs');
    }
}
