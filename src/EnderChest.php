<?php

use pocketmine\plugin\PluginBase;
use pocketmine\world\Position;
use EnderChest\event\EventHandler;
use EnderChest\command\EnderChestCommand;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\inventory\EnderChestInventory;

class EnderChest extends PluginBase
{
    private static ?Position $position = null;

    public function onEnable() : void
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventHandler(), $this);
        $this->getServer()->getCommandMap()->register("", new EnderChestCommand());
    }

    /**
     * @return Position|null
     */
    public static function getPosition() : ?Position
    {
        return self::$position ?? null;
    }

    /**
     * @param Position $position
     * @return void
     */
    public static function setPosition(Position $position) : void
    {
        self::$position = $position;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public static function create(Player $player) : bool
    {
        $position = $player->getPosition()->floor();
        $world = $player->getWorld();
        self::setPosition(Position::fromObject($position->add(0, $position->y < $world::Y_MIN + 3 ? 3 : -3, 0), $world));
        $y = self::getPosition()->y;
        if ($y > $world::Y_MIN and $y < $world::Y_MAX)
        {
            $networkSession = $player->getNetworkSession();
            $pk = new UpdateBlockPacket();
            $pk->blockPosition = BlockPosition::fromVector3(self::getPosition());
            $pk->blockRuntimeId = $networkSession->getTypeConverter()->getBlockTranslator()->internalIdToNetworkId(VanillaBlocks::ENDER_CHEST()->getStateId());
            return $networkSession->sendDataPacket($pk);
        }
        return false;
    }

    /**
     * @param Player $player
     * @return void
     */
    public static function open(Player $player) : void
    {
        if (self::create($player)):
            $player->setCurrentWindow(new EnderChestInventory(self::getPosition(), $player->getEnderInventory()));
        else:
            $player->sendMessage("§cОшибка при открытии эндер-сундука");
        endif;
    }

    /**
     * @param Player $player
     * @return void
     */
    public static function close(Player $player) : void
    {
        $networkSession = $player->getNetworkSession();
        $pk = new UpdateBlockPacket();
        $pk->blockPosition = BlockPosition::fromVector3(self::getPosition());
        $pk->blockRuntimeId = $networkSession->getTypeConverter()->getBlockTranslator()->internalIdToNetworkId($player->getWorld()->getBlock(self::getPosition())->getStateId());
        $networkSession->sendDataPacket($pk);
    }
}
