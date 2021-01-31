<?php

declare(strict_types=1);

namespace cosmicpe\form;

use pocketmine\Player;

abstract class ModalForm implements Form{

	private string $title;

	private ?string $content;

	private string $first_button = "";

	private ?string $second_button;

    /**
     * ModalForm constructor.
     * @param string $title
     * @param string|null $content
     */
	public function __construct(string $title, ?string $content = null){
		$this->title = $title;
		$this->content = $content;
	}

    /**
     * @param string $button
     * @return void
     */
	final public function setFirstButton(string $button) : void{
		$this->first_button = $button;
	}

    /**
     * @param string $button
     * @return void
     */
	final public function setSecondButton(string $button) : void{
		$this->second_button = $button;
	}

    /**
     * @param Player $player
     * @param mixed $data
     * @return void
     */
	public function handleResponse(Player $player, $data) : void{
		if(!$data){
			$this->onClose($player);
			return;
		}

		$this->onAccept($player);
	}

    /**
     * @param Player $player
     * @return void
     */
	protected function onAccept(Player $player) : void{
	}

    /**
     * @param Player $player
     * @return void
     */
	protected function onClose(Player $player) : void{
	}

    /**
     * @return array
     */
	final public function jsonSerialize() : array{
		return [
			"type" => "modal",
			"title" => $this->title,
			"content" => $this->content ?? "",
			"button1" => $this->first_button,
			"button2" => $this->second_button ?? ""
		];
	}
}