<?php


function verifier_connexion()
{
	return false;
}


/**
* ajax-hanlder
* Handle your ajax treatments with this class for security and efficacity
*/
class AjaxHandler
{
	public $action = false;

	public $type = false;

	private $actions = array();

	private $headerCntTyp = 'Content-type: application/json';

	private $secureFunction = NULL;

	private $securedAction = array();

	// private ;

	public function __construct()
	{
		try {
			$action = $this->getAction();
		} catch (Exception $e) {
			echo $e->getMessage();
			die;
		}
	}

	public function addCustomSecureFunction($functionName)
	{
		if(function_exists($functionName))
			$this->secureFunction = $functionName();
		else
			throw new Exception("The custom secure function is not defined.", 20);
	}

	public function addAction($functionName,$secure = false,$function)
	{
		if($secure === true)
			$this->securedAction[] = $functionName;

		if(in_array($functionName,$this->actions))
			throw new Exception("\"".$this->action."\" action is yet defined", 05);
		else
			$this->actions[$functionName] = $function;
	}

	private function getAction()
	{
		$action = false;
		if(isset($_GET['action']) && !empty($_GET['action']))
		{
			$action = $_GET['action'];
			$this->type = $_GET;
		}
		elseif(isset($_POST['action']) && !empty($_POST['action']))
		{
			$action = $_POST['action'];
			$this->type = $_POST;
		}

		if($action !== false)
			$this->action = $action;
		else
			throw new Exception("No action captured.", 01);
			
	}

	private function executeFunction($functionName)
	{
		$this->actions[$functionName]();
	}

	public function d($index)
	{
		$Vars = $this->type;

		if(in_array($index,$Vars) && $index !== "action")
		{
			return $Vars[$index];
		}
		else
			return false;
	}

	public function execute()
	{
		$numArg = func_num_args();

		if(in_array($this->action, array_keys($this->actions)))
		{
			if(in_array($this->action,$this->securedAction))
			{
				if($this->secureFunction === NULL)
					throw new Exception("No secure function defined.", 21);
				elseif($this->secureFunction === false)
				{
					throw new Exception("You don't have permission.", 22);
					die;
				}

			}

			$this->executeFunction($this->action);
		}
		else
			throw new Exception("No action calls \"".$this->action."\"", 03);
	}

	public function __toJSON($array)
	{
		if (is_string($array)) {
			$array = array('error'=>$array);
		}

		header($this->headerCntTyp);
		echo json_encode($array);
	}
}

$a = new AjaxHandler();

$a->addCustomSecureFunction('verifier_connexion');

$a->addAction('newPicture',true,function(){
	global $a;
	$titre = $a->d('titre');

	$nom = $a->d('nom');

	$image = $a->d('image');

	$a->__toJSON(array('ok'=>true));
});

try {
	$a->execute();
} catch (Exception $e) {
	$a->__toJSON($e->getMessage());
}

?>