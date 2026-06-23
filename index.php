<?php
require_once 'BDD/config.php';
require_once 'Clases/Personaje.php';
require_once 'Clases/Arma.php';
require_once 'Clases/Arena.php';
require_once 'Clases/Duelo.php';
require_once 'Clases/Torneo.php';

$torneo = new Torneo($database);

function leer($mensaje = "") {
    if ($mensaje) echo $mensaje;
    return trim(fgets(STDIN));
}
// Función para limpiar la pantalla en la consola, usamos el metodo str_repeat para crear una linea de separacion
function limpiarPantalla() {
    echo "\n" . str_repeat("=", 50) . "\n";
}

function pausar() {
    echo "\nPresione ENTER para continuar...";
    fgets(STDIN);
}

// ─────────────────────────────────────────
//  MENU PRINCIPAL
// ─────────────────────────────────────────
function menuPrincipal() {
    limpiarPantalla();
    echo "       *** LOS JUEGOS DEL HAMBRE ***\n";
    echo "          Sistema de Torneo Arena Masters\n";
    limpiarPantalla();
    echo "  1. Registrar personaje\n";
    echo "  2. Registrar arma\n";
    echo "  3. Registrar arena\n";
    echo "  4. Equipar arma a personaje\n";
    echo "  5. Registrar duelo\n";
    echo "  6. Ejecutar duelos pendientes\n";
    echo "  7. Recuperar personajes lesionados\n";
    echo "  8. Consultar rankings\n";
    echo "  9. Consultar historial de personaje\n";
    echo " 10. Consultas varias\n";
    echo "  0. Salir\n";
    limpiarPantalla();
    return leer("  Seleccione una opcion: ");
}

// ─────────────────────────────────────────
//  1. REGISTRAR PERSONAJE
// ─────────────────────────────────────────
function registrarPersonaje($torneo) {
    limpiarPantalla();
    echo "  --- REGISTRAR PERSONAJE ---\n\n";
    $nombre = leer("  Nombre: ");
    echo "  Tipo: 1) Guerrero  2) Mago  3) Arquero\n";
    $tipoOp = leer("  Opcion: ");

    $nivel    = (int) leer("  Nivel: "); //Usamos (int) para convertir la entrada a un número entero
    $vida     = (int) leer("  Puntos de vida: ");
    $energia  = (int) leer("  Energia: ");

    $personaje = null;
    // Dependiendo del tipo de personaje, solicitamos atributos específicos y creamos la instancia correspondiente
    if ($tipoOp == "1") {
        $fuerza   = (int) leer("  Fuerza: ");
        $armadura = (int) leer("  Armadura: ");
        $personaje = new Guerrero($nombre, 'guerrero', $nivel, $vida, $energia, 0, 0, 'disponible', $fuerza, $armadura);
    } elseif ($tipoOp == "2") {
        $mana         = (int) leer("  Mana: ");
        $inteligencia = (int) leer("  Inteligencia: ");
        $personaje = new Mago($nombre, 'mago', $nivel, $vida, $energia, 0, 0, 'disponible', $mana, $inteligencia);
    } elseif ($tipoOp == "3") {
        $precision = (int) leer("  Precision: ");
        $velocidad = (int) leer("  Velocidad: ");
        $personaje = new Arquero($nombre, 'arquero', $nivel, $vida, $energia, 0, 0, 'disponible', $precision, $velocidad);
    } else {
        echo "  Opcion invalida.\n";
        pausar();
        return;
    }

    $torneo->agregarPersonaje($personaje);
    echo "\n  Personaje '{$nombre}' registrado con ID: " . $personaje->getId() . "\n";
    pausar();
}

