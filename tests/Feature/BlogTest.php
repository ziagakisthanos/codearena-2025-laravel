<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function testBlogPageIsAccessible()
    {
        $response = $this->get(route('blog.index'));

        $response->assertOk();
    }

    public function testBlogPageHasPosts()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->get(route('blog.index'));
        $response->assertSee($post->title);
    }

    public function testBlogPostPageIsAccessible()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->get(route('blog.show', $post));
        $response->assertOk();
    }
}
