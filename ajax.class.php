<?php

class AHException extends Exception
{
}

/**
* Handle your ajax treatments with this class for security and efficacity
*
* With this class, you can easily manage your ajax functions with a default argument called "action". 
* When a request is sent, this class checks is the action sent exist. 
* A secure function can be added if a request must have a specific permission or a authentication.
*
* @author Michel EDIGHOFFER <edimitchel@gmail.com>
* 
* 
*/

class AjaxHandler
{
	/*
	* @var String $actionName Index of capture action for execution
	*/
	public $actionName;

	/*
	* @var String $defaultAction Name of the default action if no one is send
	*/
	public $defaultAction;

	/*
	* @var String $action Name of the current action captured
	*/
	public $action = false;

	/*
	* @var String $type Type of the request (POST or GET) for security
	*/
	public $type = false;

	/*
	* @var array $actions List of all actions defined by "addAction" method 
	*/
	private $actions = array();

	/*
	* @var String $headerCntTyp Content type for "__toJSON" method
	*/
	private $headerCntTyp = 'application/json';

	/*
	* @var function/bool $secureFunction Secure the request with a custom function  
	*/
	private $secureFunction = NULL;

	/*
	* @var array $securedAction List of secured actions  
	*/
	private $securedAction = array();

	public function __construct($defaultActionName = "action",$defaultAction = false)
	{
		$this->actionName = $defaultActionName;
		$this->defaultAction = $defaultAction;

		try {
			$this->action = $this->getAction();
		} catch (AHException $e) {
			echo $e->getMessage();
			die;
		}
	}

	public function addCustomSecureFunction($functionName)
	{
		if(function_exists($functionName))
			$this->secureFunction = $functionName();
		else
			throw new AHException("The custom secure function is not defined.", 20);
	}

	public function addAction($functionName,$secure = false,$function)
	{
		if($secure === true)
			$this->securedAction[] = $functionName;

		if(in_array($functionName,$this->actions))
			throw new AHException("\"".$this->action."\" action is yet defined", 05);
		else
			$this->actions[$functionName] = $function;
	}

	private function getAction()
	{
		$action = false;
		if(isset($_GET[$this->actionName]) && !empty($_GET[$this->actionName]))
		{
			$action = $_GET[$this->actionName];
			$this->type = $_GET;
		}
		elseif(isset($_POST[$this->actionName]) && !empty($_POST[$this->actionName]))
		{
			$action = $_POST[$this->actionName];
			$this->type = $_POST;
		}

		if($action !== false)
			return $action;
		else
			throw new AHException("No action captured.", 01);
			
	}

	private function executeFunction($functionName)
	{
		$this->actions[$functionName]();
	}

	public function d($index)
	{
		$Vars = $this->type;

		if(in_array($index,$Vars) && $index !== $this->actionName)
		{
			return $Vars[$index];
		}
		else
			return false;
	}

	public function execute()
	{
		if(in_array($this->action, array_keys($this->actions)))
		{
			if(in_array($this->action,$this->securedAction))
			{
				if($this->secureFunction === NULL)
					throw new AHException("No secure function defined.", 21);
				elseif($this->secureFunction === false)
				{
					throw new AHException("You don't have permission.", 22);
					die;
				}

			}

			$this->executeFunction($this->action);
		}
		else
			throw new AHException("No action calls \"".$this->action."\"", 03);
	}

	public function __toJSON($array)
	{
		if (is_string($array)) {
			$array = array('error'=>$array);
		}

		header("Content-type: ".$this->headerCntTyp);
		echo json_encode($array);
	}
}

?>