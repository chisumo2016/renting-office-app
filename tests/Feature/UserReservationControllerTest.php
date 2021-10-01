<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserReservationControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @test
     */
    public function itListsReservationsThatBelongsToTheUser()
    {

        $user = User::factory()->create();

        $reservation = Reservation::factory()->for($user)->create();

        $image = $reservation->office->images()->create([
            'path'=> 'office_image.jpg'
        ]);

        $reservation->office()->update(['featured_image_id' => $image->id]);

        Reservation::factory()->for($user)->count(2)->create();
        Reservation::factory()->count(3)->create();

        $this->actingAs($user);

        $response = $this->getJson('/api/reservations');

        //dd($response->json());
        $response
            ->assertJsonStructure(['data','meta', 'links'])
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data'=>['*' =>['id', 'office']]])
            ->assertJsonPath('data.0.office.featured_image.id', $image->id);
    }

    /**
     * @test
     */
    public function itListsReservationsFilteredByDateRange()
    {

        $user = User::factory()->create();

        $fromDate = '2021-03-03';
        $toDate   = '2021-04-04';

        //inside the date range
        $reservation1 = Reservation::factory()->for($user)->create([
            'start_date' => '2021-03-01',
            'end_date'   => '2021-03-15',
        ]);

        $reservation2 = Reservation::factory()->for($user)->create([
            'start_date' => '2021-03-25',
            'end_date'   => '2021-04-15',
        ]);

        $reservation3 = Reservation::factory()->for($user)->create([
            'start_date' => '2021-03-25',
            'end_date'   => '2021-03-29',
        ]);

        //Within the range but belongs to a different user
        Reservation::factory()->create([
            'start_date' => '2021-03-25',
            'end_date'   => '2021-03-29',
        ]);

        //outside the date range
        Reservation::factory()->for($user)->create([
            'start_date' => '2021-02-25',
            'end_date'   => '2021-03-01',
        ]);
        Reservation::factory()->for($user)->create([
            'start_date' => '2021-02-01',
            'end_date'   => '2021-05-01',
        ]);

        $this->actingAs($user);

        $response = $this->getJson('/api/reservations?'.http_build_query([
                'from_date' => $fromDate,
                'to_date'   => $toDate,
            ]));

        //dd($response->json('data'));
        //dd( collect($response->json('data'))->pluck('id'));
        $response
            ->assertJsonCount(3, 'data');
            $this->assertEquals(
                [$reservation1->id,$reservation2->id,$reservation3->id],
                collect($response->json('data'))
                                ->pluck('id')->toArray());
    }



}


/*DB::enableQueryLog();

dd(
    DB::getQueryLog()
);*/
