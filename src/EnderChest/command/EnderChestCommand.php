<?php

namespace EnderChest\command;

use pocketmine\command\Command;
use pocketmine\permission\PermissionManager;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use EnderChest;

class EnderChestCommand extends Command
{
    public const COMMAND_PERMISSION_ENDER_CHEST = "command.permission.ender.chest";

    public function __construct()
    {
        parent::__construct("enderchest", "Открыть эндер-сундук", null, ["ec"]);
        $root = PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_USER);
        DefaultPermissions::registerPermission(new Permission(self::COMMAND_PERMISSION_ENDER_CHEST), [$root]);
        $this->setPermission(self::COMMAND_PERMISSION_ENDER_CHEST);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void
    {
        if ($sender instanceof Player):
            EnderChest::open($sender);
        else:
            $sender->sendMessage("§cИспользуйте только в игре");
        endif;
    }
}
