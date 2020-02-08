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

use MixCoinSystem\MixCoinSystem;
use PJZ9n\CasinoSlots\MoneyAPIConnector\MoneyAPIConnector;
use pocketmine\Player;

class MixCoinSystemConnector implements MoneyAPIConnector
{
    
    public function getMoney(Player $player): int
    {
        /** @var MixCoinSystem $instance */
        $instance = MixCoinSystem::getInstance();
        return (int)$instance->GetCoin($player);
    }
    
    public function setMoney(Player $player, int $money): void
    {
        /** @var MixCoinSystem $instance */
        $instance = MixCoinSystem::getInstance();
        $instance->SetCoin($player, $money);
    }
    
    public function addMoney(Player $player, int $money): void
    {
        /** @var MixCoinSystem $instance */
        $instance = MixCoinSystem::getInstance();
        $instance->PlusCoin($player, $money);
    }
    
    public function takeMoney(Player $player, int $money): void
    {
        /** @var MixCoinSystem $instance */
        $instance = MixCoinSystem::getInstance();
        $instance->MinusCoin($player, $money);
    }
    
}