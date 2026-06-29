<?php
require_once 'BDD/config.php';
require_once 'Clases/Personaje.php';
require_once 'Clases/Arma.php';
require_once 'Clases/Arena.php';
require_once 'Clases/Duelo.php';
require_once 'Clases/Torneo.php';

$torneo = new Torneo($database);

// Metodos para menu

function leer($mensaje = "") {
    if ($mensaje) echo $mensaje;
    return trim(fgets(STDIN));
}

function separador() {
    echo "\n" . str_repeat("-", 45) . "\n";
}

function pausar() {
    leer("\nPresione ENTER para continuar...");
}

//Menu principal 

function menuPrincipal() {
    echo "\n\n  *** LOS JUEGOS DEL HAMBRE ***\n\n";
    echo "   1.  Registrar personaje\n";
    echo "   2.  Registrar arma\n";
    echo "   3.  Registrar arena\n";
    echo "   4.  Equipar arma a personaje\n";
    echo "   5.  Registrar duelo\n";
    echo "   6.  Ejecutar duelos pendientes\n";
    echo "   7.  Recuperar personaje lesionado\n";
    echo "   8.  Listar personajes\n";
    echo "   9.  Listar armas disponibles\n";
    echo "  10.  Listar duelos\n";
    echo "  11.  Arma equipada por personaje\n";
    echo "  12.  Historial de personaje\n";
    echo "  13.  Rankings\n";
    echo "  14.  Porcentaje de victorias\n";
    echo "  15.  Arena con mas duelos\n";
    echo "  16.  Menu modificar\n";
    echo "  17.  Menu eliminar\n";
    echo "  18.  Buscar Personaje por ID\n";
    echo "  19.  Buscar Arma por ID\n";
    echo "  20.  Buscar Arena por ID\n";
    echo "  21.  Buscar Duelo por ID\n";
    echo "   0.  Salir\n";
    separador();
    return leer("  Opcion: ");
}

function menuModificar($torneo) {
    echo "\n\n  *** MODIFICAR ***\n\n";
    echo "   1.  Modificar personaje\n";
    echo "   2.  Modificar arma\n";
    echo "   3.  Modificar arena\n";
    echo "   4.  Modificar duelo\n";
    echo "   0.  Volver al menu principal\n";
    separador();
    $opcion = leer("  Opcion: ");
    switch ($opcion) {
        case "1": modificarPersonaje($torneo); break;
        case "2": modificarArma($torneo);       break;
        case "3": modificarArena($torneo);      break;
        case "4": modificarDuelo($torneo);      break;
        case "0": return;                       break;
        default: echo "  Opcion invalida.\n"; pausar();
    }
}
function menuBorrar($torneo) {
    echo "\n\n  *** ELIMINAR ***\n\n";
    echo "   1.  Eliminar personaje\n";
    echo "   2.  Eliminar arma\n";
    echo "   3.  Eliminar duelo\n";
    echo "   0.  Volver al menu principal\n";
    separador();
    $opcion = leer("  Opcion: ");
    switch ($opcion) {
        case "1": borrarPersonaje($torneo); break;
        case "2": eliminarArma($torneo);     break;
        case "3": borrarDuelo($torneo);      break;
        case "0": return;                    break;
        default: echo "  Opcion invalida.\n"; pausar();
    }
}

//Registrar personaje

