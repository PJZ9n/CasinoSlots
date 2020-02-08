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

//警告: この名前空間以下のコードすべては気分を害する恐れがあります。
//閲覧する場合には十分気を付けてください。完全に自己責任でお願いします。
namespace PJZ9n\CasinoSlots;

use PJZ9n\CasinoSlots\API\CasinoSlotsAPI;
use PJZ9n\CasinoSlots\Command\GameCommand;
use PJZ9n\CasinoSlots\Game\Game;
use PJZ9n\CasinoSlots\Game\SaveData;
use PJZ9n\CasinoSlots\Game\Slot\StarSlot\StarSlot;
use PJZ9n\CasinoSlots\MoneyAPIConnector\Connector\EconomyAPIConnector;
use PJZ9n\CasinoSlots\MoneyAPIConnector\Connector\MixCoinSystemConnector;
use PJZ9n\CasinoSlots\MoneyAPIConnector\Connector\MoneySystemConnector;
use PJZ9n\CasinoSlots\MoneyAPIConnector\MoneyAPIConnector;
use pocketmine\event\Listener;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use RuntimeException;

class CasinoSlots extends PluginBase implements Listener
{
    
    /** @var DataConnector */
    private $db;
    
    /** @var MoneyAPIConnector */
    private $moneyAPIConnector;
    
    /**
     * @var Game[]
     * [string "ゲーム名" => Game[]]
     */
    private $games;
    
    /** @var bool */
    private $processFinished;
    
    public function onEnable(): void
    {
        $this->processFinished = false;
        //Init Config
        $this->saveDefaultConfig();
        $this->reloadConfig();
        //Check Config Version
        $resourceConfig = yaml_parse(stream_get_contents($this->getResource("config.yml")));
        $resourceConfigVersion = $resourceConfig["config-version"];
        $configVersion = $this->getConfig()->get("config-version");
        if ($resourceConfigVersion > $configVersion) {
            $this->getLogger()->notice("新しいconfig.yml(バージョン: {$resourceConfigVersion})があります。");
            $this->getLogger()->notice("更新するには、config.ymlのコピーを取ってから削除して再起動してください。");
        } else {
            $this->getLogger()->info("config.ymlは最新(バージョン: {$configVersion})です。");
        }
        //Init moneyAPIConnector
        $useApi = $this->getConfig()->getNested("moneyapi.use", null);
        switch ($useApi) {
            case "EconomyAPI":
                $this->moneyAPIConnector = new EconomyAPIConnector();
                break;
            case "MoneySystem":
                $this->moneyAPIConnector = new MoneySystemConnector();
                break;
            case "MixCoinSystem":
                $this->moneyAPIConnector = new MixCoinSystemConnector();
                break;
            default:
                throw new RuntimeException("対応していない経済APIが指定されています。");
        }
        $this->getLogger()->info("現在 {$useApi} の経済APIが指定されています。");
        //Init DB
        $dbSetting = [];
        $dbSetting["type"] = $this->getConfig()->getNested("database.type", null);
        $dbSetting["sqlite"]["file"] = $this->getConfig()->getNested("database.sqlite.file", null);
        $dbSetting["worker-limit"] = 1;
        $this->db = libasynql::create($this, $dbSetting, [
            "sqlite" => "sqls/sqlite.sql",
        ]);
        $this->db->executeGeneric("CasinoSlots.savedata.init", [], function () {
            $this->getLogger()->debug("データベースの初期化処理が完了しました。");
        });
        $this->db->waitAll();//初期化待ち
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
                        $makeGame = new StarSlot($makeNumber, $gameOption["cost"], $this->getScheduler());
                        break;
                    default:
                        $this->getLogger()->warning($gameName . " は存在しません。");
                        continue 2;
                }
                $this->games[$gameName][$makeNumber] = $makeGame;
                $this->getLogger()->debug("{$gameName} のID {$makeNumber} を作成しました。");
                if ($makeGame instanceof SaveData) {
                    $this->db->executeSelect("CasinoSlots.savedata.get", [
                        "name" => $makeGame->getName(),
                        "id" => $makeGame->getId(),
                    ], function (array $rows) use ($makeGame) {
                        if (isset($rows[0]["data"])) {
                            $data = json_decode($rows[0]["data"], true);
                            $makeGame->inputSaveData($data);
                            $this->getLogger()->debug("レコードからデータを取得しました。");
                        } else {
                            $this->db->executeInsert("CasinoSlots.savedata.add", [
                                "name" => $makeGame->getName(),
                                "id" => $makeGame->getId(),
                                "data" => json_encode($makeGame->outputSaveData()),
                            ], function (int $insertId, int $affectedRows) {
                                $this->getLogger()->debug("ID: {$insertId}, {$affectedRows} 個のレコードを作成しました。");
                            });
                        }
                    });
                    $this->db->waitAll();//最終待機
                }
            }
        }
        $this->getLogger()->info(count($this->games) . " 種類のゲームが利用可能です。");
        //Init API
        new CasinoSlotsAPI($this->games, $this->moneyAPIConnector);
        $this->processFinished = true;
    }
    
    public function onDisable(): void
    {
        //onEnableの処理途中でプラグインが無効化された場合
        if (!$this->processFinished) {
            return;
        }
        //Gameの終了処理
        foreach ($this->games as $gameArray) {
            foreach ($gameArray as $game) {
                /** @var Game $game */
                $this->getLogger()->debug("{$game->getName()} のID {$game->getId()} の修了処理をします。");
                if ($game instanceof SaveData) {
                    $this->db->executeChange("CasinoSlots.savedata.update", [
                        "data" => json_encode($game->outputSaveData()),
                        "name" => $game->getName(),
                        "id" => $game->getId(),
                    ], function (int $affectedRows) {
                        $this->getLogger()->debug("{$affectedRows} 個のアップデートが完了しました。");
                    });
                }
            }
        }
        //データベースの修了処理
        $this->db->waitAll();
        if ($this->db instanceof DataConnector) {
            $this->db->close();
        }
    }
    
}