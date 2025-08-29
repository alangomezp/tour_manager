<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\UpdateTourRequest;
use App\Http\Resources\TourResource;
use Illuminate\Http\Response;

class TourController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Tour::class);

        return TourResource::collection(Tour::all());
    }

    public function show(Tour $tour)
    {
        Gate::authorize('viewAny', Tour::class);

        return new TourResource($tour);
    }

    public function store(StoreTourRequest $request)
    {
        Gate::authorize('create', Tour::class);

        $tour = Tour::create($request->safe()->toArray());

        return response()->json([
            'message' => 'Tour created sucessfully'
        ], Response::HTTP_CREATED, ['resource' => route('tour', [$tour->id])]);
    }

    public function update(Tour $tour, UpdateTourRequest $request)
    {
        Gate::authorize('update', Tour::class);

        $tour->update($request->safe()->toArray());

        return response()->noContent(204, ['resource' => route('tour', [$tour->id])]);
    }

    public function destroy(Tour $tour)
    {
        Gate::authorize('delete', Tour::class);

        $tour->delete();

        return response()->noContent();
    }
}
