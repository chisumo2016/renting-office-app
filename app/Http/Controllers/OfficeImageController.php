<?php

namespace App\Http\Controllers;

use App\Http\Resources\ImageResource;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class OfficeImageController extends Controller
{
   public  function store(Office $office) :JsonResource
   {
       abort_unless(auth()->user()->tokenCan('office.create'),
           Response::HTTP_FORBIDDEN
       );

       $this->authorize('update',$office);

        request()->validate([
            'image'=> ['file', 'max:5000', 'mimes:jpg,png']
        ]);

        //Store the file
       $path = request()->file('image')->storePublicly('/', ['disk'=>'public']);

       //create an image belong to this office or attach to office
       $image = $office->images()->create([
           'path'=> $path
       ]);

       return ImageResource::make($image);
   }
}
