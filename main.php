<?php
	require_once "ajax.class.php";

	function verifier_connexion()
	{
		// Demonstration of a custum function

		if(time()%2 == 0)
			return false;
		return true;
	}

	$a = new AjaxHandler('typeAction');

	$a->addCustomSecureFunction('verifier_connexion');

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


	try {
		$a->execute();
	} catch (Exception $e) {
		$a->__toJSON($e->getMessage());
	}
?>