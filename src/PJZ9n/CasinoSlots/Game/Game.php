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

namespace PJZ9n\CasinoSlots\Game;

use PJZ9n\CasinoSlots\Game\Exception\Player\AlreadySeatedException;
use PJZ9n\CasinoSlots\Game\Exception\Player\NotSeatedException;
use PJZ9n\CasinoSlots\Game\Slot\Exception\AlreadyDrawingException;
use PJZ9n\CasinoSlots\MoneyAPIConnector\MoneyAPIConnector;
use pocketmine\Player;

abstract class Game
{
    
    /** @var int */
    private $id;
    
    /** @var int */
    private $cost;
    
    /** @var Player|null */
    private $seatedPlayer;
    
    /**
     * Game constructor.
     *
     * @param int $id
     * @param int $cost
     */
    public function __construct(int $id, int $cost)
    {
        $this->id = $id;
        $this->cost = $cost;
        $this->seatedPlayer = null;
    }
    
    /**
     * ゲームの名前を取得する
     *
     * @return string
     */
    abstract public function getName(): string;
    
    /**
     * IDを取得する
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * 着席しているプレイヤーを取得する
     *
     * @return Player|null
     */
    public function getSeatedPlayer(): ?Player
    {
        return $this->seatedPlayer;
    }
    
    /**
     * プレイヤーを着席させる
     *
     * @param Player $player
     *
     * @throws AlreadySeatedException
     */
    public function seatPlayer(Player $player): void
    {
        if ($this->seatedPlayer !== null) {
            throw new AlreadySeatedException($this);
        }
        $this->seatedPlayer = $player;
    }
    
    /**
     * プレイヤーを離席させる
     *
     * @throws NotSeatedException
     */
    public function unseatPlayer(): void
    {
        if (!$this->seatedPlayer instanceof Player) {
            throw new NotSeatedException($this);
        }
        $this->seatedPlayer = null;
    }
    
    /**
     * ゲームをスタートさせる
     * 内部で使うときは原則この親を呼ぶこと。
     *
     * @throws NotSeatedException
     * @throws AlreadyDrawingException
     */
    public function gameStart(): void
    {
        if ($this->getSeatedPlayer() === null) {
            throw new NotSeatedException($this);
        }
    }
    
    /**
     * スタートに必要な金額を取得する
     *
     * @return int
     */
    public function getNeedMoney(): int
    {
        return $this->cost;
    }
    
}