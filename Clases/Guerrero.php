<?php

class Guerrero extends Personaje {
    private $fuerza;
    private $armadura;

    public function __construct($nombre, $nivel, $puntosVida, $energia, $duelosGanados, $duelosPerdidos, $estado, $fuerza, $armadura, $id = null){
        $this->fuerza = $fuerza;
        $this->armadura = $armadura;
        parent::__construct($nombre, $nivel, $puntosVida, $energia, $duelosGanados, $duelosPerdidos, $estado, $id);
    }

    //Getters
    public function getFuerza(){
        return $this->fuerza;
    }
    public function getArmadura(){
        return $this->armadura;
    }

    //Setters
    public function setFuerza($fuerza){
        $this->fuerza = $fuerza;
    }
    public function setArmadura($armadura){
        $this->armadura = $armadura;
    }
    public function calcularPoderBase(){
        $nivel = $this->getNivel();
        $poderBase = $nivel * 15;
        return $poderBase;
    }
    public function calcularPoderEspecial(){
        $fuerza = $this->getFuerza();
        $armadura = $this->getArmadura();
        $poderEspecial = $fuerza * 2 + $armadura;
        return $poderEspecial;
    }

    public function toString(){
        return parent::__toString().
               "Fuerza: {$this->getFuerza()}\n".
               "Armadura: {$this->getArmadura()}\n";

    }
}