#!/usr/bin/env php
<?php

declare(strict_types=1);

// Desactiva límites de tiempo por si acaso
set_time_limit(0);


echo "Vending Machine\n";

$continuar = true;
do {
    echo "En que puedo ayudarte?\n";
    echo "1. Comprar producto\n";
    echo "2. Ver productos\n";
    echo "3. Gestionar machine\n";
    echo "0. Salir\n";

    echo "Selecciona una opción: ";
    $opcion = intval(fgets(STDIN));

    switch ($opcion) {
        case 1:
            echo "Comprar producto\n";
            break;
        case 2:
            echo "Ver productos\n";
            break;
        case 3:
            echo "Gestionar machine\n";
            break;
        case 0:
            $continuar = false;
            echo "Saliendo...\n";
            break;
        default:
            echo "Opción no válida\n";
            break;
    }
} while ($continuar);
