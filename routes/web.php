<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

//Usuário comum
$this ->group([
    'middleware' => 'auth'],
    function(){
        $this->get('/', 'HomeController@gotohome');
        $this->get('/home', 'HomeController@index')->name('home');
        $this->get('/horarios', 'HorarioController@index')->name('horarios');
        $this->post('/horarios/store', 'HorarioController@store')->name('horarios.store');
        $this->any('/horarios/search', 'HorarioController@search')->name('horarios.search');
        $this->delete('/horarios/destroy/{id}', 'HorarioController@destroy')->name('horarios.destroy');
    }
);

//Usuário administrador
$this ->group([
    'middleware' => ['auth', 'can:administrador']],
    function(){
        $this->get('/historico/horarios', 'HorariosHistoricoController@index')->name('historico.horarios');
        $this->get('/historico/horarios/{id}', 'HorariosHistoricoController@obterHistoricos')->name('historico.horarios');
        $this->post('/horarios/restore/{id}', 'HorarioController@restore')->name('horarios.restore');
        $this->get('/usuarios', 'UserController@index')->name('usuarios');
        $this->post('/usuarios/toggleAdmin/{id}', 'UserController@toggleAdmin')->name('usuarios.toggleAdmin');
        $this->delete('/usuarios/destroy/{id}', 'UserController@destroy')->name('usuarios.destroy');
    }
);