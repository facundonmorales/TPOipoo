<?php

abstract class Personaje {
    private $id;
    private $nombre;
    private $nivel;
    private $puntosVida;
    private $energia;
    private $duelosGanados;
    private $duelosPerdidos;
    private $estado; // el estado puede tomar los siguientes valores: disponible, lesionado, retirado

    public function __construct($nombre, $nivel, $puntosVida, $energia, $duelosGanados, $duelosPerdidos, $estado, $id = null){
        $this->id = $id;
        $this->nombre = $nombre;
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

    //Setters
    public function setId($id){
        $this->id = $id;
    }
    public function setNombre($nombre){
        $this->nombre = $nombre;
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


    //Métodos
    public function recibirDanio($cantidad){
        $vidaRestante = $this->getPuntosVida() - $cantidad;
        $this->setPuntosVida($vidaRestante);
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

    }
    public function calcularPoderTotal(){
        $poderBase = $this->calcularPoderBase();
        $poderEspecial = $this->calcularPoderEspecial();
        $poderTotal = $poderBase + $poderEspecial;
        return $poderTotal;
    }

    //Metodo abstractos
    abstract public function calcularPoderBase();
    abstract public function calcularPoderEspecial();

    public function __toString()
    {
        $mensaje = "Id: {$this->getId()}\n".
                   "Nombre: {$this->getNombre()}\n".
                   "Nivel: {$this->getNivel()}\n".
                   "Puntos de vida: {$this->getPuntosVida()}\n".
                   "Energia: {$this->getEnergia()}\n".
                   "Duelos Ganados: {$this->getDuelosGanados()}\n".
                   "Duelos Perdidos: {$this->getDuelosPerdidos()}\n".
                   "Estado: {$this->getEstado()}\n";
        
        return $mensaje;
    }
}