function registrarPersonaje($torneo) {
    separador();
    echo "  REGISTRAR PERSONAJE\n";
    $nombre  = leer("  Nombre: ");
    $nivel   = (int) leer("  Nivel: ");
    $vida    = (int) leer("  Puntos de vida: ");
    $energia = (int) leer("  Energia: ");

    echo "  Tipo: 1) Guerrero  2) Mago  3) Arquero\n";
    $tipo = leer("  Opcion: ");

    switch ($tipo) {
        case "1":
            $personaje = new Guerrero(
                $nombre, 'guerrero', $nivel, $vida, $energia, 0, 0, 'disponible',
                (int) leer("  Fuerza: "),
                (int) leer("  Armadura: ")
            );
            break;
        case "2":
            $personaje = new Mago(
                $nombre, 'mago', $nivel, $vida, $energia, 0, 0, 'disponible',
                (int) leer("  Mana: "),
                (int) leer("  Inteligencia: ")
            );
            break;
        case "3":
            $personaje = new Arquero(
                $nombre, 'arquero', $nivel, $vida, $energia, 0, 0, 'disponible',
                (int) leer("  Precision: "),
                (int) leer("  Velocidad: ")
            );
            break;
        default:
            echo "  Opcion invalida.\n";
            return;
    }

    $torneo->agregarPersonaje($personaje);
    echo "  Personaje '{$nombre}' registrado (ID: {$personaje->getId()})\n";
    pausar();
}
function modificarPersonaje($torneo) {
    separador();
    echo "  MODIFICAR PERSONAJE\n\n";

    $personajes = $torneo->listarPersonajes();
    if (empty($personajes)) { echo "  No hay personajes registrados.\n"; pausar(); return; }

    foreach ($personajes as $p) {
        echo "  [{$p->getId()}] {$p->getNombre()} ({$p->getTipoPersonaje()}) Nv.{$p->getNivel()} | {$p->getEstado()}\n";
    }

    $id = (int) leer("\n  ID del personaje a modificar: ");
    $personaje = Personaje::buscarPorId($torneo->getDatabase(), $id);
    if (!$personaje) { echo "  Personaje no encontrado.\n"; pausar(); return; }

    echo "\n  Modificando '{$personaje->getNombre()}' (ID: {$personaje->getId()})\n";
    $nuevoNombre = leer("  Nuevo nombre (actual: {$personaje->getNombre()}): ");
    if ($nuevoNombre) $personaje->setNombre($nuevoNombre);

    $nuevoNivel = leer("  Nuevo nivel (actual: {$personaje->getNivel()}): ");
    if ($nuevoNivel !== "") $personaje->setNivel((int)$nuevoNivel);

    $nuevoVida = leer("  Nuevos puntos de vida (actual: {$personaje->getPuntosVida()}): ");
    if ($nuevoVida !== "") $personaje->setPuntosVida((int)$nuevoVida);

    $nuevoEnergia = leer("  Nueva energia (actual: {$personaje->getEnergia()}): ");
    if ($nuevoEnergia !== "") $personaje->setEnergia((int)$nuevoEnergia);

    // Guardar cambios
    if ($personaje->guardar($torneo->getDatabase())) {
        echo "  Personaje modificado exitosamente.\n";
    }
    pausar();
}
function borrarPersonaje($torneo) {
    separador();
    echo "  ELIMINAR PERSONAJE\n\n";

    $personajes = $torneo->listarPersonajes();
    if (empty($personajes)) { echo "  No hay personajes registrados.\n"; pausar(); return; }

    foreach ($personajes as $p) {
        echo "  [{$p->getId()}] {$p->getNombre()} ({$p->getTipoPersonaje()}) Nv.{$p->getNivel()} | {$p->getEstado()}\n";
    }

    $id = (int) leer("\n  ID del personaje a eliminar: ");
    $personaje = Personaje::buscarPorId($torneo->getDatabase(), $id);
    if (!$personaje) { echo "  Personaje no encontrado.\n"; pausar(); return; }

    if ($personaje->borrar($torneo->getDatabase())) {
        echo "  Personaje '{$personaje->getNombre()}' eliminado exitosamente.\n";
    } else {
        echo "  Error al eliminar el personaje.\n";
    }
    pausar();
}

//Registrar arma

function registrarArma($torneo) {
    separador();
    echo "  REGISTRAR ARMA\n";
    $nombre      = leer("  Nombre: ");
    $tipo        = leer("  Tipo (espada/arco/baculo/hacha/daga): ");
    $danioBase   = (int) leer("  Danio base: ");
    $nivelMinimo = (int) leer("  Nivel minimo requerido: ");
    $estado = leer("  Estado: disponible / rota:  ");
    $arma = new Arma($nombre, $tipo, $danioBase, $nivelMinimo, $estado);
    $torneo->agregarArma($arma);
    echo "  Arma '{$nombre}' registrada (ID: {$arma->getId()})\n";
    pausar();
}
function modificarArma($torneo) {
    separador();
    echo "  MODIFICAR ARMA\n\n";

    $armas = Arma::listar($torneo->getDatabase());
    if (empty($armas)) { echo "  No hay armas registradas.\n"; pausar(); return; }

    foreach ($armas as $a) {
        echo "  [{$a->getId()}] {$a->getNombre()} ({$a->getTipo()}) - Danio: {$a->getDanioBase()} - Nv.Min: {$a->getNivelMinimo()} | {$a->getEstado()}\n";
    }

    $id = (int) leer("\n  ID del arma a modificar: ");
    $arma = Arma::buscarPorId($torneo->getDatabase(), $id);
    if (!$arma) { echo "  Arma no encontrada.\n"; pausar(); return; }

    echo "\n  Modificando '{$arma->getNombre()}' (ID: {$arma->getId()})\n";
    $nuevoNombre = leer("  Nuevo nombre (actual: {$arma->getNombre()}): ");
    if ($nuevoNombre) $arma->setNombre($nuevoNombre);

    $nuevoTipo = leer("  Nuevo tipo (actual: {$arma->getTipo()}): ");
    if ($nuevoTipo) $arma->setTipo($nuevoTipo);

    $nuevoDanio = leer("  Nuevo danio base (actual: {$arma->getDanioBase()}): ");
    if ($nuevoDanio !== "") $arma->setDanioBase((int)$nuevoDanio);

    $nuevoNivelMinimo = leer("  Nuevo nivel minimo (actual: {$arma->getNivelMinimo()}): ");
    if ($nuevoNivelMinimo !== "") $arma->setNivelMinimo((int)$nuevoNivelMinimo);

    $nuevoEstado = leer("  Nuevo Estado (actual: {$arma->getEstado()}): ");
    if ($nuevoEstado) $arma->setEstado($nuevoEstado);

    // Guardar cambios
    if ($arma->guardar($torneo->getDatabase())) {
        echo "  Arma modificada exitosamente.\n";
    } else {
        echo "  Error al modificar el arma.\n";
    }
    pausar();
}

