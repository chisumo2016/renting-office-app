<?php

namespace Tests\Feature;

use App\Models\Office;
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


}
