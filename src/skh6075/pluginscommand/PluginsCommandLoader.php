<?php

declare(strict_types=1);

namespace skh6075\pluginscommand;

use pocketmine\plugin\PluginBase;

final class PluginsCommandLoader extends PluginBase{

	protected function onEnable() : void{
		$commandMap = $this->getServer()->getCommandMap();
		$pmPluginsCommand = $commandMap->getCommand("plugins");
		if($pmPluginsCommand !== null){
			$commandMap->unregister($pmPluginsCommand);
		}

		$commandMap->register(strtolower($this->getName()), new PluginsCommand("plugins"));
	}
}