<?php

namespace Tests\Feature;


use App\Models\Office;
use App\Models\Reservation;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        //dd($response->json());
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
    public function itFiltersByUserId()
    {
        Office::factory(3)->create();

        $host =User::factory()->create();

        $office =Office::factory()->for($host)->create();

        $response = $this->get(
            '/api/offices?user_id='. $host->id
        );

        $response->assertOk();
        $response->assertJsonCount(1,'data');
        $this->assertEquals($office->id,$response->json('data')[0]['id']);


    }

    /**
     * @test
     */
    public function itFiltersByVisitorId()
    {
        Office::factory(3)->create();

        $user =User::factory()->create();

        $office =Office::factory()->create();

        //Reservation::factory()->for($office)->for($user)->create();
        Reservation::factory()->for(Office::factory())->create(); //completely not the user we want
        Reservation::factory()->for($office)->for($user)->create();

        $response = $this->get(
            '/api/offices?visitor_id='. $user->id
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
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $office = Office::factory()->for($user)->create();
        $tag = Tag::factory()->create();

        $office->tags()->attach($tag);
        $office->images()->create(['path' => 'image.jpg']);

        $response = $this->get('/api/offices');

        //dd($response->json());
        $response->assertOk();

        //dd($response->json('data'));
        $this->assertIsArray($response->json('data')[0]['tags']);
        $this->assertCount(1, $response->json('data')[0]['tags']);
        $this->assertIsArray($response->json('data')[0]['images']);
        $this->assertCount(1,$response->json('data')[0]['images']);
        $this->assertEquals($user->id, $response->json('data')[0]['user']['id']);

        //$response->dump();
    }

    /**
     * @test
     */
    public  function itReturnsTheNumberOfActiveReservations()
    {
        $office = Office::factory()->create();

        Reservation::factory()->for($office)->create(['status' => Reservation::STATUS_ACTIVE]);
        Reservation::factory()->for($office)->create(['status' => Reservation::STATUS_CANCELLED]);

        $response = $this->get('/api/offices');

        $response->assertOk();
        $this->assertEquals(1,$response->json('data')[0]['reservations_count']);

        //$response->dump();
    }

    /**
     * @test
     */
    public function itOrdersByDistanceWhenCoordinatesAreProvided()
    {
        $office1 = Office::factory()->create([ //far
                'lat' => '39.74051727562952',
                'lng' => '-8.770375324893696',
                'title' => 'Leiria'
            ]);

        $office2 =Office::factory()->create([ //closer
            'lat' => '39.07753883078113',
            'lng' => '-9.281266331143293',
            'title' => 'Torres Vedras'
        ]);

        $response = $this->get('/api/offices?lat=38.720661384644046&lng=-9.16044783453807');

        $response->assertOk();
        $this->assertEquals('Torres Vedras', $response->json('data')[0]['title']);
        $this->assertEquals('Leiria', $response->json('data')[1]['title']);

        $response = $this->get('/api/offices');

        $response->assertOk();
        $this->assertEquals('Leiria',$response->json('data')[0]['title']);
        $this->assertEquals('Torres Vedras',$response->json('data')[1]['title']);

        //$response->dump();
    }

     /**
     * @test
     */
    public function itShowsTheOffice()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();
        $office = Office::factory()->for($user)->create();

        $office->tags()->attach($tag);
        $office->images()->create(['path' => 'image.jpg']);

        Reservation::factory()->for($office)->create(['status' => Reservation::STATUS_ACTIVE]);
        Reservation::factory()->for($office)->create(['status' => Reservation::STATUS_CANCELLED]);

        $response = $this->get('/api/offices/'.$office->id);

        $response->assertOk()
            ->assertJsonPath('data.reservations_count', 1)
            ->assertJsonCount(1, 'data.tags')
            ->assertJsonCount(1, 'data.images')
            ->assertJsonPath('data.user.id', $user->id);
    }

    /**
     * @test
     */
    public  function  itCreatesAnOffice()
    {
        $user   = User::factory()->createQuietly();
        $tags    = Tag::factory(2)->createQuietly();
        //$tag2   = Tag::factory()->createQuietly();

        $this->actingAs($user);

        $response = $this->postJson('/api/offices',
            [
               'title'          => 'Office in Arkansas',
               'description'    =>  'Wooooooo',
                'lat'           => '39.74051727562952',
                'lng'           => '-8.770375324893696',
                'address_line1' => 'address',
                'price_per_day' => 1000,
                'monthly_discount' => 5,
                'tags' => $tags->pluck('id')->toArray()
            ]);
            //dd($response->json());

          $response->assertCreated()
              ->assertJsonPath('data.title', 'Office in Arkansas')
              ->assertJsonPath('data.approval_status', Office::APPROVAL_PENDING)
              ->assertJsonPath('data.reservations_count', 0)
              ->assertJsonPath('data.user.id', $user->id)
              ->assertJsonCount(2, 'data.tags');

          $this->assertDatabaseHas('offices',[
              'title' =>'Office in Arkansas'
          ]);

           //dd($response->json());
    }

    /**
     * @test
     */

    public  function  itDoesntAllowCreateingIfScopeIsNotProvided()
    {
        $user   = User::factory()->createQuietly();

        $token = $user->createToken('test',[]);
         //dd($token);
        $response = $this->postJson('/api/offices', [], [
            'Authorization' => 'Bearer '.$token->plainTextToken
        ]);

        $response->assertStatus(403);
        //$this->assertNotEquals(403,$response->status() );
        //dd($response->json());
    }

    /**
     * @test
     */
    public  function  itUpdateAnOffice()
    {
        $user   = User::factory()->create();
        $tags    = Tag::factory(2)->create();
        $office = Office::factory()->for($user)->create();

        $office->tags()->attach($tags);

        $this->actingAs($user);

        $response = $this->putJson('/api/offices/'.$office->id,[
            'title' =>'Amazing Office'
        ]);
        //dd($response->json());

        $response->assertOk()
            ->assertJsonPath('data.title','Amazing Office');
    }
}