// ─────────────────────────────────────────
//  2. REGISTRAR ARMA
// ─────────────────────────────────────────
function registrarArma($torneo) {
    limpiarPantalla();
    echo "  --- REGISTRAR ARMA ---\n\n";
    $nombre      = leer("  Nombre: ");
    $tipo        = leer("  Tipo (espada/hacha/arco/baculo/daga): ");
    $danioBase   = (int) leer("  Danio base: ");
    $nivelMinimo = (int) leer("  Nivel minimo requerido: ");

    $arma = new Arma($nombre, $tipo, $danioBase, $nivelMinimo, 'disponible');
    $torneo->agregarArma($arma);
    echo "\n  Arma '{$nombre}' registrada con ID: " . $arma->getId() . "\n";
    pausar();
}

// ─────────────────────────────────────────
//  3. REGISTRAR ARENA
// ─────────────────────────────────────────
function registrarArena($torneo) {
    limpiarPantalla();
    echo "  --- REGISTRAR ARENA ---\n\n";
    $nombre     = leer("  Nombre: ");
    $dificultad = (int) leer("  Dificultad (1-5): ");
    $capacidad  = (int) leer("  Capacidad de publico: ");
    echo "  Clima: 1) Normal  2) Lluvia  3) Tormenta  4) Niebla\n";
    $climaOp = leer("  Opcion: ");
    $climas  = ["1" => "normal", "2" => "lluvia", "3" => "tormenta", "4" => "niebla"];
    $clima   = $climas[$climaOp] ?? "normal";

    $arena = new Arena($nombre, $dificultad, $capacidad, $clima);
    $torneo->agregarArena($arena);
    echo "\n  Arena '{$nombre}' registrada con ID: " . $arena->getId() . "\n";
    pausar();
}

// ─────────────────────────────────────────
//  4. EQUIPAR ARMA
// ─────────────────────────────────────────
function equiparArma($torneo) {
    limpiarPantalla();
    echo "  --- EQUIPAR ARMA ---\n\n";

    // Mostrar personajes disponibles
    $personajes = $torneo->listarPersonajes('disponible');
    if (empty($personajes)) {
        echo "  No hay personajes disponibles.\n";
        pausar();
        return;
    }
    echo "  Personajes disponibles:\n";
    foreach ($personajes as $p) {
        $armaActual = $p->getArmaEquipada() ? $p->getArmaEquipada()->getNombre() : "ninguna";
        echo "  [{$p->getId()}] {$p->getNombre()} (Nv.{$p->getNivel()}) - Arma: {$armaActual}\n";
    }

    $idPersonaje = (int) leer("\n  ID del personaje: ");
    $personaje   = Personaje::buscarPorId($database ?? $torneo->getDatabase(), $idPersonaje);
    if (!$personaje) {
        echo "  Personaje no encontrado.\n";
        pausar();
        return;
    }

    // Mostrar armas disponibles
    $armas = Arma::listar($torneo->getDatabase());
    $armasDisponibles = array_filter($armas, fn($a) => $a->getEstado() === 'disponible');
    if (empty($armasDisponibles)) {
        echo "  No hay armas disponibles.\n";
        pausar();
        return;
    }
    echo "\n  Armas disponibles:\n";
    foreach ($armasDisponibles as $a) {
        echo "  [{$a->getId()}] {$a->getNombre()} - Danio: {$a->getDanioBase()} - Nv.Min: {$a->getNivelMinimo()}\n";
    }

    $idArma = (int) leer("\n  ID del arma: ");
    $arma   = Arma::buscarPorId($torneo->getDatabase(), $idArma);
    if (!$arma) {
        echo "  Arma no encontrada.\n";
        pausar();
        return;
    }

    $resultado = $torneo->equiparArma($personaje, $arma);
    if ($resultado) {
        echo "\n  Arma '{$arma->getNombre()}' equipada a '{$personaje->getNombre()}' exitosamente.\n";
    } else {
        echo "\n  No se pudo equipar el arma. Verificar nivel minimo o estado del arma.\n";
    }
    pausar();
}

