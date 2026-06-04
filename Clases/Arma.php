<?php

class Arma {
    private $id;
    private $nombre;
    private $tipo;
    private $danioBase;
    private $nivelMinimo;
    private $estado; //El estado puede tomar los siguientes valores: disponible, equipada, rota

    public function __construct($id, $nombre, $tipo, $danioBase, $nivelMinimo, $estado){
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

    }

    public function puedeSerEquipadaPor(Personaje $personaje){
        $estadoArma = $this->getEstado();
        $nivelPersonaje = $personaje->getNivel();
        if($estadoArma =='rota'){
            return false;
        }
        if ($estadoArma == 'equipada') {
            return false;
        }
        if($nivelPersonaje < 0){
            return false;
        }
        return true;
    }



}