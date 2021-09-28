<?php

namespace App\Http\Controllers;

use App\Http\Resources\OfficeResource;
use App\Models\Office;
use App\Models\Reservation;

use App\Models\User;
use App\Models\Validators\OfficeValidator;
use App\Notifications\OfficePendingApproval;
use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Http\Response;
use Illuminate\Support\Arr;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;


class OfficeController extends Controller
{
    public  function  index() : JsonResource
    {

        $offices = Office::query()
//            ->where('approval_status', Office::APPROVAL_APPROVED)
//            ->where('hidden', false)
            ->when(request('user_id') && auth()->user() && request('user_id') == auth()->id(),
                  fn($builder) => $builder,//return all the results
                  fn($builder) => $builder->where('approval_status', Office::APPROVAL_APPROVED)->where('hidden', false)
              )
            ->when(request('user_id'), fn($builder) => $builder->whereUserId(request('user_id')))
            ->when(request('visitor_id'),
                fn($builder) => $builder->whereRelation('reservations', 'user_id', '=', request('visitor_id'))
            )
            ->when(
                request('lat') && request('lng'),
                fn($builder) => $builder->nearestTo(request('lat'), request('lng')),
                fn($builder) => $builder->orderBy('id', 'ASC')
            )
            ->with(['images', 'tags', 'user'])
            ->withCount(['reservations' => fn($builder) => $builder->whereStatus(Reservation::STATUS_ACTIVE)])
            ->paginate(20);

        return OfficeResource::collection(
            $offices
        );
    }

    public function  show(Office $office): JsonResource
    {
        $office->loadCount(['reservations' => fn($builder) => $builder->where('status', Reservation::STATUS_ACTIVE)])
            ->load(['images', 'tags', 'user']);

       return OfficeResource::make($office);
    }

    public  function create(): JsonResource
    {
//      if (! auth()->user()->tokenCan('office.create')){
//          abort(Response::HTTP_FORBIDDEN);
//      }

       abort_unless(auth()->user()->tokenCan('office.create'),
          Response::HTTP_FORBIDDEN
       );

        //Validate attributes
        $attributes =(new OfficeValidator())->validate($office = new Office(), request()->all());

        //$attributes['user_id'] = auth()->id();
        $attributes['approval_status'] = Office::APPROVAL_PENDING;
        $attributes['user_id']         = auth()->id();

        //Store office inside a DB transaction
        $office =DB::transaction(function () use ($office,$attributes) {
           //Create through relationship
           //$office= auth()->user()->offices()->create(
           $office->fill(
               Arr::except($attributes,['tags'])
           )->save();

            if (isset($attributes['tags'])){
                //assign the tags  sync/attach
                $office->tags()->attach($attributes['tags']);
            }
                return $office;
       });

        //Notification::send(User::firstWhere('name','Bernard'), new OfficePendingApproval($office));
        Notification::send(User::where('is_admin',true)->get(), new OfficePendingApproval($office));
        return OfficeResource::make(
            $office->load(['images','tags','user'])
        );
    }

    public  function  update(Office $office): JsonResource
    {
        abort_unless(auth()->user()->tokenCan('office.update'),
            Response::HTTP_FORBIDDEN
        );

        $this->authorize('update',$office);

       //Validate attributes
        $attributes =(new OfficeValidator())->validate($office, request()->all());

        $office->fill(Arr::except($attributes,['tags'])); //doesnt communicate with database

        if ($requireReview = $office->isDirty(['lat', 'lng','price_per_day'])){
            $office->fill(['approval_status' => Office::APPROVAL_PENDING]);
        }
        //Update office inside a DB transaction
        DB::transaction(function () use ($office, $attributes) {
            //Update
          $office->save();

          if (isset($attributes['tags'])){
              //assign the tags  sync
              $office->tags()->sync($attributes['tags']);
          }
        });

        if ($requireReview){
            //Notification::send(User::firstWhere('name','Bernard'), new OfficePendingApproval($office));
            Notification::send(User::where('is_admin',true)->get(), new OfficePendingApproval($office));
        }
        return OfficeResource::make(
            $office->load(['images', 'tags', 'user'])
        );
    }


    public function  delete(Office $office)
    {
        abort_unless(auth()->user()->tokenCan('office.update'),
            Response::HTTP_FORBIDDEN
        );

        $this->authorize('delete',$office);

//        if ($office->reservations()->where('status', Reservation::STATUS_ACTIVE)->exists()){
//            throw  ValidationException::withMessages(['office'=>'Cannot delete this Office']);
//        }

        throw_if(
            $office->reservations()->where('status', Reservation::STATUS_ACTIVE)->exists(),
            ValidationException::withMessages(['office'=>'Cannot delete this Office'])
        );
        $office->delete();


    }
}


//Create office through create
//        $office= Office::create(
//            Arr::except($attributes,['tags'])
//        );

//$office->update(
//    Arr::except($attributes,['tags'])
//);
