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

namespace PJZ9n\CasinoSlots;

use PJZ9n\CasinoSlots\Command\GameCommand;
use PJZ9n\CasinoSlots\Game\Game;
use PJZ9n\CasinoSlots\Game\Slot\StarSlot\StarSlot;
use pocketmine\event\Listener;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class CasinoSlots extends PluginBase implements Listener
{
    
    /** @var DataConnector */
    private $db;
    
    /** @var Game[] */
    private $games;
    
    public function onEnable(): void
    {
        //Init Config
        $this->saveDefaultConfig();
        $this->reloadConfig();
        //Init DB
        $dbSetting = [];
        $dbSetting["type"] = $this->getConfig()->getNested("database.type", null);
        $dbSetting["sqlite"]["file"] = $this->getConfig()->getNested("database.sqlite.file", null);
        $dbSetting["worker-limit"] = 1;
        $this->db = libasynql::create($this, $dbSetting, [
            "sqlite" => "sqls/sqlite.sql",
        ]);
        //Init Permission
        PermissionManager::getInstance()->addPermission(new Permission(
            "casinoslots.command.cgame",
            null,
            Permission::DEFAULT_TRUE
        ));
        //Init Command
        $this->getServer()->getCommandMap()->register("CasinoSlots", new GameCommand($this));
        //Init Games
        $this->games = [];
        $games = $this->getConfig()->get("games");
        foreach ($games as $gameName => $gameOption) {
            for ($makeNumber = 1; $makeNumber <= $gameOption["make-number"]; $makeNumber++) {
                //あまり良くない実装
                /** @var Game $makeGame */
                $makeGame = null;
                switch ($gameName) {
                    case "starslot":
                        $makeGame = new StarSlot($makeNumber, $this->getScheduler());
                        break;
                    default:
                        $this->getLogger()->warning($gameName . " は存在しません。");
                        continue 2;
                }
                $this->games[$gameName][$makeNumber] = $makeGame;
                $this->getLogger()->debug("{$gameName} のID {$makeNumber} を作成しました。");
            }
        }
        $this->getLogger()->info(count($this->games) . " 種類のゲームが利用可能です。");
    }
    
}