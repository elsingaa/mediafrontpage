<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

if(!class_exists("widget")) {
	class widget {
		function __construct($widget_init) {
			$this->Id = $widget_init['Id'];
			$this->Type = $widget_init['Type'];
			$this->Block = $widget_init['Block'];
			$this->Title = $widget_init['Title'];
			$this->HeaderFunction = $widget_init['HeaderFunction'];
			$this->Function = $widget_init['Function'];
			$this->Call = $widget_init['Call'];
			$this->Interval = $widget_init['Interval'];
			$this->Stylesheet = $widget_init['Stylesheet'];
			$this->Script = $widget_init['Script'];
			$this->Section = $widget_init['Section'];
			$this->Position = $widget_init['Position'];
		}
		public $Id;
		public $Type;
		public $Block;
		public $Title;
		public $HeaderFunction;
		public $Function;
		public $Call;
		public $Interval;
		public $Stylesheet;
		public $Script;
		public $Section;
		public $Position;
		public $Class;
		public $MobileTitle;
		public $MobileHeader;
		public $MobileFunction;
		public $MobileSection;
		public $MovilePosition;
		public $MobileClass;
		public function addWidget() {

				// Open the database
			try {   $db = new PDO('sqlite:settings.db');

				// Create the database
				$db->exec("CREATE TABLE Widgets (Id TEXT PRIMARY KEY, Type TEXT, Parts TEXT, Block TEXT, Title TEXT, Function TEXT, Call TEXT, Interval INTEGER, HeaderFunction TEXT, Stylesheet TEXT, Script TEXT, Section INTEGER, Position INTEGER)");

				// Add widget to database
				$db->exec("INSERT INTO Widgets (Id, Type, Block, Title, HeaderFunction, Function, Call, Interval, Stylesheet, Script, Section, Position) VALUES ('$this->Id', '$this->Type', '$this->Block', '$this->Title', '$this->HeaderFunction', '$this->Function', '$this->Call', '$this->Interval',  '$this->Stylesheet', '$this->Script', '$this->Section', '$this->Position');");

			} catch(PDOException $e) {
				print 'Exception : '.$e->getMessage();	
			}

			// Close the database connection
			$db = NULL;
		}		
		public function getWidget() {

				// Open the database
			try {	$db = new PDO('sqlite:settings.db');

				//Fetch into an PDOStatement object			
				$request = $db->prepare("SELECT * FROM Widgets WHERE Id='".$this->Id."'");
				$request->execute();

				// Into array
				$widget = $request->fetch(PDO::FETCH_ASSOC);

    			// Close the database connection 
    			$db = null;

			} catch(PDOException $e) {
				print 'Exception : '.$e->getMessage();	
			}
			return $widget;
		}
		public function updateWidget($column, $value) {

				// Open the database
			try {	$db = new PDO('sqlite:settings.db');

				// Replace value in specified column for this widget
				$request = $db->prepare("UPDATE Widgets SET $column='$value' WHERE Id='".$this->Id."'");
				$request->execute();

    			// Close the database connection 
    			$db = null;

			} catch(PDOException $e) {
				print 'Exception : '.$e->getMessage();	
			}
		}
		public function renderWidgetHeaders($directory) {
			global $DEBUG;
			//Support the Widget "stylesheet", "headerfunction", "headerinclude", "script" properties
			if (!empty($this->Type)) {
				switch ($this->Type) {
					case "ajax":
						echo "\t\t<script type=\"text/javascript\" language=\"javascript\">\n";
						
						$loader = (!empty($this->Loader)) ? $this->Loader : "ajaxPageLoad('".$this->Call."', '".$this->Block."');"; 
						if((int)$widget["interval"] > 0) {
							echo "\t\t\tvar ".$this->Block."_interval = setInterval(\"".$this->Loader."\", ".$this->Interval.");\n";
						}
						echo "\t\t\t".$this->Loader."\n";
						echo "\t\t</script>\n";
							break;
					case "mixed":
						foreach( $this->Parts as $parts ) {
							renderWidgetHeaders($parts);
						}
						break;
				}
			}
			if(!empty($this->Stylesheet) && (strlen($this->Stylesheet) > 0)) {
				echo "\t\t<link rel=\"stylesheet\" type=\"text/css\" href=\"".$directory."/".$this->Stylesheet."\" />\n";
			}
			if(!empty($this->Script) && (strlen($this->script) > 0)) {
				echo "\t\t<link type=\"text/javascript\" language=\"javascript\ src=\"".$directory."/".$this->Script."\" />\n";
			}
			if(!empty($this->HeaderInclude) && (strlen($this->HeaderInclude) > 0)) {
				echo "\t\t".$this->HeaderInclude."\n";
			}
			if(!empty($this->HeaderFunction) && (strlen($this->HeaderFunction) > 0)) {
				if($DEBUG) { echo "\n<!-- Calling Function:".$this->HeaderFunction." -->\n"; }
				eval($this->HeaderFunction);
			}
		}
								
	}
}
?>
