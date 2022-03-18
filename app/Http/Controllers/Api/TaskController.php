<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Task;
use App\History;

define("STATUS_200", 200);
define("STATUS_404", 400);
define("STATUS_201", 201);

class TaskController extends Controller
{
    public function createTask(Request $request) {
        $message = "Record has been created";
        $result = Task::where('key', $request->key)->first()->toArray();
        if(!empty($result)){
            $this->history($result);
            $task = Task::where('key',$result['key'])->update(['value'=>$request->value,'datetime'=>strtotime(date("Y-m-d H:i:s"))]);
            $message = "Record has been updated";
        }else{
            $task = new Task;
            $task->key = $request->key;
            $task->value = $request->value;
            $task->datetime = strtotime(date("Y-m-d H:i:s"));
            $task->save();
        }

      return response()->json([
        "message" => $message
      ], STATUS_201);
    }

    public function getAllTasks() {
      $tasks = Task::get(['key','value','datetime'])->toJson(JSON_PRETTY_PRINT);
      $status = (!empty($tasks)) ? STATUS_200 : STATUS_400;
      return response($tasks, $status);
    }

    public function getTask($key) {
        $task = [];
        $cond['key'] = $key;
        if(isset($_GET['timestamp'])){
            $cond['datetime'] = trim($_GET['timestamp']);
        }
        $task = Task::where($cond)->get(['key','value','datetime'])->toArray();
        if(empty($task) && isset($_GET['timestamp'])){
            $task = History::where($cond)->get(['key','value','datetime'])->toArray();
        }
        $status = (!empty($task)) ? STATUS_200 : STATUS_400;
        return response()->json(array("data"=>$task)),$status);
    }

    public function createHistory(){
        $history = new History;
        $history->key = $result['key'];
        $history->value = $result['value'];
        $history->datetime = $result['datetime'];
        $history->save();
    }
}
