<?php

namespace MC;

use pocketmine\block\Block;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\inventory\BaseInventory;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;

class Main extends PluginBase{
  
    public function onEnable(){
        
    }
    
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if($command->getName() === "mc"){
            if($sender instanceof Player){
                $this->openChest($sender);
                return true;
            } else {
                $sender->sendMessage("Players can use this command! ");
                return true;
            }
        }
        return false;
    }
    
    public function openChest(Player $player): void{
        $block = Block::get(Block::CHEST);
        $block->x = $player->getFloorX();
        $block->y = $player->getFloorY() + 1;
        $block->z = $player->getFloorZ();
        
        $chest = Tile::createTile(Tile::CHEST, $player->getLevel(), Chest::createNBT($block));
        $inventory = $chest->getInventory();
        
        $this->fillChestWithRandomItems($inventory);
        
        $player->addWindow($inventory);
        
        $this->getScheduler()->scheduleDelayedTask(new ClosureTask(function(int $currentTick) use ($player, $inventory){
            $this->addItemToPlayer($player, $inventory);
            $player->removeWindow($inventory);
        }), 100);
    }
    
    public function fillChestWithRandomItems(BaseInventory $inventory): void{
        for($i = 0; $i < $inventory->getSize(); $i++){
            $item = $this->getRandomItem();
            $inventory->setItem($i, $item);
        }
    }
    
    public function addItemToPlayer(Player $player, BaseInventory $inventory): void{
        $randomSlot = mt_rand(0, $inventory->getSize() - 1);
        $item = $inventory->getItem($randomSlot);
        
        if(!$item->isNull()){
            $player->getInventory()->addItem($item);
        }
    }
    
    public function getRandomItem(): Item{
        $item = Item::get(Item::DIAMOND_SWORD);
        $item->setCustomName("§2§bMagicSword");
        
        return $item;
    }
}
