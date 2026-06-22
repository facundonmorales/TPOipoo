<?php
include_once "Personaje.php";
include_once "Guerrero.php";
include_once "Mago.php";
include_once "Arquero.php";
include_once "Arma.php";
include_once "Arena.php";
include_once "Duelo.php";

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

    public function realizarDuelo($duelo) {
        return $duelo->realizarDuelo($this->getDatabase());
    }

    public function listarPersonajes($estado = null) {
        $todos = Personaje::listar($this->getDatabase());
        if ($estado !== null) {
            $filtrados = array_values(array_filter($todos, fn($p) => $p->getEstado() === $estado));
            return $filtrados;
        }
        $this->setPersonajes($todos);
        return $todos;
    }

    public function listarArmas() {
        $armasDesdeBD = Arma::listar($this->getDatabase());
        $this->setArmas($armasDesdeBD);
        return $armasDesdeBD;
    }

    public function listarArenas() {
        $arenasDesdeBD = Arena::listar($this->getDatabase());
        $this->setArenas($arenasDesdeBD);
        return $arenasDesdeBD;
    }

    public function listarDuelos() {
        $duelosDesdeBD = Duelo::listar($this->getDatabase());
        $this->setDuelos($duelosDesdeBD);
        return $duelosDesdeBD;
    }

    public function rankingPersonajes() {
        $personajes = Personaje::listar($this->getDatabase());
        usort($personajes, fn($a, $b) => $b->getDuelosGanados() - $a->getDuelosGanados());
        return $personajes;
    }
}