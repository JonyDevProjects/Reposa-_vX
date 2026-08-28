<?php

/*
|--------------------------------------------------------------------------
| Pest Configuration
|--------------------------------------------------------------------------
|
| Archivo de configuracion principal de Pest. Se carga automaticamente
| antes de todos los tests.
|
*/

uses(Tests\TestCase::class)->in('Feature', 'Unit');
