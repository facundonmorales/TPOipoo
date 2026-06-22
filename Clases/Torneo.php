<?php

class Torneo {
    private $database;
    private $personajes;
    private $armas;
    private $arenas;
    private $duelos;

    public function __construct($database) {
        $this->database = $database;
        $this->personajes = [];
        $this->armas = [];
        $this->arenas = [];
        $this->duelos = [];
    }

    public function getDatabase() {
        return $this->database;
    }
    public function setDatabase($database) {
        $this->database = $database;
    }

    public function getPersonajes() {
        return $this->personajes;
    }
    public function setPersonajes($personajes) {
        $this->personajes = $personajes;
    }

    public function getArmas() {
        return $this->armas;
    }
    public function setArmas($armas) {
        $this->armas = $armas;
    }

    public function getArenas() {
        return $this->arenas;
    }
    public function setArenas($arenas) {
        $this->arenas = $arenas;
    }

    public function getDuelos() {
        return $this->duelos;
    }
    public function setDuelos($duelos) {
        $this->duelos = $duelos;
    }
    public function agregarPersonaje($personaje) {
        $personaje->guardar($this->database);
        $coleccionActual = $this->getPersonajes();
        $coleccionActual[] = $personaje; 
        
        $this->setPersonajes($coleccionActual); 
    }

    public function agregarArma($arma) {
        $arma->guardar($this->database);
        $coleccionActual = $this->getArmas();
        $coleccionActual[] = $arma;
        
        $this->setArmas($coleccionActual);
    }


    public function agregarArena($arena) {
        $arena->guardar($this->database);
        $coleccionActual = $this->getArenas();
        $coleccionActual[] = $arena;
        
        $this->setArenas($coleccionActual);
    }
    public function equiparArma($personaje, $arma) {
        $sePudoEquipar = false;
         if ($arma->puedeSerEquipadaPor($personaje)) {
            if ($personaje->getArmaEquipada() !== null) {
                $armaVieja = $personaje->getArmaEquipada();
                $armaVieja->setEstado('disponible');
                $armaVieja->guardar($this->getDatabase());
            }
            $personaje->setArmaEquipada($arma);
            $arma->setEstado('equipada');
            $personaje->guardar($this->getDatabase());
            $arma->guardar($this->getDatabase());

            $sePudoEquipar = true;
        }

        return $sePudoEquipar;
    }

    public function registrarDuelo($personaje1, $personaje2, $arena) {
        $fechaActual = date("Y-m-d H:i:s");
        $duelo = new Duelo(
            $personaje1,
            $personaje2,
            $arena,
            $fechaActual,
            'pendiente' 
        );

        $duelo->guardar($this->getDatabase());
        $coleccionActual = $this->getDuelos();
        $coleccionActual[] = $duelo;
        $this->setDuelos($coleccionActual);

        return $duelo;
    }

    public function listarPersonajes($estado = null) {
        $condiciones = [];
        if ($estado !== null) {
            $condiciones = ["estado" => $estado];
        }
        $personajesDesdeBD = Personaje::listar($this->getDatabase(), $condiciones);
        if ($estado === null) {
            $this->setPersonajes($personajesDesdeBD);
        }

        return $personajesDesdeBD;
    }           

    public function listarArmas() {
        $armasDesdeBD = Arma::listar($this->getDatabase());
        $this->setArmas($armasDesdeBD);

        return $armasDesdeBD;
    }
    public function listarDuelos(){}
    public function rankingPersonajes(){}
}