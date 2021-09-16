<?php

namespace App\Http\Controllers;

use App\Http\Resources\OfficeResource;
use App\Models\Office;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OfficeController extends Controller
{
    public  function  index() : AnonymousResourceCollection
    {

        $offices = Office::query()//->orderBy('id','DESC')
                    ->where('approval_status', Office::APPROVAL_APPROVED)
                    ->where('hidden', false)
                     ->when(request('host_id'),
                         fn($builder) => $builder->whereUserId(request('host_id')))
                     ->when(request('user_id'),
                         fn($builder) => $builder->whereRelation('reservations', 'user_id' , '=' , request('user_id')))
                    ->when(
                        request('lat') && request('lng'),
                        fn($builder) => $builder->nearestTo(request('lat'),request('lng')),  //Otherwise
                        fn($builder) => $builder->orderBy('id', 'ASC')//oldest record
                    )
                 //latest('id')
                    ->with(['images', 'tags', 'user'])//Problem
                    ->withCount(['reservations' => fn($builder) => $builder->where('status', Reservation::STATUS_ACTIVE)])
                    ->paginate(20);

        return OfficeResource::collection(
            $offices
        );
    }

    public function  show(Office $office)
    {
        $office->loadCount(['reservations' => fn($builder) => $builder->where('status', Reservation::STATUS_ACTIVE)])
        ->load(['images', 'tags', 'user']);
       return OfficeResource::make($office);
    }
}