function eliminarArma($torneo) {
    separador();
    echo "  ELIMINAR ARMA\n\n";

    $armas = Arma::listar($torneo->getDatabase());
    if (empty($armas)) { echo "  No hay armas registradas.\n"; pausar(); return; }

    foreach ($armas as $a) {
        echo "  [{$a->getId()}] {$a->getNombre()} ({$a->getTipo()}) - Danio: {$a->getDanioBase()} - Nv.Min: {$a->getNivelMinimo()} | {$a->getEstado()}\n";
    }

    $id = (int) leer("\n  ID del arma a eliminar: ");
    $arma = Arma::buscarPorId($torneo->getDatabase(), $id);
    if (!$arma) { echo "  Arma no encontrada.\n"; pausar(); return; }

    if ($arma->borrar($torneo->getDatabase())) {
        echo "  Arma '{$arma->getNombre()}' eliminada exitosamente.\n";
    } else {
        echo "  Error al eliminar el arma.\n";
    }
    pausar();
}


//Registrar arena

function registrarArena($torneo) {
    separador();
    echo "  REGISTRAR ARENA\n";
    $nombre     = leer("  Nombre: ");
    $dificultad = (int) leer("  Dificultad (1-5): ");
    $capacidad  = (int) leer("  Capacidad de publico: ");

    $climas = ["1" => "normal", "2" => "lluvia", "3" => "tormenta", "4" => "niebla"];
    echo "  Clima: 1) Normal  2) Lluvia  3) Tormenta  4) Niebla\n";
    $clima = $climas[leer("  Opcion: ")] ?? "normal";

    $arena = new Arena($nombre, $dificultad, $capacidad, $clima);
    $torneo->agregarArena($arena);
    echo "  Arena '{$nombre}' registrada (ID: {$arena->getId()})\n";
    pausar();
}
function modificarArena($torneo) {
    separador();
    echo "  MODIFICAR ARENA\n\n";

    $arenas = Arena::listar($torneo->getDatabase());
    if (empty($arenas)) { echo "  No hay arenas registradas.\n"; pausar(); return; }

    foreach ($arenas as $a) {
        echo "  [{$a->getId()}] {$a->getNombre()} - Clima: {$a->getClima()} - Dificultad: {$a->getDificultad()} - Capacidad: {$a->getCapacidadPublico()}\n";
    }

    $id = (int) leer("\n  ID de la arena a modificar: ");
    $arena = Arena::buscarPorId($torneo->getDatabase(), $id);
    if (!$arena) { echo "  Arena no encontrada.\n"; pausar(); return; }

    echo "\n  Modificando '{$arena->getNombre()}' (ID: {$arena->getId()})\n";
    $nuevoNombre = leer("  Nuevo nombre (actual: {$arena->getNombre()}): ");
    if ($nuevoNombre) $arena->setNombre($nuevoNombre);

    $nuevoDificultad = leer("  Nueva dificultad (actual: {$arena->getDificultad()}): ");
    if ($nuevoDificultad !== "") $arena->setDificultad((int)$nuevoDificultad);

    $nuevoCapacidad = leer("  Nueva capacidad de publico (actual: {$arena->getCapacidadPublico()}): ");
    if ($nuevoCapacidad !== "") $arena->setCapacidadPublico((int)$nuevoCapacidad);

    $climas = ["1" => "normal", "2" => "lluvia", "3" => "tormenta", "4" => "niebla"];
    echo "  Clima: 1) Normal  2) Lluvia  3) Tormenta  4) Niebla\n";
    $nuevoClima = $climas[leer("  Opcion (actual: {$arena->getClima()}): ")] ?? null;
    if ($nuevoClima) $arena->setClima($nuevoClima);

    // Guardar cambios
    if ($arena->guardar($torneo->getDatabase())) {
        echo "  Arena modificada exitosamente.\n";
    } else {
        echo "  Error al modificar la arena.\n";
    }
}

