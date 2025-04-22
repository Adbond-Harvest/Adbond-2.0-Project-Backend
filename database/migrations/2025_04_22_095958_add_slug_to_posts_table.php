<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use app\Helpers;
use app\Models\Post;

return new class extends Migration
{
    private function addSlug()
    {
        $posts = Post::all();
        if($posts->count() > 0) {
            foreach($posts as $post) {
                if(!$post->slug) {
                    $post->slug = Helpers::createSlug($post->topic);
                    $post->update();
                }
            }
        }
    }
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string("slug")->after("topic")->nullable();
        });
        $this->addSlug();
        Schema::table('posts', function (Blueprint $table) {
            $table->string("slug")->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            //
        });
    }
};
