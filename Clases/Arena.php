<?php

class Arena {
    private $id;
    private $nombre;
    private $dificultad;
    private $capacidadPublico;
    private $clima; //climas posibles: normal, lluvia, tormenta, niebla

    public function __construct($id, $nombre, $dificultad, $capacidadPublico, $clima)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->dificultad = $dificultad;
        $this->capacidadPublico = $capacidadPublico;
        $this->clima = $clima;
    }

    //Getters
    public function getId(){
        return $this->id;
    }
    public function getNombre(){
        return $this->nombre;
    }
    public function getDificultad(){
        return $this->dificultad;
    }
    public function getCapacidadPublico(){
        return $this->capacidadPublico;
    }
    public function getClima(){
        return $this->clima;
    }
    //Setters
    public function setId($id){
        $this->id = $id;
    }
    public function setNombre($nombre){
        $this->nombre = $nombre;
    }
    public function setDificultad($dificultad){
        $this->dificultad = $dificultad;
    }
    public function setCapacidadPublico($capacidadPublico){
        $this->capacidadPublico = $capacidadPublico;
    }
    public function setClima($clima){
        $this->clima = $clima;
    }

    //Metodos
    public function calcularModificadorArena(Personaje $personaje){

    }
    
}