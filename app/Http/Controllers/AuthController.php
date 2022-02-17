<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Helpers\UrlHelper;
use Illuminate\Support\Str;
use Spatie\Fractal\Fractal;
use Illuminate\Http\Request;
use App\Mail\SendForgetPassword;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Transformers\UserTransformer;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthController extends Controller
{
    private $urlHelper;

    public function __construct(UrlHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    public function login(Request $request)
    {
        //dd($request);
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);


        if (!Auth::attempt($loginData)) {
            return response(['message' => 'Invalid Credentials']);
        }
        //dd($loginData);

        $user = Auth::user();
        
        if ($user->active == 0) {
            return response(['message' => 'User Not Active']);
        }

        //dd($loginData);

        $clientIP = \Request::getClientIp(true);
        if (!empty($user->ips)) {
            $ips = explode(',', $user->ips);
            if (!in_array($clientIP, $ips)) {
                return response(['message' => 'Location not authorized']);
            }
        }

        //dd($loginData);
        //dd($user);

        $user->isUser = false;
        $accessToken = $user->createToken('authToken', ['login'])->accessToken;
        
        //dd($loginData);
        //dd($user);
        //return $user;
        try{

            $final = Fractal::create()
            ->item($user)
            ->transformWith(new UserTransformer)
            
            ->includeRoles()
            ->includePermissions()
           
            ->addMeta(
                [
                    'access_token' => $accessToken,
                ]
            )
            ->toArray();

            return $final;
        } catch(Exception $e){
            return $e;
        }
    }

    public function gLogin(Request $request, $email)
    {

        try {
            $user = User::where('email', '=', $email)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'data' => null, 'message' => 'User not found']);
        }

        if ($user->active == 0) {
            return response(['message' => 'User Not Active']);
        }


        try {
            $this->_ipValidation($user);
        } catch (Exception $ex) {
            return response(['message' => $ex->getMessage()]);
        }


        $user->isUser = true;
        $accessToken = $user->createToken('authToken', ['manage-dashboard'])->accessToken;

        return Fractal::create()
            ->item($user)
            ->transformWith(new UserTransformer)
            ->includeRoles()
            ->includePermissions()
            
            ->addMeta(
                [
                    'access_token' => $accessToken,
                ]
            )
            ->toArray();
    }

    public function getUserByToken(Request $request)
    {

        $user = request()->user();

        if ($user->active == 0) {
            return $this->urlHelper->responseError("User Not Active");
        }


        if (!$user->isUser) {
            //return $this->urlHelper->responseError("User Not Active");
        }

        return Fractal::create()
            ->item($user)
            ->transformWith(new UserTransformer)
            
            ->includeRoles()
            ->includePermissions()
           
            ->toArray();
    }

    public function getPortalUserByToken(Request $request)
    {
        $user = request()->user();
        $contact = Fractal::create()
            ->item($user)
            //->transformWith(new ContactTransformer)
            ->toArray();
        if ($contact) {
            return $contact;
        }
        return response(['message' => 'Invalid Credentials'], 401);
    }

    public function pForgetPsw(Request $request)
    {
        $email = $request['email'];
        $origin = $request['origin'];
        //$origin = $request->path();
        //$contact = Contact::has('record')->where('email', '=', $email)->first();
        /*if ($contact == null) {
            return $this->urlHelper->responseError("Contact not found");
        }*/

        $uuid =  Str::uuid()->toString();
        /*$contact->uuid_recover = $uuid;
        $contact->save();*/

        //send an email to contact in order to change the password
        try {
            //$origin="http://localhost:3000/portal/changepsw/".$uuid;
            $origin .= '/portal/changepsw/' . $uuid;
            //Mail::to($contact->email)->send(new SendForgetPassword($contact, $origin));
            //return response()->json($origin);
            return $this->urlHelper->responseOK("Email sent");
        } catch (Exception $ex) {
            return $this->urlHelper->responseError("Send recover password mail failed");
        }

        //throw new Exception($contact->fname);
    }

    public function pchangepsw(Request $request)
    {
        $uuid = $request['uuid'];
        $psw1 = $request['psw1'];
        $psw2 = $request['psw2'];
        if ($uuid == null || strlen(trim($uuid)) == 0) {
            throw new Exception("Invalid uuid");
        }
        if (strlen(trim($psw1)) == 0) {
            throw new Exception("Invalid password");
        }
        if ($psw1 != $psw2) {
            throw new Exception("Passwords must match");
        }
        /*$contact = Contact::where('uuid_recover', '=', $uuid)->first();
        if ($contact == null) {
            return $this->urlHelper->responseError("Contact not found");
        }
        $contact->password = bcrypt($psw1);
        $contact->uuid_recover = null;
        $contact->save();*/
        return response()->json("Password changed");
    }

    private function _ipValidation($user)
    {
        if ($user->ip_restriction == 0) return true;

        $clientIP = \Request::getClientIp(true);
        $ipbuildings = $user->buildings->pluck('ips')->filter(function ($value) {
            return !empty($value);
        })->toArray();
        $ipbuilding = [];
        foreach ($ipbuildings as $ip) {
            $ipbuilding = array_merge($ipbuilding, explode(',', $ip));
        }

        if (!empty($user->ips) || !empty($ipbuilding)) {
            $ips = explode(',', $user->ips);
            if ($user->building) {
                $ips = array_merge($ips, $ipbuilding);
            }
            if (!in_array($clientIP, $ips)) {

                if (!in_array($clientIP, $ips)) {

                    //return response(['message' => 'User not authorized from this location']);
                    throw new Exception("User not authorized from this location");
                }
                //return response(['message' => 'Location not authorized']);
                throw new Exception("Location not authorized");
            }
        }
    }
}
