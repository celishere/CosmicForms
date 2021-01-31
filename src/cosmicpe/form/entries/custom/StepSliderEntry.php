<?php

declare(strict_types=1);

namespace cosmicpe\form\entries\custom;

use ArgumentCountError;
use cosmicpe\form\entries\ModifyableEntry;
use InvalidArgumentException;

final class StepSliderEntry implements CustomFormEntry, ModifyableEntry{

	private string $title;

	private array $steps;

	private int $default = 0;

    /**
     * StepSliderEntry constructor.
     * @param string $title
     * @param string ...$steps
     */
	public function __construct(string $title, string ...$steps){
		$this->title = $title;
		$this->steps = $steps;
	}

    /**
     * @return string
     */
	public function getValue() : string{
		return $this->steps[$this->default];
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
		if(!is_int($input) || $input < 0 || $input >= $this->steps->count()){
			throw new InvalidArgumentException("Failed to process invalid user input: " . $input);
		}
	}

    /**
     * @param string $default_step
     */
	public function setDefault(string $default_step) : void{
		foreach($this->steps as $index => $step){
			if($step === $default_step){
				$this->default = $index;
				return;
			}
		}

		throw new ArgumentCountError("Step \"" . $default_step . "\" does not exist!");
	}

    /**
     * @return array
     */
	public function jsonSerialize() : array {
		return [
			"type" => "step_slider",
			"text" => $this->title,
			"steps" => $this->steps,
			"default" => $this->default
		];
	}
}