<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace Tests\Feature\Http\Controller\Admin;

use App\Models\AdminPage;
use Database\Seeders\AdminPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ActingAsAdminUser;
use Tests\TestCase;

class AdminPageControllerTest extends TestCase
{
    use RefreshDatabase;
    use ActingAsAdminUser;

    public function testIndex()
    {
        $page1 = AdminPage::factory()->create(['sort' => 1]);
        $page2 = AdminPage::factory()->create(['sort' => 2]);
        $page3 = AdminPage::factory()->create(['sort' => 3, 'public' => true]);

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_pages')
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => ['*' => ['permission']]])
            ->assertJsonPath('data.*.id', [$page3['id'], $page2['id'], $page1['id']])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_pages?public=0')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.*.id', [$page2['id'], $page1['id']])
            ->assertJsonPath('code', 0)
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_pages?public=1')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.*.id', [$page3['id']])
            ->assertJsonPath('code', 0)
            ->assertOk();
    }

    public function testStore()
    {
        $this->actingAsSuperAdminUser()
            ->postJson('api/admin/admin_pages')
            ->assertJsonStructure(['errors' => ['name', 'path']])
            ->assertStatus(422);

        $this->actingAsSuperAdminUser()
            ->postJson('api/admin/admin_pages', ['name' => 'Name', 'path' => 'Path'])
            ->assertJsonPath('data.name', 'Name')
            ->assertJsonPath('data.path', 'Path')
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->postJson('api/admin/admin_pages', ['name' => 'Name', 'path' => 'Path'])
            ->assertJsonStructure(['errors' => ['path']])
            ->assertStatus(422);
    }

    public function testUpdate()
    {
        $page1 = AdminPage::factory()->create(['name' => 'Name1', 'path' => 'path1']);
        $page2 = AdminPage::factory()->create(['name' => 'Name2', 'path' => 'path2']);

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_pages/' . $page1['id'], ['name' => 'Name11', 'path' => 'path11'])
            ->assertJsonPath('data.name', 'Name11')
            ->assertJsonPath('data.path', 'path11')
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_pages/' . $page1['id'], ['name' => 'Name111', 'path' => 'path11'])
            ->assertJsonPath('data.name', 'Name111')
            ->assertJsonPath('data.path', 'path11')
            ->assertOk();

        $this->actingAsSuperAdminUser()
            ->putJson('api/admin/admin_pages/' . $page1['id'], ['name' => 'Name1', 'path' => 'path2'])
            ->assertJsonStructure(['errors' => ['path']])
            ->assertStatus(422);
    }

    public function testShow()
    {
        $page = AdminPage::factory()->create(['name' => 'Name', 'path' => 'path']);

        $this->actingAsSuperAdminUser()
            ->getJson('api/admin/admin_pages/' . $page['id'])
            ->assertJsonPath('data.name', $page['name'])
            ->assertOk();
    }

    public function testDestroy()
    {
        $page = AdminPage::factory()->create(['name' => 'Name', 'path' => 'path']);

        $this->actingAsSuperAdminUser()
            ->deleteJson('api/admin/admin_pages/' . $page['id'])
            ->assertOk();

        $this->assertModelMissing($page);
    }
}
