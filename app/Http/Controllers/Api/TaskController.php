<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Task;
use App\History;

define("STATUS_200", 200);
define("STATUS_404", 404);
define("STATUS_400", 400);

class TaskController extends Controller
{
    public function createTask(Request $request) {

        $validator = \Validator::make($request->all(), [
            'key' => 'required|string|max:255',
            'value' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(array("error"=>"input request error"),STATUS_400);
        }

        $result = Task::where('key', $request->key)->first();
        if(!empty($result)){
            $result = $result->toArray();
            $this->createHistory($result);
            $task = Task::where('key',$request->key)->update(['value'=>$request->value,'datetime'=>strtotime(date("Y-m-d H:i:s"))]);
        }else{
            $this->addTask($request);
        }
      return $this->getTask($request->key);
    }

    public function getAllTasks() {
      $tasks = Task::get(['key','value','datetime']);
      return response()->json(array("data"=>$tasks), (!empty($tasks)) ? STATUS_200 : STATUS_404);
    }

    public function getTask($key) {
        if (!$key || is_numeric($key)) {
            return response()->json(array("error"=>"input request error"),STATUS_400);
        }

        $cond['key'] = $key;
        if(isset($_GET['timestamp'])){
            $cond['datetime'] = trim($_GET['timestamp']);
        }
        $task = Task::where($cond)->get(['key','value','datetime'])->toArray();
        if(empty($task) && isset($_GET['timestamp'])){
            $task = History::where($cond)->get(['key','value','datetime'])->toArray();
        }
        $status = (!empty($task)) ? STATUS_200 : STATUS_404;
        return response()->json(array("data"=>$task),$status);
    }

    public function createHistory($result){
        $history = new History;
        $history->key = $result['key'];
        $history->value = $result['value'];
        $history->datetime = $result['datetime'];
        $history->save();
    }

    public function addTask($request){
        $task = new Task;
        $task->key = $request->key;
        $task->value = $request->value;
        $task->datetime = strtotime(date("Y-m-d H:i:s"));
        $task->save();
    }
}
