<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    function index()
    {
        if (request()->ajax()) {
            $users = User::get();
            return datatables($users)
                ->addIndexColumn()
                ->setRowId(function ($data) {
                    return $data->id;
                })
                ->addColumn('action', function ($data) {
                    $disabled = '';
                    if (auth()->user()->id == $data->id) {
                        $disabled = 'disabled';
                    }
                    return '<div class="btn-group"><a class="btn btn-primary btn-sm btn-edit '.$disabled.'">Edit</a><a class="btn btn-danger btn-sm btn-delete '.$disabled.'">Delete</a></div>';
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('user');
    }

    function get($id)
    {
        $user = User::find($id);
        return response()->json([
            'status'    => 200,
            'message'   => 'Success',
            'data'      => $user
        ]);
    }

    function store(Request $request)
    {
        $request->validate([
            'name'  => ['required', 'min:4']
        ]);
        try {

            $user = new User();
            $user->name = request('name');
            $user->email = request('email');
            $user->level = request('level');
            $user->save();
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

            $user = User::find($id);
            $user->name = request('name');
            $user->email = request('email');
            $user->level = request('level');
            $user->save();
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
            $user = User::find($id);
            $user->delete();
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
