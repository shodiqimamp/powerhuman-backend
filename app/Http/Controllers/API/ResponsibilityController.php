<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Responsibility;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateResponsibilityRequest;

class ResponsibilityController extends Controller
{

    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $responsibilityQuery = Responsibility::query();

        // Get Single Data
        if ($id) {
            $responsibility = $responsibilityQuery->find($id);

            if ($responsibility) {
                return ResponseFormatter::success($responsibility, 'Responsibility Found');
            }

            return ResponseFormatter::error('Responsibility Not Found', 404);
        }

        // Get Multiple Data
        $responsibilities = $responsibilityQuery->where('role_id', $request->role_id);

        if ($name) {
            $responsibilities->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $responsibilities->paginate($limit),
            'Responsibility Found'
        );
    }



    public function create(CreateResponsibilityRequest $request)
    {
        try {
            // Upload icon


            // Create responsibility
            $responsibility = Responsibility::create([
                'name' => $request->name,
                'role_id' => $request->role_id,
            ]);

            if (!$responsibility) {
                throw new Exception('Responsibility not created');
            }

            return ResponseFormatter::success($responsibility, 'Responsibility created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {

            // Get Responsibility
            $responsibility = Responsibility::find($id);

            //Check If Responsibility Exist
            if (!$responsibility) {
                throw new Exception('Responsibility Not Found');
            }

            // Delete Responsibility
            $responsibility->delete();
            return ResponseFormatter::success('Responsibility Deleted');
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }
}