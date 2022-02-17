<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use \Spatie\Tags\HasTags;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\LogOptions;

class Company extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasTags, LogsActivity, CausesActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'tax', 'commission', 'email', 'phone', 'fax', 'website', 'smtpserver',
        'smtpsecure', 'smtpuser', 'smtppass', 'smtpport', 'address', 'theme', 'logo'

    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('logo')
            //->useFallbackUrl('/static/img/logo-1.jpg')
            ->acceptsMimeTypes(['image/png', 'image/jpeg'])
            ->singleFile();

        $this
            ->addMediaCollection('print_logo')
            //->useFallbackUrl('/static/img/logo-1.jpg')
            ->acceptsMimeTypes(['image/png', 'image/jpeg'])
            ->singleFile();
    }

    public function offices()
    {
        return $this->hasMany('App\Models\Office', 'company_id');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User', 'company_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name', 'tax', 'commission', 'email', 'phone', 'fax', 'website', 'smtpserver',
                'smtpsecure', 'smtpuser', 'smtppass', 'smtpport', 'address', 'theme', 'logo'
            ])
            ->logOnlyDirty(true)
            ->useLogName('companys');
    }
}
