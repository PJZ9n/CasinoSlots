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

use LogicException;
use PJZ9n\CasinoSlots\Game\Draw;
use PJZ9n\CasinoSlots\Game\Exception\Player\NotSeatedException;
use PJZ9n\CasinoSlots\Game\Slot\Exception\AlreadyDrawingException;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\scheduler\TaskScheduler;

/**
 * Class DrawSlot
 * 全てPhpStormのせい。こんなことをしたくなかった。
 *
 * @link Draw 本来使うべきトレイト
 * @package PJZ9n\CasinoSlots\Game\Slot
 */
abstract class DrawSlot extends Slot
{
    
    /** @var TaskScheduler */
    private $taskScheduler;
    
    /** @var TaskHandler|null */
    private $taskHandler;
    
    /** @var int */
    private $loop;
    
    /** @var bool */
    private $drawing;
    
    /**
     * DrawSlot constructor.
     *
     * @param int $id
     * @param int $cost
     * @param TaskScheduler $taskScheduler
     */
    protected function __construct(int $id, int $cost, TaskScheduler $taskScheduler)
    {
        parent::__construct($id, $cost);
        $this->taskScheduler = $taskScheduler;
        $this->taskHandler = null;
        $this->loop = 0;
        $this->drawing = false;
    }
    
    /**
     * 描画を開始する
     *
     * @param int $tick
     *
     * @throws AlreadyDrawingException
     * @throws NotSeatedException
     */
    protected function drawBegin(int $tick = 20): void
    {
        if ($this->getDrawing()) {
            throw new AlreadyDrawingException($this);
        }
        if ($this->getSeatedPlayer() === null || !$this->getSeatedPlayer()->isOnline()) {
            throw new NotSeatedException($this);
        }
        $this->resetLoop();
        $this->drawing = true;
        $draw = $this;
        $this->taskHandler = $this->taskScheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick) use ($draw): void {
            if ($draw->getSeatedPlayer() === null) {
                $draw->drawEnd();
                return;
            }
            $draw->addLoop();
            $draw->drawing($draw->getLoop());
        }), $tick);
    }
    
    /**
     * 描画中の処理
     *
     * @internal
     */
    abstract public function drawing(int $loop): void;
    
    /**
     * 描画を終了する
     */
    protected function drawEnd(): void
    {
        if (!$this->drawing) {
            throw new LogicException("描画中ではない状態で関数が呼ばれました。");
        }
        if (!$this->taskHandler instanceof TaskHandler) {
            throw new LogicException("TaskHandlerがセットされていない状態で関数が呼ばれました。");
        }
        $this->taskHandler->cancel();
        $this->drawing = false;
    }
    
    /**
     * ループカウントを追加する
     *
     * @internal
     */
    public function addLoop(): void
    {
        if (!$this->drawing) {
            throw new LogicException("描画中ではない状態で関数が呼ばれました。");
        }
        $this->loop++;
    }
    
    /**
     * ループカウントを取得する
     *
     * @return int
     * @internal
     */
    public function getLoop(): int
    {
        if (!$this->drawing) {
            throw new LogicException("描画中ではない状態で関数が呼ばれました。");
        }
        return $this->loop;
    }
    
    /**
     * ループカウントをリセットする
     */
    private function resetLoop(): void
    {
        if ($this->drawing) {
            throw new LogicException("描画中の状態で関数が呼ばれました。");
        }
        $this->loop = 0;
    }
    
    /**
     * 描画中か取得する
     *
     * @return bool
     */
    public function getDrawing(): bool
    {
        return $this->drawing;
    }
    
}