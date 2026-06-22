<?php
include_once "configuracion.php";
class Torneo {
    private $personajes = [];
    private $armas = [];
    private $arenas = [];
    private $duelos = [];
    
    public function agregarPersonaje(Personaje $personaje){
        $personajes[] = $personaje;
    }
    public function agregarArma(Arma $arma){
        $armas[] = $arma;
    }
    public function agregarArena(Arena $arena){
        $arena[] = $arena;
    }
    public function equiparArma(Personaje $personaje, Arma $arma){

        $puedeSerEquipada = $arma ->puedeSerEquipadaPor($personaje);
        if($puedeSerEquipada){}
    }
    public function registrarDuelo(){}
    public function listarPersonajes(){}
    public function listarArmas(){}
    public function listarDuelos(){}
    public function rankingPersonajes(){}
}