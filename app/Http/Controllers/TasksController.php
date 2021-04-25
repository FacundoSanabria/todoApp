<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Status;
use Illuminate\Support\Facades\DB;


class TasksController extends Controller
{
    public function index(){
        $tasks = DB::table('tasks')
                ->join('statuses', 'tasks.status_id', '=', 'statuses.status_id')    
                ->where('tasks.user_id','=', auth()->user()->id)
                ->get();
        error_log($tasks);
        return view('dashboard', compact('tasks'));
    }

    public function add(){
        $statuses = Status::all();
        return view('add', compact('statuses'));
    }

    public function create(Request $request){
        $this->validate($request, [
            'description' => 'required',
            'status_id' => 'required'
        ]);
        $task = new Task();
        $task->description = $request->description;
        $task->user_id = auth()->user()->id;
        $task->status_id = $request->status_id;
        $task->save();
        return redirect('/dashboard');
    }
    
    public function edit(Task $task){
        if (auth()->user()->id == $task->user_id){            
            return view('edit', compact('task'));
        }           
        else {
            return redirect('/dashboard');
        }   
    }

    public function update(Request $request, Task $task){
        if(isset($_POST['delete'])) {
    		$task->delete();
    		return redirect('/dashboard');
    	}
    	else
    	{
    		$task->description = $request->description;
	    	$task->status_id = $request->status_id;
            $task->save();
	    	return redirect('/dashboard'); 
    	}    	
    }
}
