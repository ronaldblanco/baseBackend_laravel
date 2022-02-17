<?php

namespace App\Transformers;

use App\Models\Office;
use League\Fractal\TransformerAbstract;

class OfficeTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
        //'users'
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];

    public function includeUsers(Office $office)
    {
        return $this->collection($office->users, new UserTransformer());
    }
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Office $office)
    {
        return [
            //
            'id' => $office->id,
            'name' => $office->name,
            'logo' => $office->logo,
            'address' => $office->address,
            'company_id' => $office->company_id,
            'created_at' => $office->created_at,
            'min_downpayment' => $office->min_downpayment,
            
            'phone' => $office->phone,
            'email' => $office->email,
            'website' => $office->website,
        ];
    }
}
