<?php


namespace DaCode\DaStats\Contracts;

interface StatsInterface
{
    public function title(string $title): StatsInterface;

    public function key(string $key): StatsInterface;

    public function increment(): ?bool;
    
    public function decrement(): ?bool;

    public function addition(int $value);

    public function subtraction(int $value);

    public function statsByType(string $type): StatsInterface;

    public function statsByTitles(array $titles): StatsInterface;

    public function statsByKeys(array $keys): StatsInterface;

    public function contains(string $key): StatsInterface;

    public function isolate(string $name, int $id);

    public function find();

    public function paginate(int $perPage);

    public function get();

    public function removeStats(int $id = null): bool;
}