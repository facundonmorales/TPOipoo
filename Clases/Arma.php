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
    public function getDañoBase(){
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
    public function setDañoBase($danioBase){
        $this->danioBase = $danioBase;
    }
    public function setNivelMinimo($nivelMinimo){
        $this->nivelMinimo = $nivelMinimo;
    }
    public function setEstado($estado){
        $this->estado = $estado;
    }

    //Metodos

    public function calcularDanio(){
        if($this->getEstado() == 'rota'){
            return 0;
        }
        return $this->getDañoBase();
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