//Equipar arma

function equiparArma($torneo) {
    separador();
    echo "  EQUIPAR ARMA\n\n";

    $personajes = $torneo->listarPersonajes('disponible');
    if (empty($personajes)) { echo "  No hay personajes disponibles.\n"; pausar(); return; }

    foreach ($personajes as $p) {
        $armaActual = $p->getArmaEquipada() ? $p->getArmaEquipada()->getNombre() : "ninguna";
        echo "  [{$p->getId()}] {$p->getNombre()} (Nv.{$p->getNivel()}) - Arma: {$armaActual}\n";
    }

    $personaje = Personaje::buscarPorId($torneo->getDatabase(), (int) leer("\n  ID del personaje: "));
    if (!$personaje) { echo "  Personaje no encontrado.\n"; pausar(); return; }

    $armas = array_filter(Arma::listar($torneo->getDatabase()), fn($a) => $a->getEstado() === 'disponible');
    if (empty($armas)) { echo "  No hay armas disponibles.\n"; pausar(); return; }

    echo "\n  Armas disponibles:\n";
    foreach ($armas as $a) {
        echo "  [{$a->getId()}] {$a->getNombre()} - Danio: {$a->getDanioBase()} - Nv.Min: {$a->getNivelMinimo()}\n";
    }

    $arma = Arma::buscarPorId($torneo->getDatabase(), (int) leer("\n  ID del arma: "));
    if (!$arma) { echo "  Arma no encontrada.\n"; pausar(); return; }

    if ($torneo->equiparArma($personaje, $arma)) {
        echo "  '{$arma->getNombre()}' equipada a '{$personaje->getNombre()}' exitosamente.\n";
    } else {
        echo "  No se pudo equipar: verificar nivel minimo o estado del arma.\n";
    }
    pausar();
}

// Registrar duelo

function registrarDuelo($torneo) {
    separador();
    echo "  REGISTRAR DUELO\n\n";

    $personajes = $torneo->listarPersonajes('disponible');
    if (count($personajes) < 2) { echo "  Se necesitan al menos 2 personajes disponibles.\n"; pausar(); return; }

    foreach ($personajes as $p) {
        echo "  [{$p->getId()}] {$p->getNombre()} ({$p->getTipoPersonaje()}) Nv.{$p->getNivel()}\n";
    }

    $idP1 = (int) leer("\n  ID personaje 1: ");
    $idP2 = (int) leer("  ID personaje 2: ");

    if ($idP1 === $idP2) { echo "  Un personaje no puede duelar contra si mismo.\n"; pausar(); return; }

    $p1 = Personaje::buscarPorId($torneo->getDatabase(), $idP1);
    $p2 = Personaje::buscarPorId($torneo->getDatabase(), $idP2);
    if (!$p1 || !$p2) { echo "  Personaje(s) no encontrado(s).\n"; pausar(); return; }

    $arenas = Arena::listar($torneo->getDatabase());
    if (empty($arenas)) { echo "  No hay arenas registradas.\n"; pausar(); return; }

    echo "\n  Arenas disponibles:\n";
    foreach ($arenas as $a) {
        echo "  [{$a->getId()}] {$a->getNombre()} - Clima: {$a->getClima()} - Dificultad: {$a->getDificultad()}\n";
    }

    $arena = Arena::buscarPorId($torneo->getDatabase(), (int) leer("\n  ID de la arena: "));
    if (!$arena) { echo "  Arena no encontrada.\n"; pausar(); return; }

    $duelo = $torneo->registrarDuelo($p1, $p2, $arena);
    echo "  Duelo registrado (ID: {$duelo->getId()}) - estado: pendiente\n";
    pausar();
}
function modificarDuelo($torneo) {
    separador();
    echo "  MODIFICAR DUELO\n\n";

    $duelos = Duelo::listar($torneo->getDatabase());
    if (empty($duelos)) { echo "  No hay duelos registrados.\n"; pausar(); return; }

    foreach ($duelos as $d) {
        echo "  [{$d->getId()}] {$d->getPersonaje1()->getNombre()} vs {$d->getPersonaje2()->getNombre()} | Arena: {$d->getArena()->getNombre()} | Estado: {$d->getEstado()}\n";
    }

    $id = (int) leer("\n  ID del duelo a modificar: ");
    $duelo = Duelo::buscarPorId($torneo->getDatabase(), $id);
    if (!$duelo) { echo "  Duelo no encontrado.\n"; pausar(); return; }

    echo "\n  Modificando duelo #{$duelo->getId()}\n";
    echo "  Estado actual: {$duelo->getEstado()}\n";
    $nuevoEstado = leer("  Nuevo estado (pendiente/realizado): ");
    if ($nuevoEstado === 'pendiente' || $nuevoEstado === 'realizado') {
        $duelo->setEstado($nuevoEstado);
        if ($duelo->guardar($torneo->getDatabase())) {
            echo "  Duelo modificado exitosamente.\n";
        } else {
            echo "  Error al modificar el duelo.\n";
        }
    } else {
        echo "  Estado invalido.\n";
    }
    pausar();
}

