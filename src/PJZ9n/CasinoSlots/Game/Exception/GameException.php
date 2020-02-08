<?php

/**
 * Copyright (c) 2020 PJZ9n.
 * This file is part of CasinoSlots.
 * CasinoSlots is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * CasinoSlots is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with CasinoSlots.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace PJZ9n\CasinoSlots\Game\Exception;

use Exception;
use PJZ9n\CasinoSlots\Game\Game;

abstract class GameException extends Exception
{
    
    /** @var Game */
    private $game;
    
    /**
     * GameException constructor.
     *
     * @param Game $game
     * @param string $name
     */
    public function __construct(Game $game, ?string $name = null)
    {
        parent::__construct($name);
        $this->game = $game;
    }
    
    /**
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }
    
}