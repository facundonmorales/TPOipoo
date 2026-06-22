<?php

abstract class Personaje {
    private $id;
    private $nombre;
    private $tipoPersonaje;
    private $nivel;
    private $puntosVida;
    private $energia;
    private $duelosGanados;
    private $duelosPerdidos;
    private $estado; // el estado puede tomar los siguientes valores: disponible, lesionado, retirado
    private ?Arma $armaEquipada = null;

    public function __construct($nombre, $tipoPersonaje, $nivel, $puntosVida, $energia, $duelosGanados, $duelosPerdidos, $estado, $id = null,){
        $this->id = $id;
        $this->nombre = $nombre;
        $this->tipoPersonaje = $tipoPersonaje;
        $this->nivel = $nivel;
        $this->puntosVida = $puntosVida;
        $this->energia = $energia;
        $this->duelosGanados = $duelosGanados;
        $this->duelosPerdidos = $duelosPerdidos;
        $this->estado = $estado;
    }

    //Getters
    public function getId(){
        return $this->id;
    }
    public function getNombre(){
        return $this->nombre;
    }
    public function getTipoPersonaje(){
        return $this->tipoPersonaje;
    }
    public function getNivel(){
        return $this->nivel;
    }
    public function getPuntosVida(){
        return $this->puntosVida;
    }
    public function getEnergia(){
        return $this->energia;
    }
    public function getDuelosGanados(){
        return $this->duelosGanados;
    }
    public function getDuelosPerdidos(){
        return $this->duelosPerdidos;
    }
    public function getEstado(){
        return $this->estado;
    }
    public function getArmaEquipada(){
         return $this->armaEquipada; 
    }

    //Setters
    public function setId($id){
        $this->id = $id;
    }
    public function setNombre($nombre){
        $this->nombre = $nombre;
    }
    public function setTipoPersonaje($tipoPersonaje){
        $this->tipoPersonaje = $tipoPersonaje;
    }
    public function setNivel($nivel){
        $this->nivel = $nivel;
    }
    public function setPuntosVida($puntosVida){
        $this->puntosVida = $puntosVida;
    }
    public function setEnergia($energia){
        $this->energia = $energia;
    }
    public function setDuelosGanados($duelosGanados){
        $this->duelosGanados = $duelosGanados;
    }
    public function setDuelosPerdidos($duelosPerdidos){
        $this->duelosPerdidos = $duelosPerdidos;
    }
    public function setEstado($estado){
        $this->estado = $estado;
    }
    public function setArmaEquipada(?Arma $armaEquipada){
        $this->armaEquipada = $armaEquipada;
    }


    //Métodos
    public function recibirDanio( $cantidad) {
        $this->puntosVida -= $cantidad;
        if ($this->puntosVida <= 0) {
            $this->puntosVida = 0;
            $this->estado = 'retirado';
        } elseif ($this->puntosVida <= 30) {
            $this->estado = 'lesionado';
        }
    }
    public function recuperarVida($cantidad){
        $vidaRecuperada = $this->getPuntosVida() + $cantidad;
        $this->setPuntosVida($vidaRecuperada);
    }
    public function recuperarEnergia($cantidad){
        $energiaRecuperada = $this->getEnergia() + $cantidad;
        $this->setEnergia($energiaRecuperada);
    }
    public function puedeDuelar(){
        return $this->getEstado() == 'disponible';
    }
    public function calcularPoderTotal(){
        $poderBase = $this->calcularPoderBase();
        $poderEspecial = $this->calcularPoderEspecial();
        $poderTotal = $poderBase + $poderEspecial;
        return $poderTotal;
    }
    public function recibirRecompensas(){
        $this -> setNivel($this-> getNivel() + 1);
        $this -> setEnergia($this-> getEnergia() + 5);
        $this -> setDuelosGanados($this-> getDuelosGanados() + 1);
    }

    public function recibirCastigo($danio){
        $this-> recibirDanio($danio);
        $this-> setDuelosPerdidos($this -> getDuelosPerdidos() + 1);
        $this -> setEnergia($this-> getEnergia() - 5);
        if($this -> getPuntosVida() <= 0){
            $this-> setEstado('retirado');
        } else if ($this -> getPuntosVida() < 30){
            $this-> setEstado('lesionado');
        }
    }

    //Metodo abstractos
    abstract public function calcularPoderBase();
    abstract public function calcularPoderEspecial();

    public function __toString()
    {
        $mensaje = "Id: {$this->getId()}\n".
                   "Nombre: {$this->getNombre()}\n".
                   "Clase: {$this->getTipoPersonaje()}\n".
                   "Nivel: {$this->getNivel()}\n".
                   "Puntos de vida: {$this->getPuntosVida()}\n".
                   "Energia: {$this->getEnergia()}\n".
                   "Duelos Ganados: {$this->getDuelosGanados()}\n".
                   "Duelos Perdidos: {$this->getDuelosPerdidos()}\n".
                   "Estado: {$this->getEstado()}\n";
        
        return $mensaje;
    }
}