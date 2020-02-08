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

use PJZ9n\CasinoSlots\API\CasinoSlotsAPI;
use PJZ9n\CasinoSlots\Game\Exception\Player\AlreadySeatedException;
use PJZ9n\CasinoSlots\Game\Exception\Player\NotSeatedException;
use PJZ9n\CasinoSlots\Game\Game;
use PJZ9n\CasinoSlots\Game\Slot\DrawSlot;
use PJZ9n\CasinoSlots\Game\Slot\Exception\AlreadyDrawingException;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class GameCommand extends PluginCommand implements CommandExecutor
{
    
    public function __construct(Plugin $owner)
    {
        parent::__construct("cgame", $owner);
        $this->setExecutor($this);
        $this->setAliases([
            "game",
        ]);
        $this->setDescription("ゲームを操作する");
        $this->setUsage(/** @lang TEXT */
            "/cgame start <name> <id>\n" .
            "/cgame data <name> <id>\n" .
            "/cgame admin <name> <id>");
        $this->setPermission("casinoslots.command.cgame");
    }
    
    /**
     * @param CommandSender $sender
     * @param Command $command
     * @param string $label
     * @param array $args
     *
     * @return bool
     * @throws AlreadySeatedException
     * @throws NotSeatedException
     * @throws AlreadyDrawingException
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if (!$this->testPermission($sender)) {
            return true;
        }
        if (!isset($args[0])) {
            return false;
        }
        switch ($args[0]) {
            case "start":
                if (!$sender instanceof Player) {
                    $sender->sendMessage("プレイヤーから実行してください。");
                    return true;
                }
                if (!isset($args[1], $args[2])) {
                    return false;
                }
                $gameName = filter_var($args[1]);
                if ($gameName === false) return false;
                $gameId = filter_var($args[2], FILTER_VALIDATE_INT);
                if ($gameId === false) return false;
                $game = CasinoSlotsAPI::getInstance()->getGame($gameName, $gameId);
                if (!$game instanceof Game) {
                    $sender->sendMessage("そのゲームは存在しません。");
                    return true;
                }
                if ($game instanceof DrawSlot) {
                    if ($game->getDrawing()) {
                        $sender->sendMessage("すでに実行中です。");
                        return true;
                    }
                }
                $seatedPlayer = $game->getSeatedPlayer();
                if (!$seatedPlayer instanceof Player) {
                    //空席の場合
                    $game->seatPlayer($sender);
                    $sender->sendMessage("着席しました！");
                    $seatedPlayer = $game->getSeatedPlayer();
                } else if ($seatedPlayer !== $sender) {
                    $sender->sendMessage("すでに {$seatedPlayer->getName()} さんが着席しています。");
                    return true;
                }
                $moneyAPIConnector = CasinoSlotsAPI::getInstance()->getMoneyAPIConnector();
                if ($moneyAPIConnector->getMoney($seatedPlayer) < $game->getNeedMoney()) {
                    $sender->sendMessage("所持金が足りません。");
                    return true;
                }
                $moneyAPIConnector->takeMoney($seatedPlayer, $game->getNeedMoney());
                $game->gameStart();
                return true;
            case "data":
                if (!isset($args[1], $args[2])) {
                    return false;
                }
                $gameName = filter_var($args[1]);
                if ($gameName === false) return false;
                $gameId = filter_var($args[2], FILTER_VALIDATE_INT);
                if ($gameId === false) return false;
                break;
            case "admin":
                if (!isset($args[1], $args[2])) {
                    return false;
                }
                $gameName = filter_var($args[1]);
                if ($gameName === false) return false;
                $gameId = filter_var($args[2], FILTER_VALIDATE_INT);
                if ($gameId === false) return false;
                break;
        }
        return false;
    }
    
}