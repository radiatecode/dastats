<?php


namespace RadiateCode\DaStats\Contracts;

interface StatsInterface
{
    public function isolate(string $name, int $id): StatsInterface;

    public function title(string $title): StatsInterface;

    public function key(string $key): StatsInterface;

    public function increase(float $value): bool;

    public function decrease(float $value): bool;

    public function replace(float $value): bool;

    public function inKeys(...$keys): StatsInterface;

    public function joinWith(string $table, string $pk = 'id');

    public function join($table, $first, $operator, $second): StatsInterface;

    public function contain(string $key): StatsInterface;

    public function find();

    public function paginate(int $perPage);

    public function get(array $columns = []);

    public function remove(): bool;
}