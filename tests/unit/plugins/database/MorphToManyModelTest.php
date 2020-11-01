<?php

use Database\Tester\Models\Post;
use Database\Tester\Models\Tag;
use October\Rain\Database\MorphPivot;


class MorphToManyModelTest extends PluginTestCase
{
    public function setUp() : void
    {
        parent::setUp();

        include_once base_path().'/tests/fixtures/plugins/database/tester/models/Post.php';
        include_once base_path().'/tests/fixtures/plugins/database/tester/models/Tag.php';

        $this->runPluginRefreshCommand('Database.Tester');
    }

    public function testRelation()
    {
        Model::unguard();
        $post1 = Post::create(['title' => 'First post', 'description' => 'Post description.']);
        $post2 = Post::create(['title' => 'Second post', 'description' => 'Post description.']);
        $tag1 = Tag::create(['name' => 'First tag']);
        $tag2 = Tag::create(['name' => 'Second tag']);
        Model::reguard();

        $post1->tags()->add($tag1);
        $post1->tags()->add($tag2);
        $post2->tags()->add($tag1);

        $this->assertContains($post1->id, $tag1->posts()->where('taggable_type', get_class($post1))->lists('taggable_id'));
        $this->assertContains($post2->id, $tag1->posts()->where('taggable_type', get_class($post2))->lists('taggable_id'));
        $this->assertNotContains($post2->id, $tag2->posts()->where('taggable_type', get_class($post2))->lists('taggable_id'));
    }


    public function testPivot()
    {
        Model::unguard();
        $post1 = Post::create(['title' => 'First post', 'description' => 'Post description.']);
        $tag1 = Tag::create(['name' => 'First tag']);
        Model::reguard();

        $post1->tags()->add($tag1, ['comment' => 'Any comment']);

        $this->assertEquals('Any comment', $tag1->posts()->where('taggable_type', get_class($post1))->where('taggable_id', $post1->id)->first()->pivot->comment);
    }


    public function testPivotModel()
    {
        Post::extend(function ($model) {
            $model->morphToMany['tags']['pivotModel'] = CustomMorphPivot::class;
        });

        Model::unguard();
        $post1 = Post::create(['title' => 'First post', 'description' => 'Post description.']);
        $tag1 = Tag::create(['name' => 'First tag']);
        Model::reguard();

        $tag1->posts()->add($post1, ['comment' => 'Any comment']);

        $this->assertEquals(CustomMorphPivot::class, get_class($post1->tags()->where('taggable_type', get_class($post1))->where('taggable_id', $post1->id)->first()->pivot));
        $this->assertEquals('Any comment', $post1->tags()->where('taggable_type', get_class($post1))->where('taggable_id', $post1->id)->first()->pivot->getComment());
    }
}


class CustomMorphPivot extends MorphPivot
{
    public $fillable = ['comment'];

    public function getComment()
    {
        return $this->comment;
    }
}
