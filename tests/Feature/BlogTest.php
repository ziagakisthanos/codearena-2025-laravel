<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure that the blog posts page is accessible.
     */
    public function testBlogPostsPageIsAccessible()
    {
        $response = $this->get(route('posts'));

        $response->assertStatus(200);
    }

    /**
     * Ensure that the blog post page is accessible.
     */
    public function testBlogPostPageIsAccessibleAndContainsPost()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->get(route('post', $post));

        $response->assertStatus(200)
            ->assertSee($post->title);
    }

    /**
     * Ensure that the author page contains only the posts by the given author.
     */
    public function testAuthorPageContainsOnlyPostsByAuthor()
    {
        $user1 = User::factory()->create();
        $post1 = Post::factory()->create([
            'user_id' => $user1->id,
        ]);

        $user2 = User::factory()->create();
        $post2 = Post::factory()->create([
            'user_id' => $user2->id,
        ]);

        $response = $this->get(route('author', $user1));

        $response->assertStatus(200)
            ->assertSee($post1->title)
            ->assertDontSee($post2->title);
    }

    /**
     * Ensure that empty posts page displays "No posts found."
     */
    public function testEmptyPostsPageDisplaysNoPostsFound()
    {
        $response = $this->get(route('posts'));

        $response->assertStatus(200)
            ->assertSee('No posts found.');
    }

    /**
     * Ensure that the blog post page returns 404 for unpublished posts.
     */
    public function testBlogPostPageReturns404ForUnpublishedPosts()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'published_at' => null,
        ]);

        $response = $this->get(route('post', $post));

        $response->assertStatus(404);
    }

    /**
     * Ensure that only posts with images are displayed on the blog posts page.
     */
    public function testOnlyPostsWithImagesAreDisplayedOnBlogPostsPage()
    {
        $user = User::factory()->create();

        $postWithImage = Post::factory()->create([
            'user_id' => $user->id,
            'image' => 'image.jpg',
            'published_at' => now()->subDay(),
        ]);

        $postWithoutImage = Post::factory()->create([
            'user_id' => $user->id,
            'image' => null,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('posts'));

        $response->assertStatus(200)
            ->assertSee($postWithImage->title)
            ->assertDontSee($postWithoutImage->title);
    }

    /**
     * Ensure that the blog posts page contains only published posts.
     */
    public function testBlogPostsPageContainsOnlyPublishedPosts()
    {
        $user = User::factory()->create();
        $publishedPost = Post::factory()->create([
            'user_id' => $user->id,
            'published_at' => now(),
        ]);

        $unpublishedPost = Post::factory()->create([
            'user_id' => $user->id,
            'published_at' => null,
        ]);

        $response = $this->get(route('posts'));

        $response->assertStatus(200)
            ->assertSee($publishedPost->title)
            ->assertDontSee($unpublishedPost->title);
    }

    /**
     * Ensure that the blog posts page contains posts sorted by published_at.
     */
    public function testBlogPostsPageContainsPostsSortedByPublishedAt()
    {
        $user = User::factory()->create();
        $post1 = Post::factory()->create([
            'user_id' => $user->id,
            'published_at' => now()->subDays(2),
        ]);

        $post2 = Post::factory()->create([
            'user_id' => $user->id,
            'published_at' => now()->subDays(1),
        ]);

        $response = $this->get(route('posts'));

        $response->assertStatus(200)
            ->assertSeeInOrder([$post2->title, $post1->title]);
    }

    /**
     * Blog has section has section with the authors who have published posts.
     */
    public function testBlogHasSectionWithAuthorsWhoHavePublishedPosts()
    {
        $user1 = User::factory()->create();
        Post::factory()->create([
            'user_id' => $user1->id,
            'published_at' => now(),
        ]);

        $user2 = User::factory()->create();
        Post::factory()->create([
            'user_id' => $user2->id,
            'published_at' => now(),
        ]);
        Post::factory()->create([
            'user_id' => $user2->id,
            'published_at' => now(),
        ]);

        $user3 = User::factory()->create();
        Post::factory()->create([
            'user_id' => $user3->id,
            'published_at' => null,
        ]);

        $response = $this->get(route('posts'));

        $response->assertStatus(200)
            ->assertSeeInOrder([
                '<section id="authors"',
                $user1->name,
                $user2->name,
                '</section>',
            ], false)
            ->assertDontSee($user3->name);
    }

        /**
     * Ensure that the promoted posts page contains only promoted posts.
     */
    public function testPromotedPostsPageContainsOnlyPromotedPosts()
    {
        $user = User::factory()->create();

        $promotedPost = Post::factory()->create([
            'user_id' => $user->id,
            'published_at' => now()->subDay(),
            'promoted' => true,
        ]);

        $unpromotedPost = Post::factory()->create([
            'user_id' => $user->id,
            'published_at' => now()->subDay(),
            'promoted' => false,
        ]);

        $response = $this->get('/promoted');

        $response->assertStatus(200)
            ->assertSee($promotedPost->title)
            ->assertDontSee($unpromotedPost->title);
    }

    /**
     * Ensure that promoted posts are displayed first.
     */
    public function testPromotedPostsAreDisplayedFirst()
    {
        $user = User::factory()->create();

        $post1 = Post::factory()->create([
            'user_id' => $user->id,
            'promoted' => true,
            'published_at' => now()->subDays(10),
        ]);

        $post2 = Post::factory()->create([
            'user_id' => $user->id,
            'promoted' => false,
            'published_at' => now()->subDays(2),
        ]);

        $post3 = Post::factory()->create([
            'user_id' => $user->id,
            'promoted' => false,
            'published_at' => now()->subDays(1),
        ]);

        $response = $this->get(route('posts'));

        $response->assertStatus(200)
            ->assertSeeInOrder([$post1->title, $post3->title, $post2->title]);
    }

    /**
     * Ensure that the blog post page contains a comment form.
     * The form should contain fields for the commentator's name and the comment body.
     * The form's input fields should be required.
     */
    public function testBlogPostPageContainsCommentForm()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'published_at' => now(),
        ]);

        $response = $this->get(route('post', $post));

        $response->assertStatus(200)
            ->assertSee('id="comment-form"', false)
            ->assertSee('id="name" required', false)
            ->assertSee('id="body" required', false);
    }

    /**
     * Ensure that user can add a comment to a post.
     * Comment should be displayed on the post page.
     * The comment's name and body should be validated.
     */
    public function testUserCanAddCommentToPost()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'published_at' => now(),
        ]);

        $response = $this->post(route('comment', $post), [
            'body' => 'This is a comment.',
        ]);

        $response->assertSessionHasErrors('name');

        $response = $this->post(route('comment', $post), [
            'name' => 'John Doe',
        ]);

        $response->assertSessionHasErrors('body');

        $response = $this->followingRedirects()
            ->post(route('comment', $post), [
                'name' => 'John Doe',
                'body' => 'This is a comment.',
            ]);

        $response->assertSee('This is a comment.')
            ->assertSee('John Doe');
    }

    /**
     * Ensure that the blog post comments are displayed on the post page sorted by created_at
     * in descending order (newest first).
     * Ensure that the date of the comment is displayed in the format readable for humans.
     */
    public function testBlogPostCommentsAreDisplayedOnPostPageSortedByCreatedAt()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'published_at' => now(),
        ]);

        $comment1 = $post->comments()->create([
            'name' => 'John Doe',
            'body' => 'This is a comment.',
            'created_at' => now()->subDays(2),
        ]);

        $comment2 = $post->comments()->create([
            'name' => 'Jane Doe',
            'body' => 'This is another comment.',
            'created_at' => now()->subDays(1),
        ]);

        $response = $this->get(route('post', $post));

        $response->assertStatus(200)
            ->assertSeeInOrder([$comment2->body, $comment1->body])
            ->assertSee($comment1->created_at->diffForHumans())
            ->assertSee($comment2->created_at->diffForHumans());
    }

    /**
     * Ensure that the blog post comment has a delete button.
     */
    public function testBlogPostCommentHasDeleteButton()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'published_at' => now(),
        ]);

        $comment = $post->comments()->create([
            'name' => 'John Doe',
            'body' => 'This is a comment.',
        ]);

        $response = $this->get(route('post', $post));

        $response->assertStatus(200)
            ->assertSee('Delete');
    }

    /**
     * Ensure that a blog post comment can be deleted.
     */
    public function testBlogPostCommentCanBeDeleted()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'published_at' => now(),
        ]);

        $comment = $post->comments()->create([
            'name' => 'John Doe',
            'body' => 'This is a comment.',
        ]);

        $response = $this->delete(route('comment.delete', $comment));

        $response->assertRedirect(route('post', $post))
            ->assertDontSee($comment->body);
    }

    /**
     * TOD:Ensure that blog posts page has pagination.
     */
    public function testBlogPostsPageHasPagination()
    {
        $this->markTestIncomplete();
    }
}
