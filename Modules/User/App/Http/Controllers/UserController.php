<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('user::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('user::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('user::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    public function photo(Request $request)
    {
        $request->validate(['photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5000']);
        $file = $request->file('photo');
        $user = $request->user();
        if($file->isValid())
        {
            // Define custom name for avatar
            $extension = $file->getClientOriginalExtension();
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = $originalName . '_' . time() . '_' . uniqid() . '.' . $extension;
            // Delete if avatar exist
            if($user->photo) { Storage::delete($user->photo); }
            // Upload photo
            $image_path = $request->file('photo')->storeAs('/uploads/user/avater', $fileName);
            // Update photo path into model
            $user->photo = $image_path;
            $user->save();
            return response(['message' => 'Photo update success!' ], Response::HTTP_OK);
        }

        return response(['message' => 'Unable to upload file.'], Response::HTTP_NOT_ACCEPTABLE);
    }
}
