<?php

declare(strict_types=1);

namespace cosmicpe\form;

use cosmicpe\form\entries\simple\Button;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

abstract class PaginatedForm extends SimpleForm{

	protected int $current_page;

    /**
     * PaginatedForm constructor.
     * @param string $title
     * @param string|null $content
     * @param int $current_page
     */
	public function __construct(string $title, ?string $content = null, int $current_page = 1){
		parent::__construct($title, $content);

		$this->current_page = $current_page;

		$this->populatePage();

		$pages = $this->getPages();

		if($this->current_page === 1){
			if($pages > 1){
				$this->addButton($this->getNextButton(), function(Player $player, int $data) : void{ $this->sendNextPage($player); });
			}
		}else{
			$this->addButton($this->getPreviousButton(), function(Player $player, int $data) : void{ $this->sendPreviousPage($player); });
			if($this->current_page < $pages){
				$this->addButton($this->getNextButton(), function(Player $player, int $data) : void{ $this->sendNextPage($player); });
			}
		}
	}

    /**
     * @return Button
     */
	protected function getPreviousButton() : Button{
		return new Button(TextFormat::BOLD . TextFormat::BLACK . "Previous Page" . TextFormat::RESET . TextFormat::EOL . TextFormat::DARK_GRAY . "Turn to the previous page");
	}

    /**
     * @return Button
     */
	protected function getNextButton() : Button{
		return new Button(TextFormat::BOLD . TextFormat::BLACK . "Next Page" . TextFormat::RESET . TextFormat::EOL . TextFormat::DARK_GRAY . "Turn to the next page");
	}

    /**
     * @return int
     */
	abstract protected function getPages() : int;

    /**
     * @return void
     */
	abstract protected function populatePage() : void;

    /**
     * @param Player $player
     * @return void
     */
	abstract protected function sendPreviousPage(Player $player) : void;

    /**
     * @param Player $player
     * @return void
     */
	abstract protected function sendNextPage(Player $player) : void;
}