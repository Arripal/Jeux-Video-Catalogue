<?php

namespace App\enums;

enum GameStatus: string
{
    case Wishlist = 'wishlist';
    case Playing = 'playing';
    case Completed = 'completed';
    case Dropped = 'dropped';
}