// ─────────────────────────────────────────
//  5. REGISTRAR DUELO
// ─────────────────────────────────────────
function registrarDuelo($torneo) {
    limpiarPantalla();
    echo "  --- REGISTRAR DUELO ---\n\n";

    $personajes = $torneo->listarPersonajes('disponible');
    if (count($personajes) < 2) {
        echo "  Se necesitan al menos 2 personajes disponibles para registrar un duelo.\n";
        pausar();
        return;
    }

    echo "  Personajes disponibles:\n";
    foreach ($personajes as $p) {
        echo "  [{$p->getId()}] {$p->getNombre()} ({$p->getTipoPersonaje()}) - Nv.{$p->getNivel()}\n";
    }

    $idP1 = (int) leer("\n  ID personaje 1: ");
    $idP2 = (int) leer("  ID personaje 2: ");

    if ($idP1 === $idP2) {
        echo "  Un personaje no puede duelar contra si mismo.\n";
        pausar();
        return;
    }

    $p1 = Personaje::buscarPorId($torneo->getDatabase(), $idP1);
    $p2 = Personaje::buscarPorId($torneo->getDatabase(), $idP2);

    if (!$p1 || !$p2) {
        echo "  Uno o ambos personajes no encontrados.\n";
        pausar();
        return;
    }

    // Mostrar arenas
    $arenas = Arena::listar($torneo->getDatabase());
    if (empty($arenas)) {
        echo "  No hay arenas registradas.\n";
        pausar();
        return;
    }
    echo "\n  Arenas disponibles:\n";
    foreach ($arenas as $a) {
        echo "  [{$a->getId()}] {$a->getNombre()} - Clima: {$a->getClima()} - Dificultad: {$a->getDificultad()}\n";
    }

    $idArena = (int) leer("\n  ID de la arena: ");
    $arena   = Arena::buscarPorId($torneo->getDatabase(), $idArena);
    if (!$arena) {
        echo "  Arena no encontrada.\n";
        pausar();
        return;
    }

    $duelo = $torneo->registrarDuelo($p1, $p2, $arena);
    echo "\n  Duelo registrado con ID: " . $duelo->getId() . " (estado: pendiente)\n";
    pausar();
}

// ─────────────────────────────────────────
//  6. EJECUTAR DUELOS PENDIENTES
// ─────────────────────────────────────────
function ejecutarDuelosPendientes($torneo) {
    limpiarPantalla();
    echo "  --- EJECUTAR DUELOS PENDIENTES ---\n\n";

    $duelos = Duelo::listar($torneo->getDatabase());
    $pendientes = array_filter($duelos, fn($d) => $d->getEstado() === 'pendiente');

    if (empty($pendientes)) {
        echo "  No hay duelos pendientes.\n";
        pausar();
        return;
    }

    foreach ($pendientes as $duelo) {
        $p1    = $duelo->getPersonaje1();
        $p2    = $duelo->getPersonaje2();
        $arena = $duelo->getArena();
        echo "  Duelo #{$duelo->getId()}: {$p1->getNombre()} vs {$p2->getNombre()} en {$arena->getNombre()}\n";
        $confirmar = leer("  Ejecutar este duelo? (s/n): ");

        if (strtolower($confirmar) === 's') {
            $seRealizo = $duelo->realizarDuelo($torneo->getDatabase());
            if ($seRealizo) {
                $ganador = $duelo->getGanador();
                $perdedor = ($ganador->getId() === $p1->getId()) ? $p2 : $p1;
                echo "\n  Resultado:\n";
                echo "  Poder {$p1->getNombre()}: " . $duelo->getPwPersonaje1() . "\n";
                echo "  Poder {$p2->getNombre()}: " . $duelo->getPwPersonaje2() . "\n";
                echo "  Ganador: {$ganador->getNombre()}\n";
                echo "  Danio aplicado: " . $duelo->getDanioAplicado() . "\n";
                echo "  {$perdedor->getNombre()} quedo en estado: {$perdedor->getEstado()}\n";
            } else {
                echo "  Duelo cancelado (alguno de los personajes no puede duelar).\n";
            }
            echo "\n";
        }
    }
    pausar();
}

