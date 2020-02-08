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

namespace PJZ9n\CasinoSlots\MoneyAPIConnector;

use pocketmine\Player;

interface MoneyAPIConnector
{
    
    /**
     * 所持金を取得する
     *
     * @param Player $player
     *
     * @return int
     */
    public function getMoney(Player $player): int;
    
    /**
     * 所持金を設定する
     *
     * @param Player $player
     * @param int $money
     */
    public function setMoney(Player $player, int $money): void;
    
    /**
     * 所持金を増額する
     *
     * @param Player $player
     * @param int $money
     */
    public function addMoney(Player $player, int $money): void;
    
    /**
     * 所持金を減額する
     *
     * @param Player $player
     * @param int $money
     */
    public function takeMoney(Player $player, int $money): void;
}