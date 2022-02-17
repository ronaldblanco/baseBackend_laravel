<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Spatie\Fractal\Fractal;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use App\Transformers\OfficeTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class OfficeController extends Controller
{
    //
    public function index(Request $request)
    {
        //$this->authorize('index', Office::class);

        $query = Office::whereRaw('1=1');
        $limit = $request->has('limit') ? $request->get('limit') : false;
        $OfficeQuery = QueryBuilder::for($query);
        $OfficeQuery->allowedIncludes(['users']);

        $Offices = $OfficeQuery->get();


        $response = Fractal::create()
            ->collection($Offices)
            ->transformWith(new OfficeTransformer)
            //  ->includeUsers()
            ;

        if ($limit) {
            $paginator = $OfficeQuery->paginate($limit);
            $response->paginateWith(new IlluminatePaginatorAdapter($paginator));
        }


        return $response->toArray();
    }

    public function store(Request $request)
    {
        $Office = Office::create($request->all());

        if ($request->has('users')) {
            $Office->users()->sync($request->users); //Update users
        }

        return Fractal::create()
            ->item($Office)
            ->transformWith(new OfficeTransformer)
            // ->includeUsers()
            ->toArray();
    }

    /**
     * Display the specified resource.
     *
     * @param  @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Office = Office::findOrFail($id);

        //$this->authorize('show', $appointment);

        return Fractal::create()
            ->item($Office)
            ->transformWith(new OfficeTransformer)
            //->includeUsers()
            ->toArray();
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *  @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $Office = Office::findOrFail($id);
        //$this->authorize('update', $quote);

        $Office->update($request->all());
        if ($request->has('users')) {

            $Office->users()->sync($request->users); //Update Responsables

        }

        return Fractal::create()
            ->item($Office)
            ->transformWith(new OfficeTransformer)
            //  ->includeUsers()
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
        $Office = Office::findOrFail($id);
        //$this->authorize('destroy', $appointment);
        $Office->delete();

        return response()->json([
            "message" => "Office deleted"
        ], 202);
    }
}
