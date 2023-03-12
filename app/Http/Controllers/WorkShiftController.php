<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkShiftRequest;
use App\Models\WorkShift;
use Illuminate\Http\Request;

class WorkShiftController extends Controller
{
    public function store(WorkShiftRequest $request)
    {
        return WorkShift::create($request->all());
    }

    public function open($id)
    {

        $work_shift = WorkShift::find($id);

    }
}
