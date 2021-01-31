<?php

declare(strict_types=1);

namespace cosmicpe\form\entries\custom;

use cosmicpe\form\entries\ModifyableEntry;
use InvalidArgumentException;

final class DropdownEntry implements CustomFormEntry, ModifyableEntry{

	private string $title;

	private array $options;

	private int $default = 0;

    /**
     * DropdownEntry constructor.
     * @param string $title
     * @param array $options
     */
	public function __construct(string $title, array $options){
		$this->title = $title;
		$this->options = $options;
	}

    /**
     * @return array
     */
	public function getValue() : array {
		return $this->options[$this->default];
	}

    /**
     * @param $value
     */
	public function setValue($value) : void{
		$this->setDefault($value);
	}

    /**
     * @param mixed $input
     */
	public function validateUserInput($input) : void{
		if(!is_int($input) || !isset($this->options[$input])){
			throw new InvalidArgumentException("Failed to process invalid user input: " . $input);
		}
	}

    /**
     * @param string $default_option
     */
	public function setDefault(string $default_option) : void{
		foreach($this->options as $index => $option){
			if($option === $default_option){
				$this->default = $index;
				return;
			}
		}

		throw new InvalidArgumentException("Option \"" . $default_option . "\" does not exist!");
	}

    /**
     * @return array
     */
	public function jsonSerialize() : array {
		return [
			"type" => "dropdown",
			"text" => $this->title,
			"options" => $this->options,
			"default" => $this->default
		];
	}
}