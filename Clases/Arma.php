<?php

class Arma {
    private $id;
    private $nombre;
    private $tipo;
    private $danioBase;
    private $nivelMinimo;
    private $estado; //El estado puede tomar los siguientes valores: disponible, equipada, rota

    public function __construct($nombre, $tipo, $danioBase, $nivelMinimo, $estado, $id = null){
        $this->id = $id;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->danioBase = $danioBase;
        $this->nivelMinimo = $nivelMinimo;
        $this->estado = $estado;
    }

    //Getters
    public function getId(){
        return $this->id;
    }
    public function getNombre(){
        return $this->nombre;
    }
    public function getTipo(){
        return $this->tipo;
    }
    public function getDanioBase(){
        return $this->danioBase;
    }
    public function getNivelMinimo(){
        return $this->nivelMinimo;
    }
    public function getEstado(){
        return $this->estado;
    }

    //Setters

    public function setId($id){
        $this->id = $id;
    }
    public function setNombre($nombre){
        $this->nombre = $nombre;
    }
    public function setTipo($tipo){
        $this->tipo = $tipo;
    }
    public function setDanioBase($danioBase){
        $this->danioBase = $danioBase;
    }
    public function setNivelMinimo($nivelMinimo){
        $this->nivelMinimo = $nivelMinimo;
    }
    public function setEstado($estado){
        $this->estado = $estado;
    }

    //Persistencia

    public function guardar($database) {
        // Los datos que vamos a guardar en la base de datos obtenidos a través de los getters de la clase
        $datos = [
            "nombre" => $this->getNombre(),
            "tipo" => $this->getTipo(),
            "danioBase" => $this->getDanioBase(),
            "nivelMinimo" => $this->getNivelMinimo(),
            "estado" => $this->getEstado()
        ];
        if ($this->getId()) {
            $database->update("armas", $datos, ["id" => $this->getId()]);
        } else {
            $database->insert("armas", $datos);
            $this->setId($database->id());
        }
    }

    public function borrar($database) {

        if ($this->getId()) {
            $resultado = $database->delete("armas", [
                "id" => $this->getId()
            ]);

            if ($resultado) {
                $this->setId(null); 
                return true;
            }
        }
        return false;
    }
    
    public static function buscarPorId($database, $id) {
        $datos = $database->get("armas", "*", ["id" => $id]);
        $objetoArma = null; 
        if ($datos) {
            $objetoArma = new Arma(
                $datos["nombre"],
                $datos["tipo"],
                $datos["danioBase"],
                $datos["nivelMinimo"],
                $datos["estado"],
                $datos["id"] 
            );
        }

        return $objetoArma;
    }
    
    public static function listar($database) {
        $todasLasArmas = $database->select("armas", "*");
        $listaArmas = []; 
        foreach ($todasLasArmas as $datos) {
            $listaArmas[] = new Arma(
                $datos["nombre"],
                $datos["tipo"],
                $datos["danioBase"],
                $datos["nivelMinimo"],
                $datos["estado"],
                $datos["id"] 
            );
        }
        return $listaArmas;
    }

    //Metodos

    public function calcularDanio(){
        if($this->getEstado() == 'rota'){
            return 0;
        }
        return $this->getDanioBase();
    }

    public function puedeSerEquipadaPor(Personaje $personaje){
        $estadoArma = $this->getEstado();
        $nivelPersonaje = $personaje->getNivel();
        $nivelMinimo = $this->getNivelMinimo();
        $puedeSerEquipada = false;
        if($nivelPersonaje >= $nivelMinimo){
        switch($estadoArma){
            case "rota":
                $puedeSerEquipada = false;
                break;
            case "equipada":
                $puedeSerEquipada = false;
                break;
            case "disponible":
                $puedeSerEquipada = true;
                break;
        }
        }
        return $puedeSerEquipada;
    }



}