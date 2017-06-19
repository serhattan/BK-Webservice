<?php

class MainController{

	public static function kitaplarim()
	{
		$postsOnPage = Books::get('LAST');

		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
		{
			header('Content-Type: application/json');

			echo json_encode($postsOnPage);
		}
		else
		{
			header('Content-Type: text/html; charset=utf-8');

			include "views/allPost.php";
		}
	}

	public static function detail()
	{
		header('Content-Type: text/html; charset=utf-8');

		$id = (int)$_GET['id'];
		$post = Books::find($id);

		include "views/postDetail.php";
	}

	public static function newpost()
	{
		header('Content-Type: text/html; charset=utf-8');
		
		if(isset($_POST['name']) AND isset($_POST['author']) AND isset($_POST['image'])){
			$post = new Books;
			$post->img = $_POST['image'];
			$post->name = $_POST['name'];
			$post->author = $_POST['author'];
			$post->subtitle = $_POST['subtitle'];
			$post->genre = $_POST['genre'];
			$post->publisher = $_POST['publisher'];
			$post->isbn = $_POST['isbn'];
			$post->save();
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
			{
				header('Content-Type: application/json');
			}else
			{
				header("Location: ?a=detail&id=".$post->id);
				die();
			}
		}

		include "views/newPost.php";
	}


	public static function editpost()
	{
		header('Content-Type: text/html; charset=utf-8');

		$id = (int)$_GET['id'];

		$post = BlogPost::find($id);

		if(isset($_POST['name']) OR isset($_POST['author']) OR isset($_POST['image']) OR isset($_POST['subtitle']) OR isset($_POST['genre']) OR isset($_POST['publisher']) OR isset($_POST['isbn'])) {
			$post->name = $_POST['name'];
			$post->author = $_POST['author'];
			$post->img = $_POST['image'];
			$post->subtitle = $_POST['subtitle'];
			$post->genre = $_POST['genre'];
			$post->publisher = $_POST['publisher'];
			$post->isbn = $_POST['isbn'];
			$post->save();

			header("Location: ?a=detail&id=".$post->id);
			die();
		}

		include "views/editpost.php";
	}
}

?>