<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Task;
use App\History;

class TaskController extends Controller
{
    public function createTask(Request $request) {

        if (Task::where('key', $request->key)->exists()) {
            $result = Task::where('key', $request->key)->first()->toArray();
            $history = new History;
            $history->key = $result['key'];
            $history->value = $result['value'];
            $history->datetime = $result['datetime'];
            $history->save();

            $task = Task::where('key',$result['key'])->update(['value'=>$request->value,'datetime'=>strtotime(date("Y-m-d H:i:s"))]);

            $message = "Record has been updated";
        }else{
            $task = new Task;
            $task->key = $request->key;
            $task->value = $request->value;
            $task->datetime = strtotime(date("Y-m-d H:i:s"));
            $task->save();
            $message = "Record has been created";
        }

      return response()->json([
        "message" => $message
      ], 201);
    }

    public function getAllTasks() {
      $tasks = Task::get(['key','value','datetime'])->toJson(JSON_PRETTY_PRINT);
      return response($tasks, 200);
    }

    public function getTask($key) {
      if (Task::where('key', $key)->exists()) {
        $cond['key'] = $key;
        if(isset($_GET['timestamp'])){
            $cond['datetime'] = trim($_GET['timestamp']);
        }
        $task = Task::where($cond)->get(['key','value','datetime'])->toArray();
        if(empty($task) && isset($_GET['timestamp'])){
            $task = History::where($cond)->get(['key','value','datetime'])->toArray();
        }
        if(!empty($task)){
            foreach($task as $k => $val){
                $task[$k]['pretty_time'] = date('Y-m-d h:i A',$val['datetime']);
            }
        }
        return response($task, 200);
      } else {
        return response()->json([
          "message" => "record not found"
        ], 404);
      }
    }
}
