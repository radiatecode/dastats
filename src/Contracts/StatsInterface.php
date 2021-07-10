<?php


namespace DaCode\DaStats\Contracts;

interface StatsInterface
{
    public function isolate(string $name, int $id): StatsInterface;

    public function title(string $title): StatsInterface;

    public function key(string $key): StatsInterface;

    public function increase(int $value): bool;

    public function decrease(int $value): bool;

    public function replace(int $value, bool $createNew = false): bool;

    public function inKeys(...$keys): StatsInterface;

    public function join(string $table,string $pk = 'id',array $select = []): StatsInterface;

    public function find();

    public function paginate(int $perPage);

    public function get();

    public function remove(): bool;
}