function borrarDuelo($torneo) {
    separador();
    echo "  ELIMINAR DUELO\n\n";

    $duelos = Duelo::listar($torneo->getDatabase());
    if (empty($duelos)) { echo "  No hay duelos registrados.\n"; pausar(); return; }

    foreach ($duelos as $d) {
        echo "  [{$d->getId()}] {$d->getPersonaje1()->getNombre()} vs {$d->getPersonaje2()->getNombre()} | Arena: {$d->getArena()->getNombre()} | Estado: {$d->getEstado()}\n";
    }

    $id = (int) leer("\n  ID del duelo a eliminar: ");
    $duelo = Duelo::buscarPorId($torneo->getDatabase(), $id);
    if (!$duelo) { echo "  Duelo no encontrado.\n"; pausar(); return; }

    if ($duelo->borrar($torneo->getDatabase())) {
        echo "  Duelo #{$duelo->getId()} eliminado exitosamente.\n";
    } else {
        echo "  Error al eliminar el duelo.\n";
    }
    pausar();
}

// Ejecutar duelos pendientes

function ejecutarDuelosPendientes($torneo) {
    separador();
    echo "  EJECUTAR DUELOS PENDIENTES\n\n";

    $pendientes = array_filter(Duelo::listar($torneo->getDatabase()), fn($d) => $d->getEstado() === 'pendiente');
    if (empty($pendientes)) { echo "  No hay duelos pendientes.\n"; pausar(); return; }

    foreach ($pendientes as $duelo) {
        $p1 = $duelo->getPersonaje1();
        $p2 = $duelo->getPersonaje2();
        echo "  Duelo #{$duelo->getId()}: {$p1->getNombre()} vs {$p2->getNombre()} en {$duelo->getArena()->getNombre()}\n";

        if (strtolower(leer("  Ejecutar? (s/n): ")) !== 's') continue;

        if ($duelo->realizarDuelo($torneo->getDatabase())) {
            $ganador  = $duelo->getGanador();
            $perdedor = ($ganador && $ganador->getId() === $p1->getId()) ? $p2 : $p1;
            echo "  Poder {$p1->getNombre()}: {$duelo->getPwPersonaje1()}\n";
            echo "  Poder {$p2->getNombre()}: {$duelo->getPwPersonaje2()}\n";
            echo "  Ganador: " . ($ganador ? $ganador->getNombre() : "Empate") . "\n";
            if ($ganador) echo "  Danio aplicado: {$duelo->getDanioAplicado()} | {$perdedor->getNombre()} quedo: {$perdedor->getEstado()}\n";
        } else {
            echo "  Duelo cancelado.\n";
        }
        echo "\n";
    }
    pausar();
}

//Recuperar lesionado

function recuperarLesionado($torneo) {
    separador();
    echo "  RECUPERAR PERSONAJE LESIONADO\n\n";

    $lesionados = $torneo->listarPersonajes('lesionado');
    if (empty($lesionados)) { echo "  No hay personajes lesionados.\n"; pausar(); return; }

    foreach ($lesionados as $p) {
        echo "  [{$p->getId()}] {$p->getNombre()} - Vida: {$p->getPuntosVida()}\n";
    }

    $personaje = Personaje::buscarPorId($torneo->getDatabase(), (int) leer("\n  ID del personaje: "));
    if (!$personaje || $personaje->getEstado() !== 'lesionado') {
        echo "  Personaje no encontrado o no esta lesionado.\n"; pausar(); return;
    }

    $personaje->recuperarVida((int) leer("  Puntos de vida a recuperar: "));

    if ($personaje->getPuntosVida() > 30) {
        $personaje->setEstado('disponible');
        echo "  {$personaje->getNombre()} se recupero y esta disponible.\n";
    } else {
        echo "  {$personaje->getNombre()} sigue lesionado (Vida: {$personaje->getPuntosVida()})\n";
    }

    $personaje->guardar($torneo->getDatabase());
    pausar();
}

//Listar personajes

