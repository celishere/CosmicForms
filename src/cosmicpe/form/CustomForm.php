<?php

declare(strict_types=1);

namespace cosmicpe\form;

use Closure;
use Exception;
use cosmicpe\form\entries\custom\CustomFormEntry;
use cosmicpe\form\entries\ModifyableEntry;
use cosmicpe\form\types\Icon;

use pocketmine\form\FormValidationException;
use pocketmine\Player;

abstract class CustomForm implements Form{

	private string $title;

	private ?Icon $icon;

	private array $entries = [];

	/** @var Closure[] */
	private $entry_listeners = [];

    /**
     * CustomForm constructor.
     * @param string $title
     */
	public function __construct(string $title){
		$this->title = $title;
	}

    /**
     * @param Icon|null $icon
     * @return void
     */
	final public function setIcon(?Icon $icon) : void{
		$this->icon = $icon;
	}

	/**
	 * @param CustomFormEntry $entry
	 * @param Closure|null $listener
     * @return void
	 *
	 * Listener parameters:
	 *  * Player $player
	 *  * InputEntry $entry
	 *  * mixed $value [NOT NULL]
	 */
	final public function addEntry(CustomFormEntry $entry, Closure $listener = null) : void{
		$this->entries[] = $entry;
		if($listener !== null){
			$this->entry_listeners[array_key_last($this->entries)] = $listener;
		}
	}

    /**
     * @param Player $player
     * @param mixed $data
     * @return void
     */
	public function handleResponse(Player $player, $data) : void{
		if($data === null){
			$this->onClose($player);
		}else{
			try{
				foreach($data as $key => $value){
					if(isset($this->entry_listeners[$key])){
						$entry = $this->entries[$key];
						if($entry instanceof ModifyableEntry){
							$entry->validateUserInput($value);
						}
						$this->entry_listeners[$key]($player, $this->entries[$key], $value);
					}
				}
			}catch(Exception $e){
				throw new FormValidationException($e->getMessage());
			}
		}
	}

    /**
     * @param Player $player
     * @return void
     */
	public function onClose(Player $player) : void{
	}

    /**
     * @return array
     */
	final public function jsonSerialize() : array{
		return [
			"type" => "custom_form",
			"title" => $this->title,
			"icon" => $this->icon,
			"content" => $this->entries
		];
	}
}