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

namespace PJZ9n\CasinoSlots\Game\Slot;

use PJZ9n\CasinoSlots\Game\Exception\Player\NotSeatedException;
use PJZ9n\CasinoSlots\Game\Game;
use PJZ9n\CasinoSlots\Game\Setting;

abstract class Slot extends Game implements Setting
{
    
    /** @var int */
    private $setting;
    
    /**
     * Slot constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct($id);
        $this->setting = 1;
    }
    
    /**
     * @inheritDoc
     */
    public function setSetting(int $setting): void
    {
        $this->setting = $setting;
    }
    
    /**
     * @inheritDoc
     */
    public function getSetting(): int
    {
        return $this->setting;
    }
    
}