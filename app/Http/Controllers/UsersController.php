<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Helpers\UrlHelper;
use Spatie\Fractal\Fractal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Transformers\RoleTransformer;
use App\Transformers\UserTransformer;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Config;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Database\QueryException;
use App\Classes\Firebase\FirebaseFacade;
use App\Transformers\PermissionTransformer;
use App\Classes\VoipNotification\VoipNotificationFacade;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Spatie\Permission\Models\Permission;

class UsersController extends Controller
{
    //
    private $urlHelper;


    public function __construct(UrlHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $this->authorize('index', User::class);

        $query = User::whereRaw('1=1'); //to run pure SQL querys! return all records in this case!

        $limit = $request->has('limit') ? $request->get('limit') : false;
        $usersQuery = QueryBuilder::for($query);

        $usersQuery->allowedFilters(['fname']);
        //$usersQuery->allowedIncludes(['departments']);
        $usersQuery->defaultSort('fname');
        $usersQuery->allowedSorts(['fname', 'lname']);


        $users = $usersQuery->get();

        $response = Fractal::create()
            ->collection($users)
            ->transformWith(new UserTransformer)
            ->includeRoles()
            ->includeGroups();

        if ($limit) {
            $paginator = $usersQuery->paginate($limit);
            $response->paginateWith(new IlluminatePaginatorAdapter($paginator));
        }


        return $response->toArray();
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //dd($request);
        $request["password"] = bcrypt($request->password);
        //$company = company::findOrFail($request->company_id);

        $policy = array(User::class, $request->company_id);
        $this->authorize('store', $policy);
        try {
            //dd($request->all());
            $user = User::create($request->all());
            //dd($user);
            if ($request->has('roles')) {
                $user->syncRoles(/*json_decode(*/$request->roles/*)*/);
            }

            /*$languages = Language::where("company_id", $user->company_id)->get();
            $user->languages()->sync([]);
            $temp = [];
            foreach ($languages as $language) {
                if ($request->has($language->name) && $request[$language->name] > 0) {
                    array_push($temp, $language->id);
                }
            }
            $user->languages()->sync($temp);*/

            //return $user;
            return Fractal::create()
                ->item($user)
                ->transformWith(new UserTransformer)
                ->includeRoles()

                ->toArray();
        } catch (QueryException  $e) {
            $code = $e->getCode();
            if ($code == 23000) {
                return response()->json(['error' => 'user duplicated'], 409);
            }
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function import_store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {



        $query = User::where('id', $id);
        $usersQuery = QueryBuilder::for($query);
        $usersQuery->allowedIncludes(['tasks']);
        $user = $usersQuery->first();

        //dd($user->company_id);

        //$user = User::findOrFail($id);
        $this->authorize('show', $user);

        return Fractal::create()
            ->item($user)
            ->transformWith(new UserTransformer)
            ->includeRoles()

            ->toArray();
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /*$role = Role::findByName('user_manage');
        dd($role);*/
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        try {
            if ($request->has("password"))
                $request["password"] = bcrypt($request->password);

            if ($request->has('roles')) {
                $user->syncRoles(/*json_decode(*/$request->roles/* )*/);
            }

            // was blocking is_professional, check it out
            //$payload = array_filter($request->all());

            $user->update($request->all());
        } catch (QueryException  $e) {
            $code = $e->getCode();
            if ($code == 23000) {
                return response()->json(['error' => 'user duplicated'], 409);
            }
            return response()->json(['error' => $e->getMessage()], 500);
        }

        if ($request->has('out') && $request->out > 0) {
            $myGroups = $user->groups()->get();
            $tmp = [];
            foreach ($myGroups as $group) {
                if ($group->id != $request->out) array_push($tmp, $group);
            }
            if (count($tmp) > 0) $user->groups()->sync($tmp);
            else $user->groups()->sync([67]); //set to UnassignedGroup
            unset($tmp);
        }

        if ($request->hasFile("avatar")) {
            $user->addMediaFromRequest('avatar')->toMediaCollection("avatar");
        }

        if ($request->has('multifields')) {

            $dbmultifields = $user->multifields()->get(['id']);

            $dbmultifieldids = array();
            foreach ($dbmultifields as $dbmultifield) {
                array_push($dbmultifieldids, $dbmultifield['id']);
            }

            $remultifieldids = array();
            foreach ($request->multifields as $multifield) {
                array_push($remultifieldids, $multifield['id']);
            }

            foreach ($dbmultifields as $dbmultifield) {
                if (array_search($dbmultifield['id'], $remultifieldids) === false) { //To be Deleted
                    $user->multifields()->findOrFail($dbmultifield['id'])->delete();
                }
            }

            $user = User::findOrFail($id); //Refresh the user

            foreach ($request->multifields as $multifield) {
                if ($multifield['id'] > 0 && array_search($multifield['id'], $dbmultifieldids) !== false) { //To be UPDATE
                    $user->multifields()->findOrFail($multifield['id'])->update($multifield);
                } else if ($multifield['id'] == 0) { //NEW to be Created
                    $user->multifields()->create($multifield);
                }
            }

            $user = User::findOrFail($id); //Refresh the user

        }


        /*$languages = Language::where("company_id", $user->company_id)->get();
        $user->languages()->sync([]);
        $temp = [];
        foreach ($languages as $language) {
            if ($request->has($language->name) && $request[$language->name] > 0) {
                array_push($temp, $language->id);
            }
        }
        $user->languages()->sync($temp);*/

        return Fractal::create()
            ->item($user)
            ->transformWith(new UserTransformer)
            ->includeRoles()

            ->toArray();
    }

    public function update_profile(Request $request) //User can update some user properties
    {
        $user = Auth::user();
        //$this->authorize('update_profile', $user);
        if ($request->has("password") && $request->get("passport") != "")
            $request["password"] = bcrypt($request->password);

        $user->update($request->except(['company_id', 'office_id']));

        /*if ($request->hasFile("avatar")) {
            $user->addMediaFromRequest('avatar')->toMediaCollection("avatar");
        }*/
        return Fractal::create()
            ->item($user)
            ->transformWith(new UserTransformer)
            ->includeCompany()
            ->includeRoles()

            ->toArray();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('destroy', $user);
        if ($id != 1) {
            $user->update(['active' => false]);
            $user->delete();

            return response()->json([
                "message" => "user deleted"
            ], 202);
        } else return response()->json([
            "message" => "main user id = 1 can not be deleted"
        ], 402);
    }

    public function show_user_roles(Request $request, $id)
    {

        $user = User::findOrFail($id);
        $this->authorize('show_user_roles', $user);

        return Fractal::create()
            ->collection($user->roles)
            ->transformWith(new RoleTransformer)
            ->toArray();
    }

    public function show_all_roles(Request $request, $id)
    {

        $roles = Role::all();
        $user = User::findOrFail($id);
        $this->authorize('show_user_roles', $user);

        return Fractal::create()
            ->collection($roles)
            ->transformWith(new RoleTransformer)
            ->includePermissions()
            ->toArray();
    }


    public function give_role(Request $request, $id)
    {

        $user = User::findOrFail($id);
        $this->authorize('give_role', $user);
        $role = $request->get('role');
        $user->assignRole($role);

        return Fractal::create()
            ->collection($user->roles)
            ->transformWith(new RoleTransformer)
            ->toArray();
    }


    public function revoke_role($id, $roleId)
    {

        $user = User::findOrFail($id);
        $this->authorize('revoke_role', $user);

        $role = Role::findOrFail($roleId);
        $user->removeRole($role);

        return Fractal::create()
            ->collection($user->roles)
            ->transformWith(new RoleTransformer)
            ->toArray();
    }

    public function sync_roles(Request $request, $id)
    {

        $user = User::findOrFail($id);
        $this->authorize('sync_roles', $user);
        $roles = $request->get('roles'); //Array

        $user->syncRoles($roles);

        return Fractal::create()
            ->collection($user->roles)
            ->transformWith(new RoleTransformer)
            ->toArray();
    }

    public function show_user_permissions($id)
    {

        $user = User::findOrFail($id);
        $this->authorize('show_user_permissions', $user);

        return Fractal::create()
            ->collection($user->getAllPermissions())
            ->transformWith(new PermissionTransformer)
            ->toArray();
    }

    public function index_all_permissions(Request $request, $id)
    {
        $permissions = Permission::all()
            ->groupBy('module_name');

        return response()->json($permissions);
    }
}
