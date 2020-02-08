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

namespace PJZ9n\CasinoSlots\API;

use PJZ9n\CasinoSlots\Game\Game;
use PJZ9n\CasinoSlots\MoneyAPIConnector\MoneyAPIConnector;
use RuntimeException;

/**
 * Class CasinoSlotsAPI
 * APIクラス
 *
 * @package PJZ9n\CasinoSlots\API
 */
class CasinoSlotsAPI
{
    
    /** @var self */
    private static $instance;
    
    public static function getInstance(): self
    {
        return self::$instance;
    }
    
    /**
     * @var Game[]
     * [string "ゲーム名" => Game[]]
     */
    private $games;
    
    /** @var MoneyAPIConnector */
    private $moneyAPIConnector;
    
    /**
     * CasinoSlotsAPI constructor.
     *
     * @param Game[] $games [string "ゲーム名" => Game[]]
     *
     * @internal APIのインスタンスはプラグインが生成します。
     */
    public function __construct(array $games, MoneyAPIConnector $moneyAPIConnector)
    {
        if (self::$instance instanceof self) {
            throw new RuntimeException("このクラスのインスタンスを複数生成することはできません。");
        }
        self::$instance = $this;
        $this->games = $games;
        $this->moneyAPIConnector = $moneyAPIConnector;
    }
    
    /**
     * Gameオブジェクトを取得する
     *
     * @param string $name
     * @param int $id
     *
     * @return Game|null
     */
    public function getGame(string $name, int $id): ?Game
    {
        if (!isset($this->games[$name])) {
            return null;
        }
        $games = $this->games[$name];
        if (!isset($games[$id])) {
            return null;
        }
        return $games[$id];
    }
    
    /**
     * 使用できる全てのゲーム名を取得する
     *
     * @return string[]
     */
    public function getGameNames(): array
    {
        $allGameNames = [];
        foreach ($this->games as $gameName => $gameArray) {
            $allGameNames[] = $gameName;
        }
        return $allGameNames;
    }
    
    /**
     * ゲームの全IDを取得する
     *
     * @param string $name
     *
     * @return int[]
     */
    public function getGameIds(string $name): array
    {
        $gameIds = [];
        foreach ($this->games as $gameName => $gameArray) {
            if ($gameName = $name) {
                foreach ($gameArray as $game) {
                    /** @var Game $game */
                    $gameIds[] = $game->getId();
                }
                break;
            }
        }
        return $gameIds;
    }
    
    /**
     * MoneyAPIConnectorを取得する
     *
     * @return MoneyAPIConnector
     */
    public function getMoneyAPIConnector(): MoneyAPIConnector
    {
        return $this->moneyAPIConnector;
    }
    
}