function listarPersonajes($torneo) {
    separador();
    echo "  PERSONAJES\n\n";
    echo "  Filtrar: 1) Todos  2) Disponibles  3) Lesionados  4) Retirados\n";
    $filtros = ["1" => null, "2" => "disponible", "3" => "lesionado", "4" => "retirado"];
    $estado  = $filtros[leer("  Opcion: ")] ?? null;
 
    // Usamos listarPersonajes() del torneo que ya devuelve objetos Personaje con el arma cargada
    $lista = $torneo->listarPersonajes($estado);
 
    echo "\n";
    foreach ($lista as $p) {
        $arma = $p->getArmaEquipada() ? $p->getArmaEquipada()->getNombre() : "sin arma";
        echo "  [{$p->getId()}] {$p->getNombre()} ({$p->getTipoPersonaje()}) Nv.{$p->getNivel()}";
        echo " | {$p->getEstado()} | Arma: {$arma}";
        echo " | V:{$p->getDuelosGanados()} D:{$p->getDuelosPerdidos()}\n";
    }
    if (empty($lista)) echo "  Sin resultados.\n";
    pausar();
}

//Listar armas disponibles
function listarArmasDisponibles($torneo) {
    separador();
    echo "  ARMAS DISPONIBLES\n\n";

    // Select directo con condicion de estado, sin JOIN
    $filas = $torneo->getDatabase()->select(
        "armas",
        ["id", "nombre", "tipo", "danioBase", "nivelMinimo"],
        ["estado" => "disponible"]
    );

    foreach ($filas as $fila) {
        echo "  [{$fila['id']}] {$fila['nombre']} ({$fila['tipo']}) - Danio: {$fila['danioBase']} - Nv.Min: {$fila['nivelMinimo']}\n";
    }
    if (empty($filas)) echo "  No hay armas disponibles.\n";
    pausar();
}

//Listar duelos

function listarDuelos($torneo) {
    separador();
    echo "  DUELOS\n\n";
    echo "  Filtrar: 1) Todos  2) Pendientes  3) Realizados\n";
    $op = leer("  Opcion: ");

    $estados = ["2" => "pendiente", "3" => "realizado"];
    $where   = isset($estados[$op]) ? ["duelos.estado" => $estados[$op]] : []; //

    // JOIN con personajes dos veces (p1 y p2), LEFT JOIN para el ganador (puede ser null), JOIN con arenas
    $filas = $torneo->getDatabase()->select(
        "duelos",
        [
            "[>]personajes(p1)" => ["idPersonaje1" => "id"],
            "[>]personajes(p2)" => ["idPersonaje2" => "id"],
            "[>]personajes(g)"  => ["idGanador"    => "id"],
            "[>]arenas"         => ["idArena"       => "id"]
        ],
        [
            "duelos.id",
            "duelos.estado",
            "p1.nombre(nombre1)",
            "p2.nombre(nombre2)",
            "g.nombre(ganador)",
            "arenas.nombre(arena)"
        ],
        $where // $where puede estar vacio para traer todos los duelos, o contener un filtro de estado
    );

    echo "\n";
    foreach ($filas as $fila) {
        $ganador = $fila['ganador'] ?? ($fila['estado'] === 'realizado' ? "Empate" : "-");
        echo "  #{$fila['id']} | {$fila['nombre1']} vs {$fila['nombre2']}";
        echo " | {$fila['arena']} | {$fila['estado']} | Ganador: {$ganador}\n";
    }
    if (empty($filas)) echo "  Sin resultados.\n";
    pausar();
}

//Arma equipada por personaje

function armaEquipadaPorPersonaje($torneo) {
    separador();
    echo "  ARMA EQUIPADA POR PERSONAJE\n\n";

    
    $filas = $torneo->getDatabase()->select(
        "personajes",
        ["[>]armas" => ["idArmaEquipada" => "id"]],
        [
            "personajes.nombre(personaje)",
            "armas.nombre(arma)"
        ]
    );

    foreach ($filas as $fila) {
        $arma = $fila['arma'] ?? "Ninguna";
        echo "  {$fila['personaje']}: {$arma}\n";
    }
    if (empty($filas)) echo "  Sin personajes registrados.\n";
    pausar();
}

//Historial de personaje

