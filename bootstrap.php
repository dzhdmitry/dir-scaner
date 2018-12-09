<?php

require __DIR__.'/vendor/autoload.php';

use Scanner\DatabaseConnection;

$connection = new DatabaseConnection(
    getenv('DATABASE_HOST'),
    getenv('DATABASE_PORT'),
    'postgres',
    getenv('DATABASE_USER'),
    getenv('DATABASE_PASSWORD')
);

$statement = $connection->prepare('SELECT 1 FROM pg_database WHERE datname = :db');

$statement->execute([
    'db' => getenv('DATABASE_NAME')
]);

if (count($statement->fetchAll())) {
    return;
}

$connection->prepare(sprintf('CREATE DATABASE %s', getenv('DATABASE_NAME')))
    ->execute();

$connection = new DatabaseConnection(
    getenv('DATABASE_HOST'),
    getenv('DATABASE_PORT'),
    getenv('DATABASE_NAME'),
    getenv('DATABASE_USER'),
    getenv('DATABASE_PASSWORD')
);

$connection->prepare('CREATE TABLE impressions
    (
        id          serial           not null
        constraint impressions_pk
          primary key,
        date        date             not null,
        geo         varchar(255)     not null,
        zone        integer          not null,
        impressions integer          not null,
        revenue     integer          not null
    )')
    ->execute();

$connection->prepare('CREATE UNIQUE INDEX impressions_date_geo_zone_uindex ON impressions (date, geo, zone)')
    ->execute();
