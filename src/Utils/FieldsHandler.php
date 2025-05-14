<?php

namespace App\Utils;

use App\enums\GameStatus;
use ValueError;

class FieldsHandler
{
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
