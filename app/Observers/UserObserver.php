<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserObserver
{

    public function creating(User $user)
    {
        //$user->available_days = json_encode([1, 2, 3, 4, 5, 6, 7]);
    }

    /**
     * Handle the user "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        //
    }


    public function updating(User $user)
    {
        /*$image_64 = $user->signature; //your base64 encoded data

        if( !empty($image_64)){
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
      
            $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
          
          // find substring fro replace here eg: data:image/png;base64,
          
           $image = str_replace($replace, '', $image_64); 
          
           $image = str_replace(' ', '+', $image); 
          
           $imageName = 'doctor.'.$user->id.'_signature.'.$extension;
          
           Storage::disk('local')->put("public/".$imageName, base64_decode($image));
           $user->signature_url = Storage::disk('local')->url($imageName);
        }*/

    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */


    public function updated(User $user)
    {

    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //$user->multifields()->delete();
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
