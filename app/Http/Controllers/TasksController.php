<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Hash;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if (!Auth::user()) {
            return response()->json([
                "message" => "Unauthorized"
            ], 401);
        }

        if ($request->query('completed') == true){
            $tasks = Tasks::with("user")->where('user_id', Auth::user()->id)->where('completed',true)->orderBy("id", "desc")->get();
        }
        else{
            $tasks = Tasks::with("user")->where('user_id', Auth::user()->id)->orderBy("id", "ASC")->get();
        }

        return response()->json([
            "tasks" => $tasks,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     */
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'body' => 'required|max:250',
            ]);

            $task = new Tasks();
            $task->body = $request->body;
            $task->user_id = Auth::user()->id;
            $task->completed = 0;
            $task->save();

            return response()->json([
                "task" => $task,
                "message" => "Task has been created successfully",
            ],200);
        }
        catch (Exception $error){
            return response()->json([
                "message" => "Error in creation",
                "error" => $error,
            ],422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tasks  $tasks
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Tasks $tasks, $id): \Illuminate\Http\JsonResponse
    {
        try {
            $oneTask = Tasks::with("user")->where('user_id', Auth::user()->id)->findOrFail($id);

            return response()->json([
                "task" => $oneTask,
            ],200);
        }
        catch (Exception $error){
            return response()->json([
                "message" => "Unauthorized or not exist",
            ],422);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tasks  $tasks
     */
    public function edit(Tasks $tasks)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tasks  $tasks
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Tasks $tasks,$id)
    {
        try {
            Tasks::with("user")->where('user_id', Auth::user()->id)->where('id', $id)->update(["completed" => true]);
            $displayTaskUpdated = Tasks::where('user_id', Auth::user()->id)->findOrFail($id);

            return response()->json([
                "task" => $displayTaskUpdated,
            ],200);
        }
        catch (Exception $error){
            return response()->json([
                "message" => "Unauthorized",
            ],401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tasks  $tasks
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Tasks $tasks,$id)
    {
        try {
            Tasks::where('id', $id)->where('user_id', Auth::user()->id)->firstOrfail()->delete();
            return response()->json([
                'status_code' => 200,
                'message' => 'Task deleted successfully!'
            ]);
        }
        catch (Exception $error){
            return response()->json([
                "message" => "Unauthorized or not exist",
            ],422);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
            $credentials = request(["email", "password"]);

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    "message" => "Unauthorized"
                ],401);
            }

            $user = User::where("email", $request->email)->first();
            $tokenResult = $user->createToken("authToken")->plainTextToken;

            return response()->json([
                "access_token" => $tokenResult,
                "token_type" => "Bearer",
            ],200);
        }
        catch (Exception $error) {
            return response()->json([
                "message" => "Error in Login",
                "error" => $error,
            ],422);
        }
    }

    public function register(Request $request){
        try {
            $request->validate([
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'name' => 'required'
            ]);

            $user = new User();
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->name = ucfirst($request->name);
            $user->save();

            $credentialsRegister = request(["email", "password"]);
            if (!Auth::attempt($credentialsRegister)) {
                return response()->json([
                    "message" => "Unauthorized"
                ],401);
            }

            $tokenResult = $user->createToken("authToken")->plainTextToken;

            return response()->json([
                "access_token" => $tokenResult,
                "user" => $user,
                "token_type" => "Bearer",
            ],200);
        }
        catch (Exception $error) {
            return response()->json([
                "message" => "Error in Register",
                "error" => $error,
            ],422);
        }
    }
}
