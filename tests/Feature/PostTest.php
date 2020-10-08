<?php

namespace Tests\Feature;

use App\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function testNoBlogPostsWhenNothingInDatabase()
    {

        $response = $this->get('/posts');

        $response->assertSeeText('No Blog Posts Yet!');
    }

    public function testSee1BlogPostWhenThereIsOne()
    {
        //Arrange
        $post = $this->createDummyBlogPost();

        //Act
        $response = $this->get('/posts');

        //Assert
        $response->assertSeeText('New Blog Post');
        $this->assertDatabaseHas('blog_posts',['title'=>'New Blog Post']);

    }

    public function testStoreValid()
    {
        $params=['title'=> 'Valid title',
        'content' => 'At least 10 Characters'];

        $this->post('/posts',$params)
        ->assertStatus(302)
        ->assertSessionHas('status');

        $this->assertEquals(session('status'),'Blog post was created!');
    }

    public function testStoreFail()
    {
        $params=['title'=> 'x',
        'content' => 'x'];

        $this->post('/posts',$params)
        ->assertStatus(302)
        ->assertSessionHas('errors');

        $messages = session('errors')->getMessages();

        $this->assertEquals($messages['title'][0],'The title must be at least 5 characters.');
        $this->assertEquals($messages['content'][0],'The content must be at least 10 characters.');

    }

    public function testUpdateValid()
    {
        //Arrange
        $post = $this->createDummyBlogPost();


        $this->assertDatabaseHas('blog_posts',[
            'title'=>'New Blog Post'
        ]);

        $params=['title'=> 'A new title',
        'content' => 'A new valid content'];

        $this->put("/posts/{$post->id}",$params)
        ->assertStatus(302)
        ->assertSessionHas('status');

        $this->assertEquals(session('status'),'Blog post was updated!');

        $this->assertDatabaseMissing('blog_posts',[
            'title'=>'New Blog Post'
        ]);

        $this->assertDatabaseHas('blog_posts',[
            'title'=>'A new title'
        ]);
    }

    public function testDelete()
    {
        //Arrange
        $post = $this->createDummyBlogPost();

        $this->assertDatabaseHas('blog_posts',[
            'title'=>'New Blog Post'
        ]);

        $this->put("/posts/{$post->id}")
        ->assertStatus(302)
        ->assertSessionHas('status');

        $this->assertEquals(session('status'),'Blog post was deleted!');
        $this->assertDatabaseMissing('blog_posts',[
            'title'=>'New Blog Post'
        ]);
    }

    private function createDummyBlogPost(): BlogPost
    {
        $post = new BlogPost();
        $post->title = 'New Blog Post';
        $post->content = 'Content of Blog Post';
        $post->save();

        return $post;
    }
}
