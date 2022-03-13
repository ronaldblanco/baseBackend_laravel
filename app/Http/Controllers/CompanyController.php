<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use App\Models\Company;
use Spatie\Fractal\Fractal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Transformers\UserTransformer;
use Spatie\QueryBuilder\QueryBuilder;
use App\Transformers\OfficeTransformer;
use App\Transformers\CompanyTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class CompanyController extends Controller
{
    //
    /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $this->authorize('index', Company::class);

    $query = Company::whereRaw('1=1'); //to run pure SQL querys! return all records in this case!
    //dd($query);
    $limit = $request->has('limit') ? $request->get('limit') : false;
    $CompanysQuery = QueryBuilder::for($query);

    $CompanysQuery->allowedFilters(['name']);
    $CompanysQuery->allowedIncludes(['departments']);
    $CompanysQuery->defaultSort('name');
    $CompanysQuery->allowedSorts(['name']);


    $Companys = $CompanysQuery->get();


    $response = Fractal::create()
      ->collection($Companys)
      ->transformWith(new CompanyTransformer)
      ;

    if ($limit) {
      $paginator = $CompanysQuery->paginate($limit);
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
    $this->authorize('store', Company::class);
    $Company = Company::create($request->all());

    /*if ($request->hasFile("logo")) {
      $Company->addMediaFromRequest('logo')->toMediaCollection("logo");
    }

    if ($request->hasFile("printLogo")) {
      $Company->addMediaFromRequest('printLogo')->toMediaCollection("print_logo");
    }*/

    return Fractal::create()
      ->item($Company)
      ->transformWith(new CompanyTransformer)
      ->toJson();
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $Company = Company::findOrFail($id);
    $this->authorize('show', $Company);

    return Fractal::create()
      ->item($Company)
      ->transformWith(new CompanyTransformer)
      
      ->toJson();
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
    $Company = Company::findOrFail($id);
    $this->authorize('update', $Company);
    $Company->update($request->all());

    if ($request->hasFile("printLogo")) {
      $Company->addMediaFromRequest('printLogo')->toMediaCollection("print_logo");
    }
    if ($request->hasFile("logo")) {
      $Company->addMediaFromRequest('logo')->toMediaCollection("logo");
    }
    return Fractal::create()
      ->item($Company)
      ->transformWith(new CompanyTransformer)
      
      ->toJson();
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $Company = Company::findOrFail($id);
    $this->authorize('destroy', Company::class);
    $Company->delete();

    return response()->json([
      "message" => "Company deleted"
    ], 202);
  }

  public function show_all_users(Request $request, $id)
  {
    $user = Auth::user();
    $company = Company::findOrFail($id);
    $this->authorize('user_read', $company);

    $query = User::where('users.company_id', $id)->get();

    //return $query;
    return Fractal::create()
      ->collection($query)
      ->transformWith(new UserTransformer)
      
      ->toJson();

  }

  public function show_all_offices(Request $request, $id)
  {
    $user = Auth::user();
    $company = Company::findOrFail($id);
    $this->authorize('user_read', $company);

    $query = Office::where('offices.company_id', $id)->get();

    //return $query;
    return Fractal::create()
      ->collection($query)
      ->transformWith(new OfficeTransformer)
      
      ->toJson();

  }

}
