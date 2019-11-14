<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    public function all()
    {
        $data = $this->DataRequest();

        if ($this->AccessToken($data['token'], $data['credenciales'], $Usr, $errors)) {

            $validator = Validator::make($data['data'], [
                'info' => '',
            ]);

            $flag = false;

            if (!$validator->fails()) {

                DB::beginTransaction();

                try {

                    $result = DB::table('usuario')
                        ->select('usuario.*')
                        ->get();

                    foreach ($result as $key => $row) {
                        if ($row->foto) {
                            $row->foto_archivo = $row->foto;
                            $formato = explode('.', $row->foto_archivo);
                            if (count($formato) === 2) {
                                $row->foto_formato = $formato[1];
                            } else {
                                $row->foto_formato = '';
                            }
                        } else {
                            $row->foto_archivo = '';
                            $row->foto_formato = '';
                        }
                    }

                    if ($result) {
                        $flag = true;
                        $status = 200;
                        $message = "Datos encontrados.";
                        $data = $result;
                        DB::commit();
                    } else {
                        $flag = false;
                        $status = 400;
                        $message = "Datos no encontrados.";
                        $data = array();
                        DB::rollback();
                    }

                    $response = [
                        "success" => $flag,
                        "status" => $status,
                        "message" => $message,
                        "data" => $data,
                        "user" => $Usr,
                    ];

                } catch (\Exception $e) {
                    DB::rollback();
                    return $this->ErrorTransaction($e);
                }
            } else {
                $response = [
                    "success" => $flag,
                    "status" => 400,
                    "message" => "No se encontraron datos.",
                    "errors" => $validator->errors()->messages()
                ];
            }

        } else {
            $response = [
                "success" => false,
                "status" => 400,
                "message" => "Token invalido.",
                "errors" => $errors
            ];
        }

        return $response;
    }

    public function show()
    {
        $data = $this->DataRequest();

        if ($this->AccessToken($data['token'], $data['credenciales'], $Usr, $errors)) {

            $validator = Validator::make($data['data'], [
                'id_usuario' => 'required',
            ]);

            $flag = false;

            if (!$validator->fails()) {

                DB::beginTransaction();

                try {

                    $id_usuario = $data['data']['id_usuario'];

                    $row = DB::table('usuario')
                        ->select('usuario.*')
                        ->where('usuario.id_usuario', '=', $id_usuario)
                        ->first();

                    if ($row->foto) {
                        $row->foto_archivo = $row->foto;
                        $formato = explode('.', $row->foto_archivo);
                        if (count($formato) === 2) {
                            $row->foto_formato = $formato[1];
                        } else {
                            $row->foto_formato = '';
                        }
                    } else {
                        $row->foto_archivo = '';
                        $row->foto_formato = '';
                    }

                    if ($row) {
                        $flag = true;
                        $status = 200;
                        $message = "Datos encontrados.";
                        $data = $row;
                        DB::commit();
                    } else {
                        $flag = false;
                        $status = 400;
                        $message = "Datos no encontrados.";
                        $data = array();
                        DB::rollback();
                    }

                    $response = [
                        "success" => $flag,
                        "status" => $status,
                        "message" => $message,
                        "data" => $data,
                        "user" => $Usr,
                    ];

                } catch (\Exception $e) {
                    DB::rollback();
                    return $this->ErrorTransaction($e);
                }
            } else {
                $response = [
                    "success" => $flag,
                    "status" => 400,
                    "message" => "No se encontraron datos.",
                    "errors" => $validator->errors()->messages()
                ];
            }

        } else {
            $response = [
                "success" => false,
                "status" => 400,
                "message" => "Token invalido.",
                "errors" => $errors
            ];
        }

        return $response;
    }

    public function add()
    {
        $data = $this->DataRequest();

        if ($this->AccessToken($data['token'], $data['credenciales'], $Usr, $errors)) {

            $validator = Validator::make($data['data'], [
                'id_usuario' => '',
                'id_cat_sexo' => 'required',
                'username' => 'required',
                'password' => 'required',
                'nombre' => 'required',
                'apellido_paterno' => 'required',
                'apellido_materno' => 'required',
                'foto_archivo' => 'required',
                'foto_formato' => 'required',
            ]);

            $flag = false;

            if (!$validator->fails()) {

                DB::beginTransaction();

                try {

                    $id_cat_sexo = $data['data']['id_cat_sexo'] ?? null;
                    $username = $data['data']['username'] ?? null;
                    $password = $data['data']['password'] ?? null;
                    $nombre = $data['data']['nombre'] ?? null;
                    $apellido_paterno = $data['data']['apellido_paterno'] ?? null;
                    $apellido_materno = $data['data']['apellido_materno'] ?? null;

                    $foto_archivo = $data['data']["foto_archivo"] ?? null;
                    $foto_formato = $data['data']["foto_formato"] ?? null;

                    if ($foto_archivo && $foto_formato) {
                        $nombre_archivo = 'foto-' . md5($nombre);
                        $archivo = $this->Base64ToFile(
                            $foto_archivo,
                            'files/',
                            $nombre_archivo,
                            $foto_formato
                        );
                        if ($archivo['success'] === true) {
                            $ruta = $archivo['ruta'];
                        } else {
                            $ruta = null;
                        }
                    } else {
                        $ruta = null;
                    }

                    $insertId = DB::table('usuario')->insertGetId([
                        "id_cat_sexo" => $id_cat_sexo,
                        "username" => $username,
                        "password" => $password,
                        "nombre" => $nombre,
                        "apellido_paterno" => $apellido_paterno,
                        "apellido_materno" => $apellido_materno,
                        "foto" => $ruta,
                    ]);


                    if ($insertId) {
                        $flag = true;
                        $status = 200;
                        $message = "Datos agregados.";
                        $data = $insertId;
                        DB::commit();
                    } else {
                        $flag = false;
                        $status = 400;
                        $message = "Error al agregar.";
                        $data = array();
                        DB::rollback();
                    }

                    $response = [
                        "success" => $flag,
                        "status" => $status,
                        "message" => $message,
                        "data" => $data,
                        "user" => $Usr,
                    ];

                } catch (\Exception $e) {
                    DB::rollback();
                    return $this->ErrorTransaction($e);
                }
            } else {
                $response = [
                    "success" => $flag,
                    "status" => 400,
                    "message" => "No se encontraron datos.",
                    "errors" => $validator->errors()->messages()
                ];
            }

        } else {
            $response = [
                "success" => false,
                "status" => 400,
                "message" => "Token invalido.",
                "errors" => $errors
            ];
        }

        return $response;
    }

    public function edit()
    {
        $data = $this->DataRequest();

        if ($this->AccessToken($data['token'], $data['credenciales'], $Usr, $errors)) {

            $validator = Validator::make($data['data'], [
                'id_usuario' => 'required',
                'id_cat_sexo' => 'required',
                'username' => 'required',
                'password' => 'required',
                'nombre' => 'required',
                'apellido_paterno' => 'required',
                'apellido_materno' => 'required',
                'foto_archivo' => 'required',
                'foto_formato' => 'required',
            ]);

            $flag = false;

            if (!$validator->fails()) {

                DB::beginTransaction();

                try {

                    $id_usuario = $data['data']['id_usuario'] ?? null;
                    $id_cat_sexo = $data['data']['id_cat_sexo'] ?? null;
                    $username = $data['data']['username'] ?? null;
                    $password = $data['data']['password'] ?? null;
                    $nombre = $data['data']['nombre'] ?? null;
                    $apellido_paterno = $data['data']['apellido_paterno'] ?? null;
                    $apellido_materno = $data['data']['apellido_materno'] ?? null;

                    $foto_archivo = $data['data']["foto_archivo"] ?? null;
                    $foto_formato = $data['data']["foto_formato"] ?? null;

                    $row = DB::table('usuario')
                        ->select('usuario.*')
                        ->where('usuario.id_usuario', '=', $id_usuario)
                        ->first();

                    if ($foto_archivo && $foto_formato) {
                        $nombre_archivo = 'foto-' . md5($id_usuario);
                        $archivo = $this->Base64ToFile(
                            $foto_archivo,
                            'files/',
                            $nombre_archivo,
                            $foto_formato
                        );
                        if ($archivo['success'] === true) {
                            $ruta = $archivo['ruta'];
                        } else {
                            $ruta = $row->foto;
                        }
                    } else {
                        $ruta = $row->foto;
                    }

                    $update = DB::table('usuario')
                        ->where('usuario.id_usuario', '=', $id_usuario)
                        ->update([
                            "id_cat_sexo" => $id_cat_sexo,
                            "username" => $username,
                            "password" => $password,
                            "nombre" => $nombre,
                            "apellido_paterno" => $apellido_paterno,
                            "apellido_materno" => $apellido_materno,
                            "foto" => $ruta,
                            "token" => $row->token,
                            "expiracion" => $row->expiracion,
                        ]);

                    if ($update) {
                        $flag = true;
                        $status = 200;
                        $message = "Datos actualizados.";
                        $data = array();
                        DB::commit();
                    } else {
                        $flag = false;
                        $status = 400;
                        $message = "Error al actualizar.";
                        $data = array();
                        DB::rollback();
                    }

                    $response = [
                        "success" => $flag,
                        "status" => $status,
                        "message" => $message,
                        "data" => $data,
                        "user" => $Usr,
                    ];

                } catch (\Exception $e) {
                    DB::rollback();
                    return $this->ErrorTransaction($e);
                }
            } else {
                $response = [
                    "success" => $flag,
                    "status" => 400,
                    "message" => "No se encontraron datos.",
                    "errors" => $validator->errors()->messages()
                ];
            }

        } else {
            $response = [
                "success" => false,
                "status" => 400,
                "message" => "Token invalido.",
                "errors" => $errors
            ];
        }

        return $response;
    }

    public function delete()
    {
        $data = $this->DataRequest();

        if ($this->AccessToken($data['token'], $data['credenciales'], $Usr, $errors)) {

            $validator = Validator::make($data['data'], [
                'id_usuario' => 'required',
            ]);

            $flag = false;

            if (!$validator->fails()) {

                DB::beginTransaction();

                try {

                    $id_usuario = $data['data']['id_usuario'] ?? null;

                    $delete = DB::table('usuario')
                        ->where('usuario.id_usuario', '=', $id_usuario)
                        ->delete();

                    if ($delete) {
                        $flag = true;
                        $status = 200;
                        $message = "Datos eliminados.";
                        $data = array();
                        DB::commit();
                    } else {
                        $flag = false;
                        $status = 400;
                        $message = "Error al eliminar.";
                        $data = array();
                        DB::rollback();
                    }

                    $response = [
                        "success" => $flag,
                        "status" => $status,
                        "message" => $message,
                        "data" => $data,
                        "user" => $Usr,
                    ];

                } catch (\Exception $e) {
                    DB::rollback();
                    return $this->ErrorTransaction($e);
                }
            } else {
                $response = [
                    "success" => $flag,
                    "status" => 400,
                    "message" => "No se encontraron datos.",
                    "errors" => $validator->errors()->messages()
                ];
            }

        } else {
            $response = [
                "success" => false,
                "status" => 400,
                "message" => "Token invalido.",
                "errors" => $errors
            ];
        }

        return $response;
    }
}
