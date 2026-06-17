<?php
Class Duelo {
 
private $id;
private $personaje1;
private $personaje2;
private $arena;
private $fecha;
private $estado; //Estados: pendiente, realizado, cancelado 
private $ganador;

public function __construct($personaje1, $personaje2, $arena, $fecha, $estado, $ganador = null, $id = null){
    $this->id = $id;
    $this->personaje1 = $personaje1;
    $this->personaje2 = $personaje2;
    $this->arena = $arena;
    $this->fecha = $fecha;
    $this->estado = $estado;
    $this->ganador = $ganador;
}

//Getters

public function getPersonaje1(){
    return $this->personaje1;
}
public function getPersonaje2(){
    return $this->personaje2;
}
public function getArena(){
    return $this->arena;
}
public function getFecha(){
    return $this->fecha;
}
public function getEstado(){
    return $this->estado;
}
public function getGanador(){
    return $this->ganador;
}
public function getId(){
    return $this->id;
}

//Setters

public function setPersonaje1($personaje1){
    $this->personaje1 = $personaje1;
}
public function setPersonaje2($personaje2){
    $this->personaje2 = $personaje2;
}
public function setArena($arena){
    $this->arena = $arena;
}
public function setFecha($fecha){
    $this->fecha = $fecha;
}
public function setEstado($estado){
    $this->estado = $estado;
}
public function setGanador($ganador){
    $this->ganador = $ganador;
}
public function setId($id){
    $this->id = $id;
}

public function puedeRealizarse(){
    $idPj1 = $this->getPersonaje1()->getId();
    $idPj2 = $this->getPersonaje2()->getId();
    $sePuedeRealizar = false;
    if($idPj1 != $idPj2 && $this->getPersonaje1()-> puedeDuelar() && $this-> getPersonaje2()-> puedeDuelar()){
        $sePuedeRealizar = true;
    }
    return $sePuedeRealizar;
}

//terminar
public function realizarDuelo($arma1, $arma2){
    $seRealizo = false;
    if($this->puedeRealizarse()){
        $personaje1 = $this-> getPersonaje1();
        $personaje2 = $this -> getPersonaje2();
        $pwPersonaje1 = $personaje1 -> calcularPoderTotal() + $arma1 -> calcularDanio() + $this -> getArena() -> calcularModificadorArena();
        $pwPersonaje2 = $personaje2 -> calcularPoderTotal() + $arma2 -> calcularDanio() + $this -> getArena() -> calcularModificadorArena();
        if($pwPersonaje1 > $pwPersonaje2){
            $this-> setGanador($personaje1);
            $danio = $pwPersonaje1 - $pwPersonaje2;
            $this -> getGanador() -> recibirRecompensas(); 
            $this -> getPersonaje2() -> recibirCastigo($danio);
        } else if ($pwPersonaje2 > $pwPersonaje1){
            $this -> setGanador($personaje2);
            $danio = $pwPersonaje2 - $pwPersonaje1;
            $this -> getGanador() -> recibirRecompensas(); 
            $this -> getPersonaje1() -> recibirCastigo($danio);
        }
        $this -> setEstado('realizado');
        $seRealizo = true;
    } else {
        $this -> setEstado('cancelado');
    }
    return $seRealizo;
}

public function obtenerGanador(){
    return $this -> getGanador();
}


}