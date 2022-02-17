<?php

namespace App\Transformers;

use App\Models\Company;
use League\Fractal\TransformerAbstract;

class CompanyTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
        //'offices'
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];

    public function includeOffices(Company $company)
    {
        return $this->collection($company->offices, new OfficeTransformer());
    }
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Company $company)
    {
        return [
            //
            'id' => $company->id,
            'name' => $company->name,
            'logo' => $company->logo,
            'email' => $company->email,
            'phone' => $company->phone,
            'fax' => $company->fax,
            'website' => $company->website,
            'address' => $company->address,
            'smtpserver' => $company->smtpserver,
            'smtpsecure' => $company->smtpsecure,
            'smtpuser' => $company->smtpuser,
            'smtppass' => $company->smtppass,
            'smtpport' => $company->smtpport
        ];
    }
}
