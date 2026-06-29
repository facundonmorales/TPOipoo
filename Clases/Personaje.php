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
    private $armaEquipada;

    public function __construct($nombre, $tipoPersonaje, $nivel, $puntosVida, $energia, $duelosGanados, $duelosPerdidos, $estado, $id = null, $armaEquipada =null){
        $this->id = $id;
        $this->nombre = $nombre;
        $this->tipoPersonaje = $tipoPersonaje;
        $this->nivel = $nivel;
        $this->puntosVida = $puntosVida;
        $this->energia = $energia;
        $this->duelosGanados = $duelosGanados;
        $this->duelosPerdidos = $duelosPerdidos;
        $this->estado = $estado;
        $this->armaEquipada = $armaEquipada;
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
    public function setArmaEquipada($armaEquipada){
        $this->armaEquipada = $armaEquipada;
    }

    //Consultas SQL
    //Inserta un personaje o modifica el actual si es que ya existe
    public function guardar($database) {
        $exito = false;
        $datos = [
            "nombre" => $this->getNombre(),
            "tipoPersonaje" => $this->getTipoPersonaje(),
            "nivel" => $this->getNivel(),
            "puntosVida" => $this->getPuntosVida(),
            "energia" => $this->getEnergia(),
            "duelosGanados" => $this->getDuelosGanados(),
            "duelosPerdidos" => $this->getDuelosPerdidos(),
            "estado" => $this->getEstado(),
            "idArmaEquipada" => ($this->getArmaEquipada() !== null) ? $this->getArmaEquipada()->getId() : null 
        ];
        switch ($this->getTipoPersonaje()) {
        case 'guerrero':
            /** @var Guerrero $this */
            $datos["fuerza"] = $this->getFuerza();
            $datos["armadura"] = $this->getArmadura();
            break;

        case 'mago':
            /** @var Mago $this */
            $datos["mana"] = $this->getMana();
            $datos["inteligencia"] = $this->getInteligencia();
            break;

        case 'arquero':
            /** @var Arquero $this */
            $datos["precisionPersonaje"] = $this->getPrecision(); 
            $datos["velocidad"] = $this->getVelocidad();
            break;
    }
      /*  if ($this instanceof Guerrero) {
            $datos["fuerza"] = $this->getFuerza();
            $datos["armadura"] = $this->getArmadura();
        } elseif ($this instanceof Mago) {
            $datos["mana"] = $this->getMana();
            $datos["inteligencia"] = $this->getInteligencia();
        } elseif ($this instanceof Arquero) {
            $datos["precisionPersonaje"] = $this->getPrecision(); 
            $datos["velocidad"] = $this->getVelocidad();
        }*/

        if ($this->getId()) {
            $database->update("personajes", $datos, ["id" => $this->getId()]);
            $exito = true;
        } else {
            $database->insert("personajes", $datos);
            $this->setId($database->id());
            $exito = true;
        }
        return $exito;
    }
    

    public function borrar($database) {
        if ($this->getId()) {
            $resultado = $database->delete("personajes", [
                "id" => $this->getId()
            ]);

            if ($resultado) {
                $this->setId(null); 
                return true;
            }
        }
        return false;
    }

    public static function buscarPorId($database, $id) {
        $datos = $database->get("personajes", "*", ["id" => $id]);

        if (!$datos) {
            return null;
        }

        $tipo = $datos["tipoPersonaje"];
        $personaje = null;

        switch ($tipo) {
            case 'guerrero':
                $personaje = new Guerrero(
                $datos["nombre"],          
                $datos["tipoPersonaje"],   
                $datos["nivel"],          
                $datos["puntosVida"],      
                $datos["energia"],         
                $datos["duelosGanados"],   
                $datos["duelosPerdidos"],  
                $datos["estado"],          
                $datos["fuerza"],         
                $datos["armadura"],       
                $datos["id"],             
                null                      
            );
                break;
            case 'mago':
                $personaje = new Mago(
                $datos["nombre"],         
                $datos["tipoPersonaje"],   
                $datos["nivel"],           
                $datos["puntosVida"],      
                $datos["energia"],         
                $datos["duelosGanados"],   
                $datos["duelosPerdidos"],  
                $datos["estado"],         
                $datos["mana"],           
                $datos["inteligencia"],    
                $datos["id"],              
                null                       
            );
                break;
            case 'arquero':
                $personaje = new Arquero(
                $datos["nombre"],          
                $datos["tipoPersonaje"],   
                $datos["nivel"],           
                $datos["puntosVida"],      
                $datos["energia"],         
                $datos["duelosGanados"],   
                $datos["duelosPerdidos"],  
                $datos["estado"],          
                $datos["precisionPersonaje"], 
                $datos["velocidad"],       
                $datos["id"],              
                null                       
            );
                break;
        }
       /* if ($tipo === 'guerrero') {
            $personaje = new Guerrero(
                $datos["nombre"],          
                $datos["tipoPersonaje"],   
                $datos["nivel"],          
                $datos["puntosVida"],      
                $datos["energia"],         
                $datos["duelosGanados"],   
                $datos["duelosPerdidos"],  
                $datos["estado"],          
                $datos["fuerza"],         
                $datos["armadura"],       
                $datos["id"],             
                null                      
            );
        } elseif ($tipo === 'mago') {
            $personaje = new Mago(
                $datos["nombre"],         
                $datos["tipoPersonaje"],   
                $datos["nivel"],           
                $datos["puntosVida"],      
                $datos["energia"],         
                $datos["duelosGanados"],   
                $datos["duelosPerdidos"],  
                $datos["estado"],         
                $datos["mana"],           
                $datos["inteligencia"],    
                $datos["id"],              
                null                       
            );
        } elseif ($tipo === 'arquero') {
            $personaje = new Arquero(
                $datos["nombre"],          
                $datos["tipoPersonaje"],   
                $datos["nivel"],           
                $datos["puntosVida"],      
                $datos["energia"],         
                $datos["duelosGanados"],   
                $datos["duelosPerdidos"],  
                $datos["estado"],          
                $datos["precisionPersonaje"], 
                $datos["velocidad"],       
                $datos["id"],              
                null                       
            );
        }*/
        if ($datos["idArmaEquipada"] !== null) {
            $objetoArma = Arma::buscarPorId($database, $datos["idArmaEquipada"]);
            if ($personaje !== null) {
                $personaje->setArmaEquipada($objetoArma);
            }
        }

        return $personaje;
    }

    public static function listar($database) {
        $todosLosDatos = $database->select("personajes", "*");
        $listaPersonajes = [];
        //Recorremos los datos obtenidos de la base de datos y creamos instancias de los personajes según su tipo
        foreach ($todosLosDatos as $datos) {
            $tipo = $datos["tipoPersonaje"];
            $personaje = null;
            switch ($tipo) {
                case 'guerrero':
                    $personaje = new Guerrero(
                    $datos["nombre"], $datos["tipoPersonaje"], $datos["nivel"], $datos["puntosVida"], $datos["energia"], 
                    $datos["duelosGanados"], $datos["duelosPerdidos"], $datos["estado"], 
                    $datos["fuerza"], $datos["armadura"], $datos["id"], null
                );
                    break;
                case 'mago':
                    $personaje = new Mago(
                    $datos["nombre"], $datos["tipoPersonaje"], $datos["nivel"], $datos["puntosVida"], $datos["energia"], 
                    $datos["duelosGanados"], $datos["duelosPerdidos"], $datos["estado"], 
                    $datos["mana"], $datos["inteligencia"], $datos["id"], null
                );
                    break;
                case 'arquero':
                    $personaje = new Arquero(
                    $datos["nombre"], $datos["tipoPersonaje"], $datos["nivel"], $datos["puntosVida"], $datos["energia"], 
                    $datos["duelosGanados"], $datos["duelosPerdidos"], $datos["estado"], 
                    $datos["precisionPersonaje"], $datos["velocidad"], $datos["id"], null
                );
                    break;
            }
          /*  if ($tipo === 'guerrero') {
                $personaje = new Guerrero(
                    $datos["nombre"], $datos["tipoPersonaje"], $datos["nivel"], $datos["puntosVida"], $datos["energia"], 
                    $datos["duelosGanados"], $datos["duelosPerdidos"], $datos["estado"], 
                    $datos["fuerza"], $datos["armadura"], $datos["id"], null
                );
            } elseif ($tipo === 'mago') {
                $personaje = new Mago(
                    $datos["nombre"], $datos["tipoPersonaje"], $datos["nivel"], $datos["puntosVida"], $datos["energia"], 
                    $datos["duelosGanados"], $datos["duelosPerdidos"], $datos["estado"], 
                    $datos["mana"], $datos["inteligencia"], $datos["id"], null
                );
            } elseif ($tipo === 'arquero') {
                $personaje = new Arquero(
                    $datos["nombre"], $datos["tipoPersonaje"], $datos["nivel"], $datos["puntosVida"], $datos["energia"], 
                    $datos["duelosGanados"], $datos["duelosPerdidos"], $datos["estado"], 
                    $datos["precisionPersonaje"], $datos["velocidad"], $datos["id"], null
                );
            }*/
            if ($personaje !== null && $datos["idArmaEquipada"] !== null) {
                $objetoArma = Arma::buscarPorId($database, $datos["idArmaEquipada"]);
                $personaje->setArmaEquipada($objetoArma);
            }
            if ($personaje !== null) {
                $listaPersonajes[] = $personaje;
            }
        }
        return $listaPersonajes;
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
        if ($this->getArmaEquipada() != null) {
            $poderTotal += $this->getArmaEquipada()->calcularDanio();
        }
        return $poderTotal;
    }
    public function recibirRecompensas(){
        $this -> setNivel($this-> getNivel() + 1);
        $this -> setEnergia($this-> getEnergia() + 5);
        $this -> setDuelosGanados($this-> getDuelosGanados() + 1);
    }

    public function recibirCastigo($danio){
        $this->recibirDanio($danio);
        $this->setDuelosPerdidos($this->getDuelosPerdidos() + 1);
        $this->setEnergia($this->getEnergia() - 5);
    }

    //Metodo abstractos
    abstract public function calcularPoderBase();
    abstract public function calcularPoderEspecial();

    public function __toString()
    {
        $nombreArma = ($this->getArmaEquipada() != null) ? $this->getArmaEquipada()->getNombre() : "Ninguna";
        $mensaje = "Id: {$this->getId()}\n".
                   "Nombre: {$this->getNombre()}\n".
                   "Clase: {$this->getTipoPersonaje()}\n".
                   "Nivel: {$this->getNivel()}\n".
                   "Puntos de vida: {$this->getPuntosVida()}\n".
                   "Energia: {$this->getEnergia()}\n".
                   "Duelos Ganados: {$this->getDuelosGanados()}\n".
                   "Duelos Perdidos: {$this->getDuelosPerdidos()}\n".
                   "Estado: {$this->getEstado()}\n".
                   "Arma Equipada: {$nombreArma}\n";
        
        return $mensaje;
    }
}