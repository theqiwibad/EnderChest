<?php

namespace EnderChest\event;

use pocketmine\event\Listener;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\block\inventory\EnderChestInventory;
use EnderChest;

class EventHandler implements Listener
{
    public function InventoryCloseEvent(InventoryCloseEvent $event) : void
    {
        if ($event->getInventory() instanceof EnderChestInventory):
            EnderChest::close($event->getPlayer());
        endif;
    }
}
