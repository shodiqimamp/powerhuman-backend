<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{

    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $with_responsibilities = $request->input('with_responsibilities', false);

        $roleQuery = Role::withCount('employees');

        // Get Single Data
        if ($id) {
            $role = $roleQuery->with('responsibilities')->find($id);

            if ($role) {
                return ResponseFormatter::success($role, 'Role Found');
            }

            return ResponseFormatter::error('Role Not Found', 404);
        }

        // Get Multiple Data
        $roles = $roleQuery->where('company_id', $request->company_id);

        if ($name) {
            $roles->where('name', 'like', '%' . $name . '%');
        }

        if ($with_responsibilities) {
            $roles->with('responsibilities');
        }

        return ResponseFormatter::success(
            $roles->paginate($limit),
            'Role Found'
        );
    }



    public function create(CreateRoleRequest $request)
    {
        try {
            // Upload icon


            // Create role
            $role = Role::create([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            if (!$role) {
                throw new Exception('Role not created');
            }

            return ResponseFormatter::success($role, 'Role created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }


    public function update(UpdateRoleRequest $request, $id)
    {

        try {
            // Get role
            $role = Role::find($id);

            // Check if role exists
            if (!$role) {
                throw new Exception('Role Not Found');
            }

            // Update role
            $role->update([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success($role, 'Role Updated');
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getPrevious(), 500);
        }
    }

    public function destroy($id)
    {
        try {

            // Get Role
            $role = Role::find($id);

            //Check If Role Exist
            if (!$role) {
                throw new Exception('Role Not Found');
            }

            // Delete Role
            $role->delete();
            return ResponseFormatter::success('Role Deleted');
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }
}