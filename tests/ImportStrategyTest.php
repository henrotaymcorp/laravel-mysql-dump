<?php

use Henrotaym\LaravelMysqlDump\Factories\Strategies\ImportStrategyFactory;
use Illuminate\Support\Facades\DB;

it('can import a database', function () {

    $path = '/opt/apps/app/tests/export.sql';
    $tenantDatabase = 'tenant_4ab79e07-40ca-4c72-833e-a3f9354b4c3c';

    /**
     * @var ImportStrategyFactory
     */
    $factory = app()->make(ImportStrategyFactory::class);

    $strategy = $factory->database(
        env('DB_HOST'),
        env('DB_PORT'),
        env('DB_USERNAME'),
        env('DB_PASSWORD'),
        $path
    );
    $strategy->import();

    config(['database.connections.mysql.database' => $tenantDatabase]);
    DB::purge('mysql');

    $hasInvoices = DB::connection('mysql')->table('invoices')->exists();

    expect($hasInvoices)->toBe(true);
});

it('can import a database to a server using a self-signed certificate', function () {

    $path = '/opt/apps/app/tests/export.sql';
    $tenantDatabase = 'tenant_4ab79e07-40ca-4c72-833e-a3f9354b4c3c';
    $originalHome = getenv('HOME');
    $homeWithoutClientOptions = sys_get_temp_dir().'/'.uniqid('mysql-client-home-');
    mkdir($homeWithoutClientOptions);

    /**
     * @var ImportStrategyFactory
     */
    $factory = app()->make(ImportStrategyFactory::class);

    $strategy = $factory->database(
        env('DB_HOST'),
        env('DB_PORT'),
        env('DB_USERNAME'),
        env('DB_PASSWORD'),
        $path
    );

    putenv("HOME=$homeWithoutClientOptions");

    try {
        $strategy->import();
    } finally {
        putenv("HOME=$originalHome");
        rmdir($homeWithoutClientOptions);
    }

    config(['database.connections.mysql.database' => $tenantDatabase]);
    DB::purge('mysql');

    $hasInvoices = DB::connection('mysql')->table('invoices')->exists();

    expect($hasInvoices)->toBe(true);
});
