<?php

namespace App\Helpers;

use App\Transformers\UserTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use PhpParser\Node\Stmt\Foreach_;
use Spatie\Fractal\Facades\Fractal;

class UrlHelper
{

    function urlQueryApplySearch($request, $entitiesQuery, $searchFields = null, $searchFieldsRelation = null)
    {
        $i = 0;
        $search = $request->has('search') ? $request->get('search') : false;
        if ($search != false) {
            $entitiesQuery->where(function ($q) use ($searchFieldsRelation, $search, $i, $searchFields) {
                if ($searchFields != null) {
                    foreach ($searchFields as $field) {
                        $q = $field == $searchFields[0] ? $q->where($field, 'LIKE', '%' . $search . '%') : $q->orWhere($field, 'LIKE', '%' . $search . '%');
                        $i++;
                    }
                }
                if ($searchFieldsRelation != null) {

                    foreach ($searchFieldsRelation as $key => $value) {
                        if ($i == 0) {
                            $q->WhereHas($key, function ($query) use ($search, $value) {
                                $query->where($value, 'like', '%' . $search . '%');
                            });
                        } else {
                            $q->orWhereHas($key, function ($query) use ($search, $value) {
                                $query->where($value, 'like', '%' . $search . '%');
                            });
                        }
                        $i++;
                    }
                }
            });
        }
        //dd($entitiesQuery->toSql());
        return $entitiesQuery;
    }

    function urlQueryApplySearch2($request, $entitiesQuery, $searchFields = null, $searchFieldsRelation = null)
    {
        $i = 0;
        $search = $request->has('search') ? $request->get('search') : false;
        if ($search != false) {
            $entitiesQuery->where(function ($q) use ($searchFieldsRelation, $search, $i, $searchFields) {
                if ($searchFields != null) {
                    foreach ($searchFields as $field) {
                        $q = $field == $searchFields[0] ? $q->where($field, 'LIKE', '%' . $search . '%') : $q->orWhere($field, 'LIKE', '%' . $search . '%');
                        $i++;
                    }
                }
                if ($searchFieldsRelation != null) {

                    foreach ($searchFieldsRelation as $key => $value) {
                        if ($i == 0) {
                            $q->WhereHas($key, function ($query) use ($search, $value) {
                                $query->where($value, 'like', '%' . $search . '%');
                            });
                        } else {
                            $q->orWhereHas($key, function ($query) use ($search, $value) {
                                $query->where($value, 'like', '%' . $search . '%');
                            });
                        }
                        $i++;
                    }
                }
            });
        }
        //dd($entitiesQuery->toSql());
        return $entitiesQuery;
    }

    function urlQueryApplyPaginator($request, $entitiesQuery)
    {
        $limit = $request->has('limit') ? $request->get('limit') : false;
        $paginator = $limit ? $entitiesQuery->paginate($limit) : null;
        return $paginator;
    }

    function resultPaginate($res, $paginator)
    {
        $res = $paginator ? $res->paginateWith(new IlluminatePaginatorAdapter($paginator)) : $res;
        return $res;
    }



    function responseError($msg = null)
    {
        $msg = $msg == null ? "ERROR" : $msg;
        return response()->json([
            "message" => $msg
        ], 409);
    }

    function responseOK($msg = null)
    {
        $msg = $msg == null ? "" : $msg;
        return response()->json([
            "message" => $msg
        ], 202);
    }

    function getUserAuth()
    {
        $user = request()->user();
        $userObj = Fractal::create()
            ->item($user)
            ->transformWith(new UserTransformer)
            ->includeOrganization()->toArray();
        return $userObj;
    }

    function getOrganizationId()
    {
        $user = $this->getUserAuth();
        return $user['data']['organization_id'];
    }

    function getUserAuthId()
    {
        $user = $this->getUserAuth();
        return $user['data']['id'];
    }

    function urlQueryApplyGlobalParams($entitiesQuery, $global_required_params, $global_required_params_relation = null)
    {
        $organizationId = $this->getOrganizationId();
        if ($global_required_params != null) {
            foreach ($global_required_params as $keyIt => $valueIt) {
                $key = $keyIt;
                $value = $valueIt;
                if ($keyIt == 'organization_id' || $valueIt == 'organization_id') {
                    $key = $valueIt;
                    $value = $organizationId;
                }
                $entitiesQuery = $entitiesQuery->where($key, '=', $value);
            }
        }
        if ($global_required_params_relation != null) {
            foreach ($global_required_params_relation as $key => $value) {
                $entitiesQuery->WhereHas($key, function ($query) use ($organizationId, $value) {
                    if ($value == 'organization_id') {
                        $query->where($value, '=', $organizationId);
                    }
                });
            }
        }
        return $entitiesQuery;
    }

    function fillObjectWithGlobalParams($object, $global_required_params = null)
    {
        //$obj = $request->all();
        if ($global_required_params) {
            foreach ($global_required_params as $key => $value) {
                switch ($value) {
                    case 'organization_id':
                        $object['organization_id'] = $this->getOrganizationId();
                        break;
                    default:
                        break;
                }
            }
        }
        return $object;
    }
}
