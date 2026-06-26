<?php
include 'Guerrero.php';
include 'Mago.php';
include 'Arquero.php';
class Arena {
    private $id;
    private $nombre;
    private $dificultad;
    private $capacidadPublico;
    private $clima; //climas posibles: normal, lluvia, tormenta, niebla

    public function __construct($nombre, $dificultad, $capacidadPublico, $clima, $id = null)
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

    //Persistencia
        public function guardar($database) {
        $exito = false;
        $datos = [
            "nombre" => $this->getNombre(),
            "dificultad" => $this->getDificultad(),
            "capacidadPublico" => $this->getCapacidadPublico(),
            "clima" => $this->getClima()
        ];

        if ($this->getId()) {
            $database->update("arenas", $datos, ["id" => $this->getId()]);
            $exito = true;
        } else {
            $database->insert("arenas", $datos);
            $this->setId($database->id());
            $exito = true;
        }
        return $exito;
    }

    
    public static function buscarPorId($database, $id) {
        $datos = $database->get("arenas", "*", ["id" => $id]);
        $objetoArena = null; 

        if ($datos) {
            $objetoArena = new Arena(
                $datos["nombre"],
                $datos["dificultad"],
                $datos["capacidadPublico"],
                $datos["clima"],
                $datos["id"]
            );
        }

        return $objetoArena;
    }

    public static function listar($database) {
        $todasLasArenas = $database->select("arenas", "*");
        $listaArenas = []; 

        foreach ($todasLasArenas as $datos) {
            $listaArenas[] = new Arena(
                $datos["nombre"],
                $datos["dificultad"],
                $datos["capacidadPublico"],
                $datos["clima"],
                $datos["id"]
            );
        }

        return $listaArenas;
    }

    public function borrar($database) {
        if ($this->getId()) {
            $resultado = $database->delete("arenas", [
                "id" => $this->getId()
            ]);
            if ($resultado) {
                $this->setId(null); 
                return true;
            }
        }
        return false;
    }

    //Metodos
    public function calcularModificadorArena(Personaje $personaje){
        $tipo = $personaje->getTipoPersonaje();
        $clima = $this->getClima();
        $modificador = 0;
        
        switch ($clima) {
            case "lluvia":
                switch ($tipo){
                    case "arquero":
                        $modificador = -10;
                        break;
                    case "guerrero":
                        $modificador = 0;
                        break;
                    case "mago":
                        $modificador = 5;
                        break;
                }
                break;
            case "tormenta":
                switch ($tipo){
                    case "arquero":
                        $modificador = -5;
                        break;
                    case "guerrero":
                        $modificador = -5;
                        break;
                    case "mago":
                        $modificador = 15;
                        break;
                }
                break;
            case "niebla":
                switch ($tipo){
                    case "arquero":
                        $modificador = -15;
                        break;
                    case "guerrero":
                        $modificador = 5;
                        break;
                    case "mago":
                        $modificador = 0;
                        break;
                }
                break;
            case "normal":
                $modificador = 0;
                break;
        }

        return $modificador;
    }
    
}


