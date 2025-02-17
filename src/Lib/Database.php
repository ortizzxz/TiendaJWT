<?php
namespace Lib;

use PDO;
use PDOException;

class Database
{
    private PDO $conexion;
    private string $servidor;
    private string $usuario;
    private string $pass;
    private string $base_datos;
    private string $tipo_de_base;

    private mixed $resultado;


    public function __construct()
    {
        $this->tipo_de_base = "mysql";
        $this->servidor = $_ENV["SERVERNAME"];
        $this->usuario = $_ENV["USER"];
        $this->pass = $_ENV["PASSWORD"];
        $this->base_datos = $_ENV["DATABASE"];
        $this->conexion = $this->connect();
    }

    private function connect(): PDO
    {
        try {
            $opciones = [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ];
            return new PDO(
                "{$this->tipo_de_base}:host={$this->servidor};dbname={$this->base_datos}",
                $this->usuario,
                $this->pass,
                $opciones
            );
        } catch (PDOException $e) {
            die("Error al conectar con la base de datos: " . $e->getMessage());
        }
    }

    public function lastInsertId()
    {
        return $this->conexion->lastInsertId();
    }


    public function escape($string)
    {
        return $this->conexion->quote($string);
    }

    public function query(string $consultaSQL): mixed
    {
        try {
            $this->resultado = $this->conexion->query($consultaSQL);
            return $this->resultado;
        } catch (PDOException $e) {
            echo "Error al ejecutar la consulta: " . $e->getMessage();
            return false;
        }
    }

    public function customQuery($sql, $params = [])
    {
        $stmt = $this->conexion->prepare($sql);
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function extractRegister(): mixed
    {
        return ($fila = $this->resultado->fetch(PDO::FETCH_ASSOC)) ? $fila : false;
    }

    public function fetchAll(): array
    {
        return $this->resultado->fetchAll(PDO::FETCH_ASSOC);
    }

    public function execute($sql, $params = [])
    {
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount(); // Return the number of affected rows
    }



    public function queryOne($sql, $params)
    {
        $stmt = $this->conexion->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function prepare($sql)
    {
        return $this->conexion->prepare($sql);
    }

    public function getConnection(): PDO
    {
        return $this->conexion;
    }

}