// ─────────────────────────────────────────
//  7. RECUPERAR PERSONAJES LESIONADOS
// ─────────────────────────────────────────
function recuperarLesionados($torneo) {
    limpiarPantalla();
    echo "  --- RECUPERAR PERSONAJES LESIONADOS ---\n\n";

    $lesionados = $torneo->listarPersonajes('lesionado');
    if (empty($lesionados)) {
        echo "  No hay personajes lesionados.\n";
        pausar();
        return;
    }

    foreach ($lesionados as $p) {
        echo "  [{$p->getId()}] {$p->getNombre()} - Vida: {$p->getPuntosVida()}\n";
    }

    $id = (int) leer("\n  ID del personaje a recuperar: ");
    $personaje = Personaje::buscarPorId($torneo->getDatabase(), $id);

    if (!$personaje || $personaje->getEstado() !== 'lesionado') {
        echo "  Personaje no encontrado o no esta lesionado.\n";
        pausar();
        return;
    }

    $cantidad = (int) leer("  Cuantos puntos de vida recupera: ");
    $personaje->recuperarVida($cantidad);

    // Si supera los 30 puntos vuelve a disponible
    if ($personaje->getPuntosVida() > 30) {
        $personaje->setEstado('disponible');
        echo "  {$personaje->getNombre()} se recupero y volvio a estado: disponible\n";
    } else {
        echo "  {$personaje->getNombre()} recupero vida pero sigue lesionado ({$personaje->getPuntosVida()} PV)\n";
    }

    $personaje->guardar($torneo->getDatabase());
    pausar();
}

// ─────────────────────────────────────────
//  8. RANKINGS
// ─────────────────────────────────────────
function menuRanking($torneo) {
    limpiarPantalla();
    echo "  --- RANKINGS ---\n\n";
    echo "  1. Ranking por victorias\n";
    echo "  2. Personaje con mas victorias\n";
    echo "  3. Porcentaje de victorias por personaje\n";
    echo "  4. Arena con mas duelos\n";
    echo "  0. Volver\n";
    $op = leer("\n  Opcion: ");

    switch ($op) {
        case "1":
            $ranking = $torneo->rankingPersonajes();
            echo "\n  === RANKING POR VICTORIAS ===\n";
            $pos = 1;
            foreach ($ranking as $p) {
                echo "  {$pos}. {$p->getNombre()} - Victorias: {$p->getDuelosGanados()} - Derrotas: {$p->getDuelosPerdidos()}\n";
                $pos++;
            }
            break;

        case "2":
            $ranking = $torneo->rankingPersonajes();
            if (!empty($ranking)) {
                $primero = $ranking[0];
                echo "\n  Campeon: {$primero->getNombre()} con {$primero->getDuelosGanados()} victorias\n";
            }
            break;

        case "3":
            $personajes = Personaje::listar($torneo->getDatabase());
            echo "\n  === PORCENTAJE DE VICTORIAS ===\n";
            foreach ($personajes as $p) {
                $total = $p->getDuelosGanados() + $p->getDuelosPerdidos();
                $porcentaje = ($total > 0) ? round(($p->getDuelosGanados() / $total) * 100, 1) : 0;
                echo "  {$p->getNombre()}: {$porcentaje}% ({$p->getDuelosGanados()}/{$total})\n";
            }
            break;

        case "4":
            $db = $torneo->getDatabase();
            // Consulta con JOIN para contar duelos por arena
            $resultado = $db->query(
                "SELECT a.nombre, COUNT(d.id) as totalDuelos
                 FROM arenas a
                 LEFT JOIN duelos d ON d.idArena = a.id AND d.estado = 'realizado'
                 GROUP BY a.id, a.nombre
                 ORDER BY totalDuelos DESC
                 LIMIT 1"
            )->fetch(\PDO::FETCH_ASSOC); //fetchAll para obtener todos los resultados como un array asociativo, PDO::FETCH_ASSOC para que nos devuelva un array asociativo en lugar de un array indexado

            if ($resultado) {
                echo "\n  Arena con mas duelos: {$resultado['nombre']} ({$resultado['totalDuelos']} duelos)\n";
            } else {
                echo "\n  No hay datos suficientes.\n";
            }
            break;
    }

    pausar();
}

