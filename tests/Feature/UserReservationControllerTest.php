<?php

namespace Tests\Feature;

use App\Models\Office;
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
        $reservations = Reservation::factory()->for($user)->createMany([
           [
               'start_date' => '2021-03-01',
               'end_date'   => '2021-03-15',
           ],
           [
               'start_date' => '2021-03-25',
               'end_date'   => '2021-04-15',
           ],
           [
               'start_date' => '2021-03-25',
               'end_date'   => '2021-03-29',
           ],
           [
               'start_date' => '2021-03-01',
               'end_date'   => '2021-04-15',
           ],
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
           'start_date' => '2021-05-01',
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
            ->assertJsonCount(4, 'data');
            $this->assertEquals($reservations->pluck('id')->toArray(),
                collect($response->json('data'))
                                 ->pluck('id')->toArray());
    }

    /**
     * @test
     */
    public function itFiltersResultByStatus()
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->for($user)->create([
            'status' => Reservation::STATUS_ACTIVE
        ]);
        $reservation2 = Reservation::factory()->for($user)->cancelled()->create();

        $this->actingAs($user);

        $response = $this->getJson('/api/reservations?'.http_build_query([
                'status' => Reservation::STATUS_ACTIVE,
            ]));

        $response
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $reservation->id);

    }

    /**
     * @test
     */
    public function itFiltersResultByOffice()
    {

        $user = User::factory()->create();

        $office = Office::factory()->create();

        $reservation = Reservation::factory()->for($office)->for($user)->create();
        $reservation2 = Reservation::factory()->for($user)->create();

        $this->actingAs($user);

        $response = $this->getJson('/api/reservations?'.http_build_query([
                'office_id' => $office->id,
            ]));

        $response
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $reservation->id);

    }

}


/*DB::enableQueryLog();

dd(
    DB::getQueryLog()
);*/
