<?php

namespace App\Http\Controllers;

use App\Models\BookAuthor;
use Exception;
use Illuminate\Http\Request;

class BookAuthorController extends Controller
{
    function index()
    {
        if (request()->ajax()) {
            $authors = BookAuthor::get();
            return datatables($authors)
                ->setRowId(function ($data) {
                    return $data->id;
                })
                ->addIndexColumn()
                ->addColumn('action', function () {
                    return '<div class="btn-group"><a class="btn btn-primary btn-sm btn-edit">Edit</a><a class="btn btn-danger btn-sm btn-delete">Delete</a></div>';
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('author');
    }

    function get($id)
    {
        $author = BookAuthor::find($id);
        return response()->json([
            'status'    => 200,
            'message'   => 'Success',
            'data'      => $author
        ]);
    }

    function store(Request $request)
    {
        $request->validate([
            'name'  => ['required', 'min:4']
        ]);
        try {

            $author = new BookAuthor();
            $author->name = request('name');
            $author->save();
            $res = [
                'status'    => 201,
                'message'   => 'Data created successfully'
            ];
        } catch (Exception $e) {
            $res = [
                'status'    => $e->getCode() ?? 400,
                'message'   => $e->getMessage() ?? 'Error'
            ];
        }
        return response()->json($res, $res['status']);
    }

    function update(Request $request, $id)
    {
        $request->validate([
            'name'  => ['required', 'min:4']
        ]);
        try {

            $author = BookAuthor::find($id);
            $author->name = request('name');
            $author->save();
            $res = [
                'status'    => 201,
                'message'   => 'Data updated successfully'
            ];
        } catch (Exception $e) {
            $res = [
                'status'    => $e->getCode() ?? 400,
                'message'   => $e->getMessage() ?? 'Error'
            ];
        }
        return response()->json($res, $res['status']);
    }

    function destroy($id)
    {
        try {
            $author = BookAuthor::find($id);
            $author->delete();
            $res = [
                'status'    => 201,
                'message'   => 'Data deleted successfully'
            ];
        } catch (Exception $e) {
            $res = [
                'status'    => $e->getCode() ?? 400,
                'message'   => $e->getMessage() ?? 'Error'
            ];
        }
        return response()->json($res, $res['status']);
    }

    function select2()
    {
        $author = BookAuthor::where('name','like','%'.request('q').'%')->get();
        return response()->json($author);
    }
}
