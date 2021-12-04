<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookController extends Controller
{
    function index()
    {
        if (request()->ajax()) {
            $books = Book::with(['author'])->get();

            return datatables($books)
                ->addIndexColumn()
                ->setRowId(function ($data) {
                    return $data->id;
                })
                ->addCOlumn('cover', function ($data) {
                    return '<img height="150" src="' . asset('img/cover/' . $data->cover) . '">';
                })
                ->addCOlumn('action', function () {
                    return '<div class="btn-group"><a class="btn btn-primary btn-sm btn-edit">Edit</a><a class="btn btn-danger btn-sm btn-delete">Delete</a></div>';
                })
                ->addCOlumn('author', function ($data) {
                    return $data->author->name;
                })
                ->rawColumns(['cover', 'action'])
                ->toJson();
        }
        return view('book');
    }

    function get($id)
    {
        $book = Book::with('author')->find($id);
        return response()->json([
            'status'    => 200,
            'message'   => 'Success',
            'data'      => $book
        ]);
    }

    function store(Request $request)
    {
        $request->validate([
            'title'  => ['required', 'min:4'],
            'book_author_id' => ['required'],
            'description'   => ['required', 'min:10'],
            'cover'  => ['required', 'file', 'max:1024'],
        ]);
        try {

            $book = new Book();
            $book->title = request('title');
            $book->book_author_id = request('book_author_id');
            $book->description = request('description');

            $cover = $request->cover;
            $file_name = date('Ymd') . rand(0, 9999) . Str::slug(request('title'), '-') . '.' . request('cover')->getClientOriginalExtension();
            if (!$cover->move('img/cover/', $file_name)) {
                throw new Exception('Failed while moving cover image', 400);
            }
            $book->cover = $file_name;
            $book->save();
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
            'title'  => ['required', 'min:4'],
            'book_author_id' => ['required'],
            'description'   => ['required', 'min:10'],
            'cover'  => ['file', 'max:1024'],
        ]);
        try {

            $book = Book::find($id);
            $book->title = request('title');
            $book->book_author_id = request('book_author_id');
            $book->description = request('description');
            if (request()->has('cover')) {

                if ($book->cover)
                    if (file_exists('img/cover/' . $book->cover))
                        unlink('img/cover/' . $book->cover);

                $cover = $request->cover;
                $file_name = date('Ymd') . rand(0, 9999) . Str::slug(request('title'), '-') . '.' . request('cover')->getClientOriginalExtension();
                if (!$cover->move('img/cover/', $file_name)) {
                    throw new Exception('Failed while moving cover image', 400);
                }
                $book->cover = $file_name;
            }
            $book->save();
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
            $book = Book::find($id);
            $book->delete();
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
}
