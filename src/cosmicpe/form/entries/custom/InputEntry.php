<?php

declare(strict_types=1);

namespace cosmicpe\form\entries\custom;

use cosmicpe\form\entries\ModifyableEntry;
use InvalidArgumentException;

final class InputEntry implements CustomFormEntry, ModifyableEntry{

	private string $title;

	private ?string $placeholder;

	private ?string $default;

    /**
     * InputEntry constructor.
     * @param string $title
     * @param string|null $placeholder
     * @param string|null $default
     */
	public function __construct(string $title, ?string $placeholder = null, ?string $default = null){
		$this->title = $title;
		$this->placeholder = $placeholder;
		$this->default = $default;
	}

    /**
     * @return string|null
     */
	public function getPlaceholder() : ?string{
		return $this->placeholder;
	}

    /**
     * @return string|null
     */
	public function getDefault() : ?string{
		return $this->default;
	}

    /**
     * @return string
     */
	public function getValue() : string{
		return $this->default;
	}

    /**
     * @param $value
     */
	public function setValue($value) : void{
		$this->default = $value;
	}

    /**
     * @param mixed $input
     */
	public function validateUserInput($input) : void{
		if(!is_string($input)){
			throw new InvalidArgumentException("Failed to process invalid user input: " . $input);
		}
	}

    /**
     * @return array
     */
	public function jsonSerialize() : array{
		return [
			"type" => "input",
			"text" => $this->title,
			"placeholder" => $this->placeholder ?? "",
			"default" => $this->default ?? ""
		];
	}
}