function historialPersonaje($torneo) {
    separador();
    echo "  HISTORIAL DE PERSONAJE\n\n";

    foreach (Personaje::listar($torneo->getDatabase()) as $p) {
        echo "  [{$p->getId()}] {$p->getNombre()} ({$p->getTipoPersonaje()})\n";
    }

    $id = (int) leer("\n  ID del personaje: ");
    $personaje = Personaje::buscarPorId($torneo->getDatabase(), $id);
    if (!$personaje) { echo "  Personaje no encontrado.\n"; pausar(); return; }

    echo "\n  Historial de: {$personaje->getNombre()}\n";

    $filas = $torneo->getDatabase()->select(
        "duelos",
        [
            "[>]personajes(p1)" => ["idPersonaje1" => "id"],
            "[>]personajes(p2)" => ["idPersonaje2" => "id"],
            "[<]personajes(g)"  => ["idGanador"    => "id"],
            "[>]arenas"         => ["idArena"       => "id"]
        ],
        [
            "duelos.id",
            "duelos.fecha",
            "duelos.estado",
            "duelos.danioAplicado",
            "p1.nombre(nombre1)",
            "p2.nombre(nombre2)",
            "g.nombre(ganador)",
            "arenas.nombre(arena)"
        ],
        [
            "OR" => [
                "duelos.idPersonaje1" => $id,
                "duelos.idPersonaje2" => $id
            ]
        ]
    );

    if (empty($filas)) {
        echo "  Sin duelos registrados.\n";
    } else {
        foreach ($filas as $fila) {
            echo "\n  #{$fila['id']} {$fila['fecha']} | Arena: {$fila['arena']}";
            echo " | {$fila['nombre1']} vs {$fila['nombre2']} | {$fila['estado']}";
            if ($fila['estado'] === 'realizado') {
                $ganador = $fila['ganador'] ?? "Empate";
                echo " | Ganador: {$ganador} | Danio: {$fila['danioAplicado']}";
            }
            echo "\n";
        }
    }
    pausar();
}

//Rankings

function rankings($torneo) {
    separador();
    echo "  RANKINGS\n\n";
    echo "  1. Ranking por victorias\n";
    echo "  2. Campeon (mas victorias)\n";
    $op = leer("\n  Opcion: ");

    separador();

    switch ($op) {
        case "1":
            $pos = 1;
            foreach ($torneo->rankingPersonajes() as $p) {
                echo "  {$pos}. {$p->getNombre()} - Victorias: {$p->getDuelosGanados()} | Derrotas: {$p->getDuelosPerdidos()}\n";
                $pos++;
            }
            break;

        case "2":
            $ranking = $torneo->rankingPersonajes();
            if (!empty($ranking)) {
                $p = $ranking[0];
                echo "  Campeon: {$p->getNombre()} con {$p->getDuelosGanados()} victorias\n";
            } else {
                echo "  Sin datos.\n";
            }
            break;

        default:
            echo "  Opcion invalida.\n";
    }
    pausar();
}

//Porcentaje de victorias

function porcentajeVictorias($torneo) {
    separador();
    echo "  PORCENTAJE DE VICTORIAS POR PERSONAJE\n\n";

    foreach (Personaje::listar($torneo->getDatabase()) as $p) { //
        $total      = $p->getDuelosGanados() + $p->getDuelosPerdidos();
        $porcentaje = $total > 0 ? round($p->getDuelosGanados() / $total * 100, 1) : 0;
        echo "  {$p->getNombre()}: {$porcentaje}% ({$p->getDuelosGanados()} victorias / {$total} duelos)\n";
    }
    pausar();
}

//Arena con mas duelos

function arenaConMasDuelos($torneo) {
    separador();
    echo "  ARENA CON MAS DUELOS\n\n";

    $resultado = $torneo->getDatabase()->query(
        "SELECT a.nombre, COUNT(d.id) AS totalDuelos
         FROM arenas a
         LEFT JOIN duelos d ON d.idArena = a.id AND d.estado = 'realizado'
         GROUP BY a.id, a.nombre
         ORDER BY totalDuelos DESC
         LIMIT 1"   
    )->fetch(\PDO::FETCH_ASSOC); //

    if ($resultado) {
        echo "  Arena: {$resultado['nombre']} con {$resultado['totalDuelos']} duelos realizados\n";
    } else {
        echo "  Sin datos suficientes.\n";
    }
    pausar();
}

//Buscar personaje por ID

function buscarPersonajePorId($torneo){
    separador();
    echo "   BUSCAR PERSONAJE POR ID\n\n";

    $id = (int) leer("   Ingrese el ID del personaje: ");
    $pj = Personaje::buscarPorId($torneo->getDatabase(), $id);
    
    if (!$pj) {
        echo "   Personaje no encontrado.\n";
        pausar();
    } else{
        echo "\n   [ Detalle del Personaje ]\n";
        echo "   ID:          {$pj->getId()}\n";
        echo "   Nombre:      {$pj->getNombre()}\n";
        echo "   Tipo:        {$pj->getTipoPersonaje()}\n";
        echo "   Nivel:       {$pj->getNivel()}\n";
        echo "   Vida:        {$pj->getPuntosVida()}\n";
        echo "   Energía:     {$pj->getEnergia()}\n";
        echo "   Estado:      {$pj->getEstado()}\n";
        $arma = $pj->getArmaEquipada() ? $pj->getArmaEquipada()->getNombre() : "Ninguna";
        echo "   Arma:        {$arma}\n";
        echo "   Historial:   {$pj->getDuelosGanados()} Victorias / {$pj->getDuelosPerdidos()} Derrotas\n";
        pausar();
    }
}

