<?php

namespace yutarou;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\command\{Command, CommandSender};

use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\player\PlayerQuitEvent;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;
use pocketmine\level\Position;

class cw extends PluginBase implements Listener{

    public $p_inv = [];
    const TAG = "§e§l[§fCreativeWorld§e] §r";

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        $this->getLogger()->info(self::TAG."§aを読み込みました");
        $this->getLogger()->info("制作者 yutarou1241477");

        $this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML, array(
            "クリエイティブワールド名" => "creative",
            "x座標" => 116,
            "y座標" => 72,
            "z座標" => 54
        ));
    }

     public function onTp(EntityTeleportEvent $event){
            $entity = $event->getEntity();
            if($entity instanceof Player){
                $posi = $event->getFrom()->level->getName();
                $posi_2 = $event->getTo()->level->getName();
                $player = $entity->getPlayer();
                $player_name = $player->getName();
                $level_name = $this->config->get("クリエイティブワールド名");
                if($posi === $posi_2) {
                    $player->setGamemode(1);
                }else if($posi !== $posi_2 && $posi === $level_name){
                    $player->setGamemode(0);
                    $player->getInventory()->clearAll();
                    if($this->p_inv[$player_name]){
                        $player->getInventory()->setContents($this->p_inv[$player_name]);
                        unset($this->p_inv[$player_name]);
                    }

                }
            }

    }

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        $p_spawn = $player->getSpawn();
        $p_level = $player->getLevel();
        $p_level_name = $p_level->getName();
        $level_name = $this->config->get("クリエイティブワールド名");

        if($p_level_name === $level_name){
            $player->setGamemode(0);
            $player->teleport($p_spawn);
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        switch ($label){
            case "cw":
                if (!$sender instanceof Player) {
                    $sender->sendMessage(self::TAG."§cプレイヤーのみ利用可能です");
                    return true;
                }
                //config読み込み
                $level_name = $this->config->get("クリエイティブワールド名");
                $level_x = $this->config->get("x座標");
                $level_y = $this->config->get("y座標");
                $level_z = $this->config->get("z座標");

                if(!Server::getInstance()->isLevelLoaded($level_name)){
                    if(!$this->getServer()->loadLevel($level_name)){
                        if($sender->isOp()){
                            $sender->sendMessage(self::TAG."§cワールドが削除されているか、フォルダが見つかりません..");
                            return true;
                        }else{
                            $sender->sendMessage(self::TAG."§cエラーが発生しました.鯖主に確認をお願いします");
                            return true;
                        }
                        return true;
                    }else{
                        $this->getLogger()->info(self::TAG."§fレベルをロードしました");
                    }
                }
                if($sender->getLevel()->getName() === $level_name){
                    $sender->sendMessage(self::TAG."§a既にクリエイティブワールドに居ます");
                    return true;
                }else{
                    $inventry = $sender->getInventory()->getContents();
                    $pname = $sender->getName();
                    $this->p_inv[$pname] = $inventry;
                    $level = $this->getServer()->getLevelByName($level_name);
                    $targetPosition = new Position($level_x,$level_y,$level_z,$level);
                    $sender->setGamemode(1);
                    $sender->teleport($targetPosition);
                    $sender->getInventory()->clearAll();
                    $sender->sendMessage(self::TAG."§aクリエイティブワールドへテレポートしました");
                    $sender->sendMessage(self::TAG."§c元の所持品はこのワールド以外へ移動すると戻ります");
                }
                break;
            case "cw_reload":
                if($sender->isOp()){
                    $this->config->reload();
                    $sender->sendMessage(self::TAG."§cConfigを再読み込みしました");
                    return true;
                }
                break;
        }
        return true;
    }
}