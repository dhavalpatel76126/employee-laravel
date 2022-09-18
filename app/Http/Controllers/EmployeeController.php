<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // retrive employee data
        $employeeData = Employee::get();
        return $employeeData;
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //insert data into table
        $request->validate([
            'name'=>'required',
            'email'=>'required',
            'age'=>'required',
            'image'=>'required|image'
        ]);

        try{
            $imageName = Str::random().'.'.$request->image->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('employee/image', $request->image,$imageName);
            Employee::create($request->post()+['image'=>$imageName]);

            return response()->json([
                'message'=>'Employee Created Successfully!!'
            ]);
        }catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while creating a employee!!'
            ],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        //
        $employeeData = $employee;
        return response()->json([
            'employee'=>$employeeData
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        //
        $request->validate([
            'name'=>'required',
            'email'=>'required',
            'age'=>'required',
        ]);

        try{

            $employee->fill($request->post())->update();

            if($request->hasFile('image')){

                // remove old image
                if($employee->image){
                    $exists = Storage::disk('public')->exists("employee/image/{$employee->image}");
                    if($exists){
                        Storage::disk('public')->delete("employee/image/{$employee->image}");
                    }
                }

                $imageName = Str::random().'.'.$request->image->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('employee/image', $request->image,$imageName);
                $employee->image = $imageName;
                $employee->save();
            }

            return response()->json([
                'message'=>'Employee Updated Successfully!!'
            ]);

        }catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while updating a employee!!'
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        //
        try {

            if($employee->image){
                $exists = Storage::disk('public')->exists("employee/image/{$employee->image}");
                if($exists){
                    Storage::disk('public')->delete("employee/image/{$employee->image}");
                }
            }

            $employee->delete();

            return response()->json([
                'message'=>'Employee Deleted Successfully!!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while deleting a employee!!'
            ]);
        }
    
    }
}
