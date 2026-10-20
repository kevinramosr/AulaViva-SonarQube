<?php
declare(strict_types=1);

// Cargar Composer.
require_once __DIR__ . '/../vendor/autoload.php';

// Preparar una sesión autenticada para las pruebas de roles.
// Primero cargamos la configuración real de sesiones.
require_once __DIR__ . '/../config/sesion.php';