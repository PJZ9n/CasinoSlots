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

namespace PJZ9n\CasinoSlots\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;

class SlotCommand extends PluginCommand implements CommandExecutor
{
    
    public function __construct(Plugin $owner)
    {
        parent::__construct("casinoslot", $owner);
        $this->setExecutor($this);
        $this->setAliases([
            "slot",
            "cslot",
            "cs",
        ]);
        $this->setDescription("スロットをスタートする");
        $this->setUsage(/** @lang TEXT */
            "/casinoslot start <type> <id>\n" .
            "/casinoslot data <type> <id>\n" .
            "/casinoslot admin <type>");
        $this->setPermission("casinoslots.command.slot");
    }
    
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        return false;
    }
    
}