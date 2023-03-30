<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\UpdateFolderRequest;
use App\Models\Folder;
use App\Models\User;
use Auth;

class FoldersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($userId)
    {
        if ($userId !== Auth::user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $folders = User::with('folders')->find($userId);
        return response()->json($folders);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreFolderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFolderRequest $request)
    {
        $input = $request->all();
        $folder = Folder::create($input);

        $response = [
            'success' => true,
            'data' => $folder,
            'message' => 'Folder added Successfully'
        ];

        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function show(Folder $folder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function edit(Folder $folder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateFolderRequest  $request
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFolderRequest $request, $id)
    {
        $folder = Folder::find($id);
    
        if (!$folder) {
            $response = [
                'success' => false,
                'message' => 'Folder not found'
            ];
            return response()->json($response, 404);
        }
        if ($folder->user_id !== Auth::user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $input = $request->only(['foldername']);
        $folder->update($input);
    
        $response = [
            'success' => true,
            'data' => $folder,
            'message' => 'Folder updated successfully.'
        ];
    
        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $folder = Folder::find($id);

        if (!$folder) {
            $response = [
                'success' => false,
                'message' => 'Folder not found'
            ];
            return response()->json($response, 404);
        }

        if ($folder->user_id !== Auth::user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $folder->delete();

        $response = [
            'success' => true,
            'message' => 'Folder deleted successfully'
        ];

        return response()->json($response, 200);
    }
}