//Buscar arma por ID

function buscarArmaPorId($torneo) {
    separador();
    echo "   BUSCAR ARMA POR ID\n\n";

    $id = (int) leer("   Ingrese el ID del arma: ");
    $arma = Arma::buscarPorId($torneo->getDatabase(), $id);
    
    if (!$arma) {
        echo "   Arma no encontrada.\n";
        pausar();
    } else {
        echo "\n   [ Detalle del Arma ]\n";
        echo "   ID:          {$arma->getId()}\n";
        echo "   Nombre:      {$arma->getNombre()}\n";
        echo "   Tipo:        {$arma->getTipo()}\n";
        echo "   Daño Base:   {$arma->getDanioBase()}\n";
        echo "   Nivel Mín:   {$arma->getNivelMinimo()}\n";
        echo "   Estado:      {$arma->getEstado()}\n";
        pausar();
    }
}

//Buscar arena por ID

function buscarArenaPorId($torneo) {
    separador();
    echo "   BUSCAR ARENA POR ID\n\n";

    $id = (int) leer("   Ingrese el ID de la arena: ");
    $arena = Arena::buscarPorId($torneo->getDatabase(), $id);
    
    if (!$arena) {
        echo "   Arena no encontrada.\n";
        pausar();
    } else {
        echo "\n   [ Detalle de la Arena ]\n";
        echo "   ID:          {$arena->getId()}\n";
        echo "   Nombre:      {$arena->getNombre()}\n";
        echo "   Dificultad:  {$arena->getDificultad()} (1-5)\n";
        echo "   Capacidad:   {$arena->getCapacidadPublico()} espectadores\n";
        echo "   Clima:       {$arena->getClima()}\n";
        pausar();
    }
}
function buscarDueloPorId($torneo) {
    separador();
    echo "   BUSCAR DUELO POR ID\n\n";

    $id = (int) leer("   Ingrese el ID del duelo: ");
    $duelo = Duelo::buscarPorId($torneo->getDatabase(), $id);
    
    if (!$duelo) {
        echo "   Duelo no encontrado.\n";
        pausar();
    } else {
        echo "\n   [ Detalle del Duelo ]\n";
        echo "   ID:          {$duelo->getId()}\n";
        echo "   Personaje 1: {$duelo->getPersonaje1()->getNombre()}\n";
        echo "   Personaje 2: {$duelo->getPersonaje2()->getNombre()}\n";
        echo "   Arena:       {$duelo->getArena()->getNombre()}\n";
        echo "   Estado:      {$duelo->getEstado()}\n";
        if ($duelo->getEstado() === 'realizado') {
            $ganador = $duelo->getGanador() ? $duelo->getGanador()->getNombre() : "Empate";
            echo "   Ganador:     {$ganador}\n";
            echo "   Daño Aplicado: {$duelo->getDanioAplicado()}\n";
        }
        pausar();
    }
}

do {
    $opcion = menuPrincipal();

    switch ($opcion) {
        case "1":  registrarPersonaje($torneo);       break;
        case "2":  registrarArma($torneo);            break;
        case "3":  registrarArena($torneo);           break;
        case "4":  equiparArma($torneo);              break;
        case "5":  registrarDuelo($torneo);           break;
        case "6":  ejecutarDuelosPendientes($torneo); break;
        case "7":  recuperarLesionado($torneo);       break;
        case "8":  listarPersonajes($torneo);         break;
        case "9":  listarArmasDisponibles($torneo);   break;
        case "10": listarDuelos($torneo);             break;
        case "11": armaEquipadaPorPersonaje($torneo); break;
        case "12": historialPersonaje($torneo);       break;
        case "13": rankings($torneo);                 break;
        case "14": porcentajeVictorias($torneo);      break;
        case "15": arenaConMasDuelos($torneo);        break;
        case "16": menuModificar($torneo);            break;
        case "17": menuBorrar($torneo);             break;
        case "18": buscarPersonajePorId($torneo); break;
        case "19": buscarArmaPorId($torneo); break;
        case "20": buscarArenaPorId($torneo); break;
        case "21": buscarDueloPorId($torneo); break;
        case "0":  echo "\n  Torneo finalizado.\n\n";       break;
        default:   echo "  Opcion invalida.\n"; pausar();
    }

} while ($opcion !== "0");
