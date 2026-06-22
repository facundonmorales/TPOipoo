<?php
class Duelo
{

    private $id;
    private $personaje1;
    private $personaje2;
    private $pwPersonaje1;
    private $pwPersonaje2;
    private $danioAplicado;
    private $arena;
    private $fecha;
    private $estado; //Estados: pendiente, realizado, cancelado 
    private $ganador;

    public function __construct(
        $personaje1,
        $personaje2,
        $arena,
        $fecha,
        $estado,
        $ganador = null,
        $id = null,
        $danioAplicado = null,
        $pwPersonaje1 = null,
        $pwPersonaje2 = null
    ) {
        $this->id = $id;
        $this->personaje1 = $personaje1;
        $this->personaje2 = $personaje2;
        $this->arena = $arena;
        $this->fecha = $fecha;
        $this->estado = $estado;
        $this->ganador = $ganador;

        // 💡 Los asignamos directamente acá
        $this->danioAplicado = $danioAplicado;
        $this->pwPersonaje1 = $pwPersonaje1;
        $this->pwPersonaje2 = $pwPersonaje2;
    }

    //Getters

    public function getPersonaje1()
    {
        return $this->personaje1;
    }
    public function getPersonaje2()
    {
        return $this->personaje2;
    }
    public function getArena()
    {
        return $this->arena;
    }
    public function getFecha()
    {
        return $this->fecha;
    }
    public function getEstado()
    {
        return $this->estado;
    }
    public function getGanador()
    {
        return $this->ganador;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getPwPersonaje1()
    {
        return $this->pwPersonaje1;
    }
    public function getPwPersonaje2()
    {
        return $this->pwPersonaje2;
    }
    public function getDanioAplicado(){
        return $this->danioAplicado;
    }


    //Setters

    public function setPersonaje1($personaje1)
    {
        $this->personaje1 = $personaje1;
    }
    public function setPersonaje2($personaje2)
    {
        $this->personaje2 = $personaje2;
    }
    public function setArena($arena)
    {
        $this->arena = $arena;
    }
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    public function setGanador($ganador)
    {
        $this->ganador = $ganador;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function setPwPersonaje1($pwPersonaje1)
    {
        $this->pwPersonaje1 = $pwPersonaje1;
    }
    public function setPwPersonaje2($pwPersonaje2)
    {
        $this->pwPersonaje2 = $pwPersonaje2;
    }
    public function setDanioAplicado($danioAplicado){
        $this->danioAplicado = $danioAplicado;
    }
    //consultas SQL

    public function guardar($database) {
        $datos = [
            "idPersonaje1"    => $this->getPersonaje1()->getId(),
            "idPersonaje2"    => $this->getPersonaje2()->getId(),
            "idArena"         => $this->getArena()->getId(),
            "fecha"           => $this->getFecha(),
            "estado"          => $this->getEstado(),
            "idGanador"       => ($this->getGanador() !== null) ? $this->getGanador()->getId() : null,
            "poderPersonaje1" => $this->getPwPersonaje1(),
            "poderPersonaje2" => $this->getPwPersonaje2(),
            "danioAplicado"   => $this->getDanioAplicado()
        ];

        if ($this->getId()) {
            $database->update("duelos", $datos, ["id" => $this->getId()]);
        } else {
            $database->insert("duelos", $datos);
            $this->setId($database->id());
        }
    }

    public static function buscarPorId($database, $id) {
        $datos = $database->get("duelos", "*", ["id" => $id]);
        $objetoDuelo = null; 

        if ($datos) {
            $personaje1 = Personaje::buscarPorId($database, $datos["idPersonaje1"]);
            $personaje2 = Personaje::buscarPorId($database, $datos["idPersonaje2"]);
            $arena      = Arena::buscarPorId($database, $datos["idArena"]);
            
            $ganador = null;
            if (!empty($datos["idGanador"])) {
                $ganador = Personaje::buscarPorId($database, $datos["idGanador"]);
            }
            $objetoDuelo = new Duelo(
                $personaje1,
                $personaje2,
                $arena,
                $datos["fecha"],
                $datos["estado"],
                $ganador,
                $datos["id"],
                $datos["danioAplicado"],
                $datos["poderPersonaje1"],
                $datos["poderPersonaje2"]
            );
        }

        return $objetoDuelo;
    }

    public static function listar($database) {
        $todosLosDuelos = $database->select("duelos", "*");
        $listaDuelos = []; 

        foreach ($todosLosDuelos as $datos) {
            $listaDuelos[] = self::buscarPorId($database, $datos["id"]);
        }

        return $listaDuelos;
    }


    public function borrar($database) {
        $borradoExitoso = false; 

        if ($this->getId()) {
            $resultado = $database->delete("duelos", [
                "id" => $this->getId()
            ]);

            if ($resultado) {
                $this->setId(null); 
                $borradoExitoso = true;
            }
        }

        return $borradoExitoso;
    }


    public function puedeRealizarse()
    {
        $idPj1 = $this->getPersonaje1()->getId();
        $idPj2 = $this->getPersonaje2()->getId();
        $sePuedeRealizar = false;
        if ($idPj1 != $idPj2 && $this->getPersonaje1()->puedeDuelar() && $this->getPersonaje2()->puedeDuelar()) {
            $sePuedeRealizar = true;
        }
        return $sePuedeRealizar;
    }

   public function realizarDuelo($database)
    {
        $seRealizo = false;

        if ($this->puedeRealizarse()) {
            $personaje1 = $this->getPersonaje1();
            $personaje2 = $this->getPersonaje2();
            $this->setPwPersonaje1($personaje1->calcularPoderTotal() + $this->getArena()->calcularModificadorArena($personaje1));
            $this->setPwPersonaje2($personaje2->calcularPoderTotal() + $this->getArena()->calcularModificadorArena($personaje2));

            if ($this->getPwPersonaje1() > $this->getPwPersonaje2()) {
                $this->setGanador($personaje1);
                $this->setDanioAplicado($this->getPwPersonaje1() - $this->getPwPersonaje2());
                $this->getGanador()->recibirRecompensas();
                $this->getPersonaje2()->recibirCastigo($this->getDanioAplicado());

            } elseif ($this->getPwPersonaje2() > $this->getPwPersonaje1()) {
                $this->setGanador($personaje2);
                $this->setDanioAplicado($this->getPwPersonaje2() - $this->getPwPersonaje1());
                $this->getGanador()->recibirRecompensas();
                $this->getPersonaje1()->recibirCastigo($this->getDanioAplicado());

            } else {
                // Empate: nadie gana ni pierde, daño 0
                $this->setGanador(null);
                $this->setDanioAplicado(0);
            }

            $this->setEstado('realizado');
            $seRealizo = true;
            $personaje1->guardar($database);
            $personaje2->guardar($database);

        } else {
            $this->setEstado('cancelado');
            $this->setPwPersonaje1(null);
            $this->setPwPersonaje2(null);
            $this->setDanioAplicado(null);
        }
        $this->guardar($database);

        return $seRealizo;
    }

    public function obtenerGanador()
    {
        return $this->getGanador();
    }
}