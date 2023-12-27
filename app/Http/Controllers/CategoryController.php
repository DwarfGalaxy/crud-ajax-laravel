<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function home()
    {
        return view('admin.category.index');
    }

    public function index(Request $request)
    {
        $categories = Category::select('id', 'name', 'type')->latest();
        if ($request->ajax()) {
            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $viewUrl = route('single.category', ['id' => $row->id]);
                    return '<a href="javascript:void(0)" class="btn btn-info editButton" data-id="' . $row->id . '">Edit</a> 
                    <a href="javascript:void(0)" class="btn btn-danger delButton" data-id="' . $row->id . '">Delete</a> 
                    <a href="' . $viewUrl . '" class="btn btn-success">View</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function singleCategory(string $id)
    {
        $category = Category::select('id', 'name', 'type')->where('id', $id)->first();
        return view('admin.category.view', ['category' => $category]);
    }

    public function store(CategoryRequest $request)
    {
        if ($request->category_id != null) {
            $category = Category::find($request->category_id);
            if (!$category) {
                abort(404);
            }
            $category->update([
                'name' => $request->name,
                'type' => $request->type
            ]);
            return response()->json([
                'success' => 'Category Updated Successfully'
            ], 201);
        } else {
            Category::create([
                'name' => $request->name,
                'type' => $request->type
            ]);

            return response()->json([
                'success' => 'Category Saved Successfully'
            ], 201);
        }
    }

    public function edit(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            abort(404);
        }
        return $category;
    }

    public function destroy(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            abort(404);
        }
        $category->delete();
        return response()->json([
            'success' => 'Category Deleted Successfully'
        ], 201);
    }
}
