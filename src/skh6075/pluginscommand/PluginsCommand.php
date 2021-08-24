<?php

declare(strict_types=1);

namespace skh6075\pluginscommand;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\KnownTranslationKeys;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\Server;

final class PluginsCommand extends Command{

	public function __construct(string $name){
		parent::__construct($name, KnownTranslationKeys::POCKETMINE_COMMAND_PLUGINS_DESCRIPTION);
		$this->setAliases(["pl"]);
		$this->setUsage(KnownTranslationKeys::POCKETMINE_COMMAND_PLUGINS_USAGE);
		$this->setPermission(DefaultPermissionNames::COMMAND_PLUGINS);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
		$plugins = []; //name => [plugins..]
		$virions = []; //name => [virions..]

		foreach(Server::getInstance()->getPluginManager()->getPlugins() as $plugin){
			if(!$plugin->isEnabled()){
				continue;
			}
			if(!isset($plugins[$plugin->getName()])){
				$plugins[$plugin->getName()] = [];
			}
			$plugins[$plugin->getName()] = [
				"authors" => $plugin->getDescription()->getAuthors(),
				"version" => $plugin->getDescription()->getVersion()
			];
		}
		if(is_dir("virions/")){
			$virionDiff = array_diff(scandir("virions/"), ['.', '..']);
			foreach($virionDiff as $value){
				if(!$this->canReadVirionYAML("virions/" . $value . "/virion.yml")){
					continue;
				}
				$ymlArr = yaml_parse(file_get_contents("virions/" . $value . "/virion.yml"));
				$virions[$ymlArr["name"]] = $ymlArr;
			}
		}

		$sender->sendMessage("Loads all plugins and libraries on the server.");
		$sender->sendMessage("Plugins(" . count($plugins) . "count): ");
		foreach($plugins as $name => $option){
			$sender->sendMessage(" - {$name} (v" . $option["version"] . ") : " . implode(", ", $option["authors"]));
		}
		$sender->sendMessage("Libraries(" . count($virions) . "count): ");
		foreach($virions as $name => $option){
			$sender->sendMessage(" - {$name} (v" . $option["version"] . ") : ". $option["author"] . " (" . $option["antigen"] . ")");
		}
		return true;
	}

	public function canReadVirionYAML(string $yml): bool{
		$ymlArr = yaml_parse(file_get_contents($yml));
		return is_array($ymlArr) && isset($ymlArr["name"], $ymlArr["author"], $ymlArr["version"], $ymlArr["antigen"], $ymlArr["api"]);
	}
}