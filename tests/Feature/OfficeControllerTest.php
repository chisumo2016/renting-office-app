<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\Office;
use App\Models\Reservation;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OfficeControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public  function itListsAllOfficeInPaginateWay()
    {
        Office::factory(3)->create();
        $response = $this->get('/api/offices');
        //$response->dump();
       // $response->assertOk();
        $response->assertJsonCount(3,'data');

        $this->assertNotNull($response->json('data')[0]['id']);
        $this->assertNotNull($response->json('meta'));
        $this->assertNotNull($response->json('links'));


        //$this->assertCount(3, $response->json('data'));
        //$response->assertStatus(200);
        //$response->assertOk(200)->dump();
    }

    /**
     * @test
     */
    public function itOnlyListsOfficesThatAreNotHiddenAndApproved()
    {
        Office::factory(3)->create();

        Office::factory()->create(['hidden' => true]);
        Office::factory()->create(['approval_status'=> Office::APPROVAL_PENDING]);

        $response = $this->get('/api/offices');

        $response->assertOk();
        $response->assertJsonCount(3,'data');
    }

    /**
     * @test
     */
    public function itFiltersByHostId()
    {
        Office::factory(3)->create();

        $host =User::factory()->create();

        $office =Office::factory()->for($host)->create();

        $response = $this->get(
            '/api/offices?host_id='. $host->id
        );

        $response->assertOk();
        $response->assertJsonCount(1,'data');
        $this->assertEquals($office->id,$response->json('data')[0]['id']);


    }

    /**
     * @test
     */
    public function itFiltersByUserId()
    {
        Office::factory(3)->create();

        $user =User::factory()->create();

        $office =Office::factory()->create();

        //Reservation::factory()->for($office)->for($user)->create();
        Reservation::factory()->for(Office::factory())->create();
        Reservation::factory()->for($office)->for($user)->create();

        $response = $this->get(
            '/api/offices?user_id='. $user->id
        );

        $response->assertOk();
        $response->assertJsonCount(1,'data');
        $this->assertEquals($office->id,$response->json('data')[0]['id']);


    }

    /**
     * @test
     */
    public function itIncludesImagesTagsAndUser()
    {
        $user   = User::factory()->create();
        $tag    = Tag::factory()->create();
        $office = Office::factory()->for($user)->create();

        $office->tags()->attach($tag);
        $office->images()->create(['path' => 'image.jpg']);

        $response = $this->get('/api/offices');

        //$response->dump();

        $response->assertOk();

        $this->assertIsArray($response->json('data')[0]['tags']);
        $this->assertCount(1, $response->json('data')[0]['tags']);
        $this->assertIsArray($response->json('data')[0]['images']);
        $this->assertCount(1,$response->json('data')[0]['images']);
        $this->assertEquals($user->id, $response->json('data')[0]['user']['id']);

        //$response->dump();






    }


}
