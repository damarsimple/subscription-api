<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(
        Request $request
    ) {
        $subscribers = Subscriber::paginate(
            $request->input('per_page', 10)
        );
        return response()->json($subscribers);
    }

    public function addWebsite(Request $request, $id)
    {

        $subscriber = Subscriber::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'website_id' => 'required|exists:websites,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $validated = $validator->validated();
        $validated = $validator->safe()->only(['id', 'website_id']);
        $subscriber->websites()->attach($validated['website_id']);
        return response()->json($subscriber, 201);
    }

    public function addWebsites(Request $request, $id)
    {
        $subscriber = Subscriber::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'website_ids' => 'required|array',
            'website_ids.*' => 'required|exists:websites,id',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $validated = $validator->validated();
        $validated = $validator->safe()->only(['id', 'website_ids']);
        $subscriber->websites()->attach($validated['website_ids']);
        return response()->json($subscriber, 201);
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
            'email' => 'required|email|unique:subscribers,email',
            'name' => 'required|max:255',
            'website_ids' => 'array',
            'website_ids.*' => 'exists:websites,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $validated = $validator->validated();
        $validated = $validator->safe()->only(['email', 'name', 'website_ids']);

        $subscriber = Subscriber::create($validated);

        $subscriber->websites()->attach($validated['website_ids']);

        // reset website subscribers cache

        foreach ($validated['website_ids'] as $websiteId) {
            Website::dropCachedSubscribers($websiteId);
        }

        return response()->json($subscriber, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $subscriber = Subscriber::with('websites')->findOrFail($id);
        return response()->json($subscriber);
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
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:subscribers,email,' . $id,
            'name' => 'required|max:255',
            'website_ids' => 'array',
            'website_ids.*' => 'exists:websites,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $validated = $validator->validated();
        $validated = $validator->safe()->only(['email', 'name', 'website_ids']);

        $subscriber = Subscriber::findOrFail($id);
        $subscriber->update($validated);
        $subscriber->websites()->sync($validated['website_ids']);

        // reset website subscribers cache

        foreach ($validated['websites'] as $websiteId) {
            Website::dropCachedSubscribers($websiteId);
        }


        return response()->json($subscriber, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subscriber = Subscriber::findOrFail($id);
        $subscriber->delete();

        // reset website subscribers cache

        foreach ($subscriber->websites()->pluck('id') as $websiteId) {
            Website::dropCachedSubscribers($websiteId);
        }


        return response()->json(null, 204);
    }
}
