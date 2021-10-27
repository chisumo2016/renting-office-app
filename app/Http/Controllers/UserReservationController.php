<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReservationResource;
use App\Models\Office;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserReservationController extends Controller
{
    public  function index()
    {
        abort_unless(auth()->user()->tokenCan('reservations.show'),
            Response::HTTP_FORBIDDEN
        );

        //validation
        validator(request()->all(),
        [
           'status'     =>  Rule::in([Reservation::STATUS_ACTIVE, Reservation::STATUS_CANCELLED]),
            'office_id' =>  ['integer'],
            'from_date' =>  ['date','required_with:to_date'],
            'to_date'   =>  ['date','required_with:from_date','after:from_date'],
        ]
        )->validate();

        $reservations = Reservation::query()
           ->where('user_id', auth()->id())
           ->when(request('office_id'),
              fn($query) => $query->where('office_id', request('office_id'))
           )->when(request('status'),
                fn($query) => $query->where('status', request('status'))
            )->when(request('from_date') && request('to_date'),

               fn($query) =>$query-> betweenDates(request('from_date'),request('to_date'))
            )
            ->with(['office', 'office.featuredImage'])
            ->paginate(20);

        return ReservationResource::collection($reservations);
    }

    public function  create()
    {
        abort_unless(auth()->user()->tokenCan('reservations.make'),
            Response::HTTP_FORBIDDEN
        );

        validator(request()->all(), [
            'office_id'     => ['required', 'integer'],
            'start_date'    => ['required', 'date:Y-m-d'],
            'end_date'      => ['required', 'date:Y-m-d'],
        ]);

        try {
            $office = Office::findOrFail(request('office_id'));
        }catch(ModelNotFoundException $e){
            throw ValidationException::withMessages([
              'office_id' => 'Invalid Office_id'
            ]);
        }
        if ($office->user_id == auth()->id()) {
            throw ValidationException::withMessages([
             'office_id' => 'You cannot make a reservation on your own office'
          ]);
        }
    }
}

/*
 * Moved to query scope
 $query->where(function ($query){
    return $query->whereBetween('start_date',[request('from_date'),request('to_date')])
        ->orWhereBetween('end_date',  [request('from_date'),request('to_date')]);
});
 */
