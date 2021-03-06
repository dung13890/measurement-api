<?php

namespace App\Http\Controllers;

use App\Device;
use App\Measurement;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    protected $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(\App\Repositories\MeasurementRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return $this->repository->responsePagination(Measurement::paginate());
    }

    public function show($id)
    {
        try {
            $item = Measurement::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Measurement not found');
        }

        return $this->repository->responseItem($item);
    }

    public function device($id)
    {
        try {
            $item = Measurement::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Measurement not found');
        }
        return $this->repository->responseItem($item, ['device']);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'humidity' => 'required|numeric',
            'temperature' => 'required|numeric',
            'device_id' => 'required|exists:devices,id'
        ]);

        $device = Device::findOrFail($request->input('device_id'));
        if (\Gate::denies('store-device-measurements', $device)) {
            abort(403, 'Permission insufficient');
        }

        $item = $device->measurements()->create($request->all());

        return response()->json($this->repository->responseItem($item), 201, [
            'Location' => route('measurement.show', ['id' => $item->id])
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $item = Measurement::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Measurement not found');
        }

        $this->validate($request, [
            'humidity' => 'sometimes|required|numeric',
            'temperature' => 'sometimes|required|numeric',
            'device_id' => 'sometimes|required|exists:devices,id'
        ]);

        if (\Gate::denies('update-device-measurements', $item->device)) {
            abort(403, 'Permission insufficient');
        }

        $item->fill($request->all());
        $item->save();

        return $this->repository->responseItem($item);
    }

    public function destroy(Request $request, $id)
    {
        try {
            $item = Measurement::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Measurement not found');
        }

        if (\Gate::denies('destroy-device-measurements', $item->device)) {
            abort(403, 'Permission insufficient');
        }

        $item->delete();

        return $this->repository->responseItem($item);
    }
}
