<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use App\Http\Requests\TodoRequest;

class TodoController extends Controller
{
    public function index()
    {
        $todos=Todo::all();
        return view('index', compact('todos'));
    }
    public function store(TodoRequest $request)
    {
        $todo=$request->only(['content']);
        Todo::create($todo);
        return redirect('/')->with('message', 'Todoを作成しました');
    }
    public function update(TodoRequest $request)
    {
        $todo=Todo::find($request->id);

        if (!$todo){
            return redirect('/')->with('error', 'Todoが見つかりません');
        }
        $todo->update($request->only(['content']));
        return redirect('/')->with('message', 'Todoを更新しました');
    }
    public function destroy(Request $request)
    {
        $todo=Todo::find($request->id);

         if (!$todo){
            return redirect('/')->with('error', 'Todoが見つかりません');
        }
        $todo->delete();
        return redirect('/')->with('message', 'Todoを削除しました');
    }
}
