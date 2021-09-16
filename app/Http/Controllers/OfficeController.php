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
                    ->latest('id')
                    ->with(['images', 'tags', 'user'])//Problem
                    ->with(['reservations' => fn($builder) => $builder->where('status', Reservation::STATUS_ACTIVE)])
                    ->paginate(20);

        return OfficeResource::collection(
            $offices
        );
    }
}
