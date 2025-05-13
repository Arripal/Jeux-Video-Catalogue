<?php


namespace App\Utils;

use App\enums\GameStatus;
use DateTimeImmutable;
use Exception;
use ValueError;

class FieldsHandler
{

    public static function date(string $date_time): DateTimeImmutable | null
    {
        try {
            $releaseDate = new \DateTimeImmutable($date_time);
            return $releaseDate;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function gameStatus(string $status): GameStatus | null
    {
        try {
            $game_status = GameStatus::from($status);
            return $game_status;
        } catch (ValueError $e) {
            return null;
        }
    }
}
