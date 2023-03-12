<?php

namespace App\Http\Controllers;

use App\Models\WorkShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkShiftController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start' => 'required|date_format:Y-m-d H:i|after_or_equal:now',
            'end' => 'required|date_format:Y-m-d H:i|after:start',
        ]);
//        $validator = $request->validate([
//            'start' => 'required|date_format:Y-m-d H:i|after_or_equal:now',
//            'end' => 'required|date_format:Y-m-d H:i|after:start',
//        ]);

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
}
