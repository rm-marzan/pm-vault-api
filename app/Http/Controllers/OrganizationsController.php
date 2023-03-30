<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Models\Organization;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;

class OrganizationsController extends Controller
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

        $organization = User::with('organizations')->find($userId);

        return response()->json($organization);
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
     * @param  \App\Http\Requests\StoreOrganizationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrganizationRequest $request)
    {
        $input = $request->all();
        $organization = Organization::create($input);

        $response = [
            'success' => true,
            'data' => $organization,
            'message' => 'Organization added Successfully'
        ];

        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function edit(Organization $organization)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrganizationRequest  $request
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrganizationRequest $request, $id)
    {
        $organization = Organization::find($id);

        if (!$organization) {
            $response = [
                'success' => false,
                'message' => 'Organization not found'
            ];
            return response()->json($response, 404);
        }
        if ($organization->user_id !== Auth::user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        $input = $request->only(['orgname','email','details']);
        $organization->update($input);
    
        $response = [
            'success' => true,
            'data' => $organization,
            'message' => 'Organization updated successfully.'
        ];
    
        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $organization = Organization::find($id);
        
        if (!$organization) {
            $response = [
                'success' => false,
                'message' => 'Organization not found'
            ];
            return response()->json($response, 404);
        }

        if ($organization->user_id !== Auth::user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $organization->delete();

        $response = [
            'success' => true,
            'message' => 'Organization deleted successfully'
        ];

        return response()->json($response, 200);
    }
}
