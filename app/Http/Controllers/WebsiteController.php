<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Rules\DomainName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(
        Request $request
    ) {
        $websites = Website::paginate(
            $request->input('per_page', 10)
        );

        return response()->json($websites);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'endpoint' => [
                'required',
                new DomainName(),
                'unique:websites,endpoint',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $validated = $validator->validated();
        $validated = $validator->safe()->only(['name', 'endpoint']);
        $website = Website::create($validated);
        return response()->json($website, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $website = Website::findOrFail($id);
        return response()->json($website);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $website = Website::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'endpoint' => [
                'required',
                new DomainName(),
                'unique:websites,endpoint,' . $website->id,
            ],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $validated = $validator->validated();
        $validated = $validator->safe()->only(['name', 'endpoint']);
        $website->update($validated);
        return response()->json($website);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $website = Website::findOrFail($id);
        $website->delete();
        return response()->json(null, 204);
    }
}
