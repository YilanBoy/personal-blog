<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Reply;
use App\Models\User;
use App\Models\Post;
use Faker\Generator as Faker;

class ReplySeeder extends Seeder
{
    public function run()
    {
        // 所有會員 ID 數組，如 : [1, 2, 3, 4]
        $user_ids = User::all()->pluck('id')->toArray();

        // 所有文章 ID 數組，如 : [1, 2, 3, 4]
        $post_ids = Post::all()->pluck('id')->toArray();

        // 獲取 Faker 實例
        $faker = app(Faker::class);

        $replies = Reply::factory()
            ->times(1000)
            ->make()
            ->each(function ($reply, $index) use ($user_ids, $post_ids, $faker) {

                // 從會員 ID 數據中隨機取出一個並賦值
                $reply->user_id = $faker->randomElement($user_ids);

                // 文章 ID，同上
                $reply->post_id = $faker->randomElement($post_ids);
            });

        Reply::insert($replies->toArray());
    }

}
