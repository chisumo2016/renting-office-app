<?php

namespace App\Http\Controllers;

use App\Http\Resources\OfficeResource;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OfficeController extends Controller
{
    public  function  index() : AnonymousResourceCollection
    {

        $offices = Office::query('id', 'DESC')//->orderBy('id','DESC')
                    ->latest('id')
                    ->paginate(20);

        return OfficeResource::collection(
            $offices
        );
    }
}
