<?php
	require_once "ajax.class.php";

	function verifier_connexion()
	{
		// Demonstration of a custum function

		if(time()%2 == 0)
			return false;
		return true;
	}

	// Create the ajax handler object
	$a = new AjaxHandler('typeAction');

	// Adding a function that he can stop undesirable people to execute function
	$a->addCustomSecureFunction('verifier_connexion');

	// Adding a new action with the name, if it must be secured and the function
	$a->addAction('newPicture',true,function(){
		global $a;
		$titre = $a->d('titre');

		$nom = $a->d('nom');

		$image = $a->d('image');

		$a->__toJSON(array('ok'=>true));
	});

	$a->addAction('sdsd',false,function(){
		global $a;
		$titre = $a->d('titre');

		$nom = $a->d('nom');

		$image = $a->d('image');

		$a->__toJSON(array('ok'=>true));
	});

	// Execute and execute function 
	try {
		$a->execute();
	} catch (Exception $e) {
		$a->__toJSON($e->getMessage());
	}
?>