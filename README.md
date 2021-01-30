# Forms
A library for PocketMine-MP providing an API to create forms.

## Developer Docs
### Modal Form
These forms can serve as a prompt window. They require a title field, a body and either one or two buttons.
The second button is pretty unnecessary as it does the same action as of closing the window. The bedrock client doesn't let servers
distinguish between a "prompt window close action" and a "second button click action".
```php
class MyModalForm extends ModalForm{

	public function __construct(){
		parent::__construct("/suicide prevention", // <- title
			"Are you sure you'd like to commit /suicide?" . TextFormat::EOL . // <- body
			"You will lose all your inventory contents if you proceed."
		);
		$this->setFirstButton("YES");
		$this->setSecondButton("I'll need time to make up my mind."); // optional second button
	}
	
	protected function onAccept(Player $player) : void{
		// Called when first button is clicked
		$player->getServer()->dispatchCommand($player, "kill");
	}
	
	protected function onClose(Player $player) : void{
		// Called when second button is clicked OR player closes the form
		$player->sendMessage("You can access this form again, anytime, using /suicide");
	}
}

$player->sendForm(new MyModalForm());
```

### Simple Form
A bunch of buttons, that's all this form provides. Some servers like using this as a method of sending client text in a form window.
You can add as many buttons as you like, or no buttons.
```php
class MySimpleForm extends SimpleForm{

	public function __construct(){
		parent::__construct("/suicide prevention", // <- title
			"There's more to life than our shithole education system!" . TextFormat::EOL . // <- body
			"Do you really think it's worth losing all your diamonds?"
		);
		
		$this->addButton(
			new Button("Ugh..?", new Icon(Icon::TYPE_URL, "http://meme.images/three-qn-marks.png")), // <- button params: body, icon (optional)
			function(Player $player, int $button_index) : void{ // <- (optional) callback when this button is clicked
				$player->getServer()->dispatchCommand($player, "kill");
			}
		);
		
		$this->addButton(new Button("I guess you have a point"));
		
		$this->addButton(new Button(
			"Emergency Call" . TextFormat::EOL .
			"There are people out there" . TextFormat::EOL .
			"who genuinely care for you!"
		));
	}
	
	public function onClickButton(Player $player, Button $button, int $button_index) : void{
		// optional override
		// for buttons that dont have a callback
	}
	
	public function onClose(Player $player) : void{
		// when player closes this form
	}
}

$player->sendForm(new MySimpleForm());
```

### Paginated Form
These are simple forms that provide an interface for creating simple forms with pages.
```php
class MyPaginatedForm extends PaginatedForm{

	private const ENTRIES_PER_PAGE = 10;

	/** @var int */
	private $total_players;

	/** @var string[] */
	private $people_to_avoid = [];

	public function __construct(int $current_page = 1){
		$players = Server::getInstance()->getOnlinePlayers();
		$this->total_players = count($players);
		
		foreach(array_slice(
			$players,
			($current_page - 1) * self::ENTRIES_PER_PAGE,
			self::ENTRIES_PER_PAGE
		) as $player){
			$this->people_to_avoid[] = $player->getName();
		}
		
		// Call parent::__construct() at the end.
		parent::__construct(
			"List of people to avoid" // <- title
			"Avoid talking with or being in the vicinity of these people." // <- content
			$current_page // <- current page, defaults to 1 (pages start at 1, not 0)
		);
	}
	
	protected function getPreviousButton() : Button{
		return new Button("<- Go back");
	}
	
	protected function getNextButton() : Button{
		return new Button("Next Page ->");
	}
	
	protected function getPages() : int{
		// Returns the maximum number of pages.
		// This will alter the visibility of previous and next buttons.
		// For example:
		//   * If we are on page 7 of 7, the "next" button wont be visible
		//   * If we are on page 6 of 7, the "next" and "previous" button WILL be visible
		//   * If we are on page 1 of 7, the "previous" button won't be visible
		return (int) ceil(count($this->total_players) / self::ENTRIES_PER_PAGE);
	}
	
	protected function populatePage() : void{
		// populate this page with buttons
		foreach($this->people_to_avoid as $people){
			$this->addButton(new Button($people, "- Responsible for limiting PhotoTransferPacket to edu only"));
		}
	}
	
	protected function sendPreviousPage(Player $player) : void{
		$player->sendForm(new self($this->current_page - 1));
	}
	
	protected function sendNextPage(Player $player) : void{
		$player->sendForm(new self($this->current_page + 1));
	}
}

$player->sendForm(new MyPaginatedForm());
```


### Custom Form
Unlike the other two forms, this form lets players enter a custom input. At the bottom of this form is a "Submit" button. No, you
cannot modify that button's text.
These forms don't contain buttons. As of now, there are 6 types of entries you can add to this form.

#### 1. Dropdown
The closest alternative to buttons. Dropdowns let players select an option from a list of options.
```php
$entry = new DropdownEntry(
	"What's your gender?", // the first parameter is the name of the dropdown
	"Male", // the next parameters are the dropdown options
	"Female",
	"Prefer not to answer"
);
$entry->setDefault("Prefer not to answer");
```

#### 2. Input
Lets players input a string. Also can be useful in making shit confirmatory prompts.
```php
// Parameters:
//  * Name of the input option
//  * Placeholder (displayed in grey) [optional]
//  * Default (fallback value if nothing entered) [optional]
$entry = new InputEntry("Enter your name", "Sbeve");
```

#### 3. Label
Remember when I said these forms let players enter a custom input? This entry is an exception. Labels only display text[citation needed].
```php
$entry = new LabelEntry("Probably can fit a huge wall of text here?");
```

#### 4. Slider
Lets players choose a value between two numbers using a slider to select.
```php
$entry = new Slider(
	"How would you rate our suicide prevention?", // <- title
	0.0, // <- minimum value
	10.0, // <- maximum value
	0.5, // <- step size,
	10.0 // <- default value
);
```

#### 5. Step Slider
Resemble sliders, but their value is a string.
```php
$entry = new StepSlider(
	"Brain size", // <- title
	"Small", // <- the next parameters are steps in order
	"Medium",
	"Large"
);
$entry->setDefault("Medium");
```

#### 6. Toggle
A simple switch.
```php
$entry = new Toggle("Enable suicide prevention", true); // <- parameters: title, bool (true: turned on, false: turned off)
```

#### Creating the custom form
```php
class MyCustomForm extends CustomForm{

	public function __construct(){
		parent::__construct("Suicide prevention confirmation");
		
		$this->addEntry(new LabelEntry("Are you sure you'd like to commit /suicide? You will lose all your inventory contents."));
		$this->addEntry(new LabelEntry("Type " . TextFormat::BOLD . "YES" . TextFormat::RESET . "below to confirm!");
		
		
		$this->addEntry(
			new InputEntry("", "Type YES here"),
			function(Player $player, InputEntry $entry, string $value) : void{
				if($value === "YES"){
					$player->getServer()->dispatchCommand($player, "kill");
				}else{
					$player->sendMessage("Suicide aborted.");
				}
			}
		);
	}
	
	public function onClose(Player $player) : void{
		// when player closes this form
	}
}

$player->sendForm(new MyCustomForm());
```
