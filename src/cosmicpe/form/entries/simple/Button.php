<?php

declare(strict_types=1);

namespace cosmicpe\form\entries\simple;

use cosmicpe\form\entries\FormEntry;
use cosmicpe\form\types\Icon;

final class Button implements FormEntry{

	private string $title;

	private ?Icon $icon;

	private array $data;

    /**
     * Button constructor.
     * @param string $title
     * @param Icon|null $icon
     * @param array $data
     */
	public function __construct(string $title, ?Icon $icon = null, array $data = []){
		$this->title = $title;
		$this->icon = $icon;
		$this->data = $data;
	}

    /**
     * @return array
     */
	public function jsonSerialize() : array{
		return [
			"text" => $this->title,
			"image" => $this->icon
		];
	}

    /**
     * @return array
     */
	public function getData(): array {
	    return $this->data;
    }
}