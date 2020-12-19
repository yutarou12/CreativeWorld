<?php

namespace yutarou;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\{Command, CommandSender};
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

use pocketmine\level\Position;

class cw extends PluginBase implements Listener{

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        $this->getLogger()->info("§aテストプラグインを読み込みました");
    }

    public function onTp(EntityTeleportEvent $event){

        $posi = $event->getFrom()->level->getName();
        $sposi = (string) $posi;
        $to = $event->getTo();
        $this->getLogger()->info($posi);
        $this->getLogger()->info($to);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        switch ($label){
            case "cw":
                if (!$sender instanceof Player) {
                    $sender->sendMessage("§cプレイヤーのみ利用可能です。");
                    return true;
                }
                if(!Server::getInstance()->isLevelLoaded("test")){
                    if(!$this->getServer()->loadLevel("test")){
                        $sender->sendMessage("§ワールドが削除されています...");
                        return true;
                    }else $this->getLogger()->info("レベルをロードしました");
                }
                $level = $this->getServer()->getLevelByName("test");
                $targetPosition = new Position(116,72,54,$level);
                $sender->setGamemode(1);
                $sender->teleport($targetPosition);
                $sender->sendMessage("§e§l[§fCreativeWorld§e] §r§aクリエイティブワールドへテレポートしました");
                break;
        }
        return true;
    }
}