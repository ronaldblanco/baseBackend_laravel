<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\LogOptions;

class Office extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, CausesActivity;

    protected $fillable = [
        'name', 'logo', 'email', 'phone', 'website', 'address', 'company_id'
    ];

    protected static $logName = 'users';
    protected static $logAttributes = [
        'name', 'logo', 'email', 'phone', 'website', 'address', 'company_id'
    ];
    protected static $logOnlyDirty = true;

    public function users()
    {
        return $this->hasMany('App\Models\User', 'office_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    /*public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name', 'logo', 'email', 'phone', 'website', 'address', 'company_id'
            ])
            ->logOnlyDirty(true)
            ->useLogName('offices');
    }*/
}
