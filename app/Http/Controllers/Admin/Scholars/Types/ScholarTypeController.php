<?php

namespace App\Http\Controllers\Admin\Scholars\Types;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Scholars\ScholarTypeStoreRequest;
use Illuminate\Http\Request;

use App\Models\Scholars\ScholarType;

class ScholarTypeController extends Controller
{
    protected $indexView = 'admin.scholars.types.index';
    protected $createView = 'admin.scholars.types.create';
    protected $showView  = 'admin.scholars.types.show';
    protected $guard = 'admin';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view($this->indexView, [
            //
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->createView, [
            //
        ]);
    }

    public function store(ScholarTypeStoreRequest $request)
    {
        $item = ScholarType::store($request);

        $message = "You have successfully created {$item->name}";
        $redirect = $item->renderShowUrl();

        return response()->json([
            'message' => $message,
            'redirect' => $redirect,
        ]);
    }

    public function show($id, $slug = null)
    {
        $item = ScholarType::withTrashed()->findOrFail($id);

        return view($this->showView, [
            'item' => $item,
        ]);
    }

    public function update(ScholarTypeStoreRequest $request, $id)
    {
        $item = ScholarType::withTrashed()->findOrFail($id);
        $message = "You have successfully updated {$item->name}";

        $item = ScholarType::store($request, $item);

        return response()->json([
            'message' => $message,
        ]);
    }

    public function archive($id)
    {
        $item = ScholarType::withTrashed()->findOrFail($id);
        $item->archive();

        return response()->json([
            'message' => "You have successfully archived {$item->name}",
        ]);
    }

    public function restore($id)
    {
        $item = ScholarType::withTrashed()->findOrFail($id);
        $item->unarchive();

        return response()->json([
            'message' => "You have successfully restored {$item->name}",
        ]);
    }

    public function removeImage(Request $request, $id)
    {
        $item = ScholarType::withTrashed()->findOrFail($id);
        $message = "You have successfully remove the image in {$item->name}";

        $result = $item->removeImage($request);

        return response()->json([
            'message' => $message,
        ]);
    }
}
