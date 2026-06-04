<?php

class Arquero extends Personaje {
    private $precision;
    private $velocidad;

    public function __construct($nombre, $nivel, $puntosVida, $energia, $duelosGanados, $duelosPerdidos, $estado, $precision, $velocidad, $id = null ){
        $this->precision = $precision;
        $this->velocidad = $velocidad;
        parent::__construct($nombre, $nivel, $puntosVida, $energia, $duelosGanados, $duelosPerdidos, $estado, $id);
    }

    //Getters
    public function getPrecision(){
        return $this->precision;
    }
    public function getVelocidad(){
        return $this->velocidad;
    }

    //Setters
    public function setPrecision($precision){
        $this->precision = $precision;
    }
    public function setVelocidad($velocidad){
        $this->velocidad = $velocidad;
    }
    public function calcularPoderBase(){
        $nivel = $this->getNivel();
        $poderBase = $nivel * 15;
        return $poderBase;
    }
    public function calcularPoderEspecial(){
        $precision = $this->getPrecision();
        $velocidad = $this->getVelocidad();
        $poderEspecial = $precision*2 + $velocidad;
        return $poderEspecial;
    }

    public function toString(){
        return parent::__toString().
               "Precision: {$this->getPrecision()}\n".
               "Velocidad: {$this->getVelocidad()}\n";
    }
}