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

/**
 * Interface SaveData
 * セーブする必要があるデータがある場合に使用します。
 *
 * @package PJZ9n\CasinoSlots\Game
 */
interface SaveData
{
    
    /**
     * セーブデータを出力する
     *
     * @return array
     */
    public function outputSaveData(): array;
    
    /**
     * セーブデータを入力する
     *
     * @param array $saveData
     */
    public function inputSaveData(array $saveData): void;
    
}