<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\Seller::factory(10)->create();

        \App\Models\User::factory()->create([
            'id' => 1,
            'name' => 'Test Seller',
            'email' => 'test@example.com',
        ]);

        \App\Models\Shop\Apply::create([
            'user_id' => 1,
            'name' => '老王',
            'sex' => 1,
            'mobile' => '13888888888',
            'industry' => '某行业',
            'working_seniority' => 20,
            'area_id' => 450312,
            'town_population' => 8,
            'available_capital' => 80,
            'has_operating_team' => 1,
            'advantage' => '我从事线下零售多年，完全能把线下业务执行得滴水不漏。',
        ]);
    }
}