// ─────────────────────────────────────────
//  9. HISTORIAL DE PERSONAJE
// ─────────────────────────────────────────
function historialPersonaje($torneo) {
    limpiarPantalla();
    echo "  --- HISTORIAL DE PERSONAJE ---\n\n";

    $personajes = Personaje::listar($torneo->getDatabase());
    foreach ($personajes as $p) {
        echo "  [{$p->getId()}] {$p->getNombre()} ({$p->getTipoPersonaje()})\n";
    }

    $id = (int) leer("\n  ID del personaje: ");
    $personaje = Personaje::buscarPorId($torneo->getDatabase(), $id);

    if (!$personaje) {
        echo "  Personaje no encontrado.\n";
        pausar();
        return;
    }

    echo "\n  Historial de: {$personaje->getNombre()}\n";
    echo str_repeat("-", 40) . "\n";

    // Consulta JOIN para traer todos los duelos del personaje
    $database = $torneo->getDatabase();
    // Usamos una consulta SQL con JOIN para obtener los duelos del personaje, incluyendo nombres de los personajes y el ganador
    $duelos = $database->query(
        "SELECT d.id, d.fecha, d.estado, d.poderPersonaje1, d.poderPersonaje2, d.danioAplicado,
                p1.nombre AS nombre1, p2.nombre AS nombre2,
                g.nombre AS ganador, a.nombre AS arena
         FROM duelos d
         JOIN personajes p1 ON d.idPersonaje1 = p1.id
         JOIN personajes p2 ON d.idPersonaje2 = p2.id
         LEFT JOIN personajes g ON d.idGanador = g.id
         JOIN arenas a ON d.idArena = a.id
         WHERE d.idPersonaje1 = :id OR d.idPersonaje2 = :id
         ORDER BY d.fecha DESC",
        [":id" => $id]
    )->fetchAll(\PDO::FETCH_ASSOC); 

    if (empty($duelos)) {
        echo "  Este personaje no tiene duelos registrados.\n";
    } else {
        foreach ($duelos as $d) {
            echo "\n  Duelo #{$d['id']} - {$d['fecha']} - Arena: {$d['arena']}\n";
            echo "  {$d['nombre1']} vs {$d['nombre2']} | Estado: {$d['estado']}\n";
            if ($d['estado'] === 'realizado') {
                echo "  Ganador: {$d['ganador']} | Danio: {$d['danioAplicado']}\n";
            }
        }
    }

    pausar();
}

