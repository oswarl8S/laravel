<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SexoController extends Controller
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

                    $result = DB::table('sexo')
                        ->select('sexo.*')
                        ->get();

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
                        DB::commit();
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
                'id_sexo' => 'required',
            ]);

            $flag = false;

            if (!$validator->fails()) {

                DB::beginTransaction();

                try {

                    $id_sexo = $data['data']['id_sexo'];

                    $row = DB::table('sexo')
                        ->select('sexo.*')
                        ->where('sexo.id_sexo', '=', $id_sexo)
                        ->first();

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
                        DB::commit();
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
                'sexo' => 'required',
                'activo' => 'required',
            ]);

            $flag = false;

            if (!$validator->fails()) {

                DB::beginTransaction();

                try {

                    $id_sexo = $data['data']['id_sexo'] ?? null;
                    $sexo = $data['data']['sexo'] ?? null;
                    $activo = $data['data']['activo'] ?? null;

                    $insertId = DB::table('sexo')->insertGetId([
                        "sexo" => $sexo,
                        "activo" => $activo,
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
                        DB::commit();
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
                'id_sexo' => '',
                'sexo' => 'required',
                'activo' => 'required',
            ]);

            $flag = false;

            if (!$validator->fails()) {

                DB::beginTransaction();

                try {

                    $id_sexo = $data['data']['id_sexo'] ?? null;
                    $sexo = $data['data']['sexo'] ?? null;
                    $activo = $data['data']['activo'] ?? null;

                    $update = DB::table('sexo')
                        ->where('sexo.id_sexo', '=', $id_sexo)
                        ->update([
                            "sexo" => $sexo,
                            "activo" => $activo
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
                        DB::commit();
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
                'id_sexo' => 'required',
            ]);

            $flag = false;

            if (!$validator->fails()) {

                DB::beginTransaction();

                try {

                    $id_sexo = $data['data']['id_sexo'] ?? null;

                    $delete = DB::table('sexo')
                        ->where('sexo.id_sexo', '=', $id_sexo)
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
                        DB::commit();
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
