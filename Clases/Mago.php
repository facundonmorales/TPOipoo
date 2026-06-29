<?php

class Mago extends Personaje {
    private $mana;
    private $inteligencia;
    public function __construct($nombre, $tipoPersonaje, $nivel, $puntosVida, $energia, $duelosGanados, $duelosPerdidos, $estado, $mana, $inteligencia, $id = null, $idArmaEquipada = null){
        parent::__construct($nombre, $tipoPersonaje, $nivel, $puntosVida, $energia, $duelosGanados, $duelosPerdidos, $estado, $id, $idArmaEquipada);     
        $this->mana = $mana;
        $this->inteligencia = $inteligencia;
    }

    //Getters
    public function getMana(){
        return $this->mana;
    }
    public function getInteligencia(){
        return $this->inteligencia;
    }

    //Setters
    public function setMana($mana){
        $this->mana = $mana;
    }
    public function setInteligencia($inteligencia){
        $this->inteligencia = $inteligencia;
    }
    public function calcularPoderBase(){
        $nivel = $this->getNivel();
        $mana = $this->getMana();
        $poderBase = $nivel * 10 + $mana;
        return $poderBase;
    }
    public function calcularPoderEspecial(){
        $mana = $this->getMana();
        $inteligencia = $this->getInteligencia();
        $poderEspecial = $mana*3 + $inteligencia * 5;
        return $poderEspecial;
    }

    public function __toString(){
        return parent::__toString().
               "Mana: {$this->getMana()}\n".
               "Inteligencia: {$this->getInteligencia()}\n";
    }
}