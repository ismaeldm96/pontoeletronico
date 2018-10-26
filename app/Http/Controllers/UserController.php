<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::paginate(10);
        return view('admin.usuarios', compact('usuarios'));
    }

    public function toggleAdmin($id)
    {
        $usuario = User::where('id', $id)->first();
        if ($usuario && $usuario->name!='admin') {
            $usuario->admin = !$usuario->admin;
            if ($usuario->save()) {
                return redirect()->route('usuarios')->with('success', 'Usuário alterado com sucesso');
            }
        }

        return redirect()->route('usuarios')->with('error', 'Erro ao alterar o usuário');
    }

    public function destroy($id)
    {
        $usuario = User::where('id', $id)->first();
        if ($usuario && $usuario->name!='admin' && $usuario->delete()) {
            return redirect()->route('usuarios')->with('success', 'Usuário excluído com sucesso');
        } else {
            return redirect()->route('usuarios')->with('error', 'Erro ao excluir o usuário');

        }
    }
}