// ─────────────────────────────────────────
//  10. CONSULTAS VARIAS
// ─────────────────────────────────────────
function menuConsultas($torneo) {
    limpiarPantalla();
    echo "  --- CONSULTAS ---\n\n";
    echo "  1. Listar todos los personajes\n";
    echo "  2. Listar personajes disponibles\n";
    echo "  3. Listar personajes lesionados\n";
    echo "  4. Listar personajes retirados\n";
    echo "  5. Listar armas disponibles\n";
    echo "  6. Mostrar arma equipada por cada personaje\n";
    echo "  7. Mostrar duelos realizados\n";
    echo "  8. Mostrar duelos pendientes\n";
    echo "  0. Volver\n";
    $op = leer("\n  Opcion: ");

    switch ($op) {
        case "1":
            $lista = Personaje::listar($torneo->getDatabase());
            echo "\n  === TODOS LOS PERSONAJES ===\n";
            foreach ($lista as $p) {
                echo "  [{$p->getId()}] {$p->getNombre()} ({$p->getTipoPersonaje()}) - Nv.{$p->getNivel()} - Estado: {$p->getEstado()}\n";
            }
            break;

        case "2":
            $lista = $torneo->listarPersonajes('disponible');
            echo "\n  === PERSONAJES DISPONIBLES ===\n";
            foreach ($lista as $p) {
                echo "  [{$p->getId()}] {$p->getNombre()} - Nv.{$p->getNivel()}\n";
            }
            break;

        case "3":
            $lista = $torneo->listarPersonajes('lesionado');
            echo "\n  === PERSONAJES LESIONADOS ===\n";
            foreach ($lista as $p) {
                echo "  [{$p->getId()}] {$p->getNombre()} - Vida: {$p->getPuntosVida()}\n";
            }
            break;

        case "4":
            $lista = $torneo->listarPersonajes('retirado');
            echo "\n  === PERSONAJES RETIRADOS ===\n";
            foreach ($lista as $p) {
                echo "  [{$p->getId()}] {$p->getNombre()}\n";
            }
            break;

        case "5":
            $armas = Arma::listar($torneo->getDatabase());
            $disponibles = array_filter($armas, fn($a) => $a->getEstado() === 'disponible');
            echo "\n  === ARMAS DISPONIBLES ===\n";
            foreach ($disponibles as $a) {
                echo "  [{$a->getId()}] {$a->getNombre()} - Danio: {$a->getDanioBase()} - Nv.Min: {$a->getNivelMinimo()}\n";
            }
            break;

        case "6":
            // JOIN personajes con armas
            $database = $torneo->getDatabase();
            $filas = $database->query(
                "SELECT p.nombre AS personaje, a.nombre AS arma
                 FROM personajes p
                 LEFT JOIN armas a ON p.idArmaEquipada = a.id
                 ORDER BY p.nombre" //JOIN para obtener el nombre del arma equipada por cada personaje, LEFT JOIN para incluir personajes sin arma equipada, y ordenamos por nombre de personaje
            )->fetchAll(\PDO::FETCH_ASSOC);
            echo "\n  === ARMA EQUIPADA POR PERSONAJE ===\n";
            foreach ($filas as $f) {
                $arma = $f['arma'] ?? "Ninguna";
                echo "  {$f['personaje']}: {$arma}\n";
            }
            break;

        case "7":
            $duelos = Duelo::listar($torneo->getDatabase());
            $realizados = array_filter($duelos, fn($d) => $d->getEstado() === 'realizado');
            echo "\n  === DUELOS REALIZADOS ===\n";
            foreach ($realizados as $d) {
                $ganador = $d->getGanador() ? $d->getGanador()->getNombre() : "Empate";
                echo "  #{$d->getId()} | {$d->getPersonaje1()->getNombre()} vs {$d->getPersonaje2()->getNombre()}";
                echo " | Ganador: {$ganador} | Arena: {$d->getArena()->getNombre()} | {$d->getFecha()}\n";
            }
            break;

        case "8":
            $duelos = Duelo::listar($torneo->getDatabase());
            $pendientes = array_filter($duelos, fn($d) => $d->getEstado() === 'pendiente');
            echo "\n  === DUELOS PENDIENTES ===\n";
            foreach ($pendientes as $d) {
                echo "  #{$d->getId()} | {$d->getPersonaje1()->getNombre()} vs {$d->getPersonaje2()->getNombre()}";
                echo " | Arena: {$d->getArena()->getNombre()} | {$d->getFecha()}\n";
            }
            break;
    }

    pausar();
}

// ─────────────────────────────────────────
//  LOOP PRINCIPAL
// ─────────────────────────────────────────
do {
    $opcion = menuPrincipal();

    switch ($opcion) {
        case "1": registrarPersonaje($torneo);        break;
        case "2": registrarArma($torneo);             break;
        case "3": registrarArena($torneo);            break;
        case "4": equiparArma($torneo);               break;
        case "5": registrarDuelo($torneo);            break;
        case "6": ejecutarDuelosPendientes($torneo);  break;
        case "7": recuperarLesionados($torneo);       break;
        case "8": menuRanking($torneo);               break;
        case "9": historialPersonaje($torneo);        break;
        case "10": menuConsultas($torneo);            break;
        case "0": echo "\n  Torneo Finalizado\n\n"; break;
        default:  echo "\n  Opcion invalida.\n"; pausar();
    }

} while ($opcion !== "0");//usamos un bucle do-while para que el menu se muestre al menos una vez y se repita hasta que el usuario elija salir (la opcion 0)