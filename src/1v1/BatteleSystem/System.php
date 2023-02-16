<?php
namespace 1v1\BattleSystem;

use pocketmine\player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteraceEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;

use 1v1\Callback;

class System implements Listener{
 
  const TEXT_DECO = "§eSYSTEM>>§f";

  public function __construct($plugin){
    $this->plugin = $plugin;
    $this->isGame = false;
    $this->player1 = 0;
    $this->player2 = 0;
    //イベント登録
    $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
  }
 
  public function onEntry(DataPacketReceiveEvent $event){
    $packet = $event->getPacket();
    //npcタッチ時の処理
    if($packet instanceof InventoryTransactionPacket and
      $packet->transactionType === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY){
      //タッチしたNpcの処理IDがENTRYなら
      if(Npc::ID[$packet->trData->entityRuntimeId] === Npc::TYPE_ENTRY){
        //エントリーリストにプレイヤー追加
        $this->entry[] = $event->getPlayer();
        $player->sendMessage("§eSYSTEM>>§fエントリーしました!");
      }
    }
  }
  
  //二人以下のときに回すよう関数
  public function tickTask(){
    //エントリーした人数が二人以下なら
    if(count($this->entry) < 2){
      $this->plugin->getScheduler()->scheduleDelayedTask(new Callback([$this, 'tickTask'], []), 20);
    }else{
      $this->gameStart();
    }
  }
  
  public function gameStart(){
    $this->player1 = array_splice($this->enrty, 0, 1);
    $this->player2 = array_splice($this->entry, 0, 1);
    $this->toPoint($player1, $player2);
    $this->gameTask(0);
    $this->isGame = true;
  }
  
  public function gameTask($time){
    if($this->isGame) return true;
    if($time >= 120){
      $this->gameEnd(0);
    }else{
      $this->plugin->getScheduler()->scheduleDelayedTask(new Callback([$this, 'gameTask'], [$time++]), 20);
    }
  }
  
  public function gameEnd($winner){
    $winner->sendMessage(self::TEXT_DECO."You Win");
    $this->isGame = false;
    $this->tickTask();
  }
  
  public function toPoint($player1, $player2){
    $player1->teleport(new Vector3(100, 100, 100));
    $player2->teleport(new Vector3(100, 100, 100));
  }
  
  public function onDeath(EntityDeathEvent $event){
    $entity = $event->getEntity();
    if($entity instanceof Player){
      if($this->isGame){
        if($entity === $this->player1){
          $this->gameEnd($this->player2);
        }elseif($entity === $this->player2){
          $this->gameEnd($this->player1);
        }
      }
    }
  }
  
}
    
  
  
    
    
      
        
   
   
   

   
  
