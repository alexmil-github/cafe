<?php

namespace App\Http\Controllers;

use App\Models\ShiftWorker;
use App\Models\User;
use App\Models\WorkShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WorkShiftController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start' => 'required|date_format:Y-m-d H:i|after_or_equal:now',
            'end' => 'required|date_format:Y-m-d H:i|after:start',
        ]);

        if($validator->fails())
            return response()->json([
                'error' => [
                    'code' => 422,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ]
            ],422);


        return WorkShift::create($request->all());
    }

    public function open($id)
    {
        $opens = WorkShift::where([
            'active' => 1
        ])->get();

        if (count($opens) != 0) {
            return response()->json([
                'error' => [
                    'code' => 403,
                    'message' => 'Forbidden. There are open shifts!'
                ]
            ], 403);
        }
        $work_shift = WorkShift::find($id);
        $work_shift->active = 1;
        $work_shift->save();

        return [
          'data' => [
              'id' => $work_shift->id,
              'start' => $work_shift->start,
              'end' => $work_shift->end,
              'active' => 'true'
          ]
        ];

    }

    public function close($id)
    {
        $work_shift = WorkShift::find($id);
        if ($work_shift->active == 0) {
            return response()->json([
               'error' => [
                   'code' => 403,
                   'message' => 'Forbidden. The shift is already closed!'
               ]
            ]);
        }

        $work_shift->active = 0;
        $work_shift->save();

        return [
            'data' => [
                'id' => $work_shift->id,
                'start' => $work_shift->start,
                'end' => $work_shift->end,
                'active' => 'false'
            ]
        ];
    }

    public function addUser(Request $request, $id)
    {
        $working = User::where(['status' => 'working'])->pluck('id');

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id', Rule::in($working)],
        ]);

        if($validator->fails())
            return response()->json([
                'error' => [
                    'code' => 422,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ]
            ],422);

        $workshift = WorkShift::find($id);

        ShiftWorker::create([
            'work_shift_id' => $workshift->id,
            'user_id' => $request->user_id
        ]);

        return response()->json([
            'data' => [
                'id_user' => $request->user_id,
                'status' => 'added'
            ]
        ])->setStatusCode(201);
    }
}
