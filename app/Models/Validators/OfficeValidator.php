<?php

namespace App\Models\Validators;

use App\Models\Office;
use Illuminate\Validation\Rule;

class OfficeValidator
{
    public  function validate(Office $office, array $atrributes): array
    {
        return validator($atrributes,   //request()->all(),
            [
                'title'             =>[Rule::when($office->exists, 'sometimes'),'required', 'string'],
                'description'       =>[Rule::when($office->exists, 'sometimes'),'required', 'string'],
                'lat'               =>[Rule::when($office->exists, 'sometimes'),'required', 'numeric'],
                'lng'               =>[Rule::when($office->exists, 'sometimes'),'required', 'numeric'],
                'address_line1'     =>[Rule::when($office->exists, 'sometimes'),'required', 'string'],
                'hidden'            =>['bool'],
                'price_per_day'     =>[Rule::when($office->exists, 'sometimes'),'required','integer','min:100'],
                'monthly_discount'  =>['integer','min:0','max:90'],

                //Relationship attributes
                'tags'      =>['array'],
                'tags.*'    =>['integer', Rule::exists('tags','id')]
            ]
        )->validate();
    }
}