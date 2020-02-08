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

namespace PJZ9n\CasinoSlots\MoneyAPIConnector\Connector;

use metowa1227\moneysystem\api\core\API;
use PJZ9n\CasinoSlots\MoneyAPIConnector\MoneyAPIConnector;
use pocketmine\Player;

class MoneySystemConnector implements MoneyAPIConnector
{
    
    public function getMoney(Player $player): int
    {
        return (int)API::getInstance()->get($player);
    }
    
    public function setMoney(Player $player, int $money): void
    {
        API::getInstance()->set($player, $money);
    }
    
    public function addMoney(Player $player, int $money): void
    {
        API::getInstance()->increase($player, $money);
    }
    
    public function takeMoney(Player $player, int $money): void
    {
        API::getInstance()->reduce($player, $money);
    }
    
}