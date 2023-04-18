<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;

class TeamController extends Controller
{

    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $teamQuery = Team::withCount('employees');

        // Get Single Data
        if ($id) {
            $team = $teamQuery->find($id);

            if ($team) {
                return ResponseFormatter::success($team, 'Team Found');
            }

            return ResponseFormatter::error('Team Not Found', 404);
        }

        // Get Multiple Data
        $teams = $teamQuery->where('company_id', $request->company_id);

        if ($name) {
            $teams->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $teams->paginate($limit),
            'Team Found'
        );
    }



    public function create(CreateTeamRequest $request)
    {
        try {
            // Upload icon
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // Create team
            $team = Team::create([
                'name' => $request->name,
                'icon' => isset($path) ? $path : '',
                'company_id' => $request->company_id,
            ]);

            if (!$team) {
                throw new Exception('Team not created');
            }

            return ResponseFormatter::success($team, 'Team created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }


    public function update(UpdateTeamRequest $request, $id)
    {

        try {
            // Get team
            $team = Team::find($id);

            // Check if team exists
            if (!$team) {
                throw new Exception('Team Not Found');
            }

            // Upload icon
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // Update team
            $team->update([
                'name' => $request->name,
                'icon' => isset($path) ? $path : $team->icon,
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success($team, 'Team Updated');
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getPrevious(), 500);
        }
    }

    public function destroy($id)
    {
        try {

            // Get Team
            $team = Team::find($id);

            //Check If Team Exist
            if (!$team) {
                throw new Exception('Team Not Found');
            }

            // Delete Team
            $team->delete();
            return ResponseFormatter::success('Team Deleted');
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 500);
        }
    }
}