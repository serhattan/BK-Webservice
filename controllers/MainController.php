<?php

class MainController{

	public static function get()
	{
		if (!isset($_GET['func']) || empty($_GET['func']))
			$_GET['func']='kitaplarim';

		$postsOnPage = Books::get($_GET['func'],$_GET['userId']);
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
		{
			header('Content-Type: application/json');

			echo json_encode($postsOnPage);
		}
		else
		{
			header('Content-Type: text/html; charset=utf-8');

			echo json_encode($postsOnPage);
			return $postsOnPage;
		}
	}
	public static function insert()
	{
		header('Content-Type: text/html; charset=utf-8');
		if (isset($_POST['bookId'])&&isset($_GET['userId'])){
			Books::insertBook($_POST['bookId'],$_GET['userId']);
		}
	}

	public static function detail()
	{
		header('Content-Type: text/html; charset=utf-8');

		$id = (int)$_GET['id'];
		$userid=$_GET['userId'];
		$post = Books::find($id,$userid);
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
		{
			header('Content-Type: application/json');

			echo json_encode($post,JSON_UNESCAPED_UNICODE);
		}
		else
		{
			echo json_encode($post,JSON_UNESCAPED_UNICODE);
			return $post;
		}
	}

	public static function getinfo()
	{
		header('Content-Type: text/html; charset=utf-8');

		$case = $_GET['info'];
		$email = $_GET['email'];
		$userId = $_GET['userId'];
		$info = Books::choice($case, $email, $userId);
		echo json_encode($info,JSON_UNESCAPED_UNICODE);
		return $info;
	}

	public static function update2()
	{
		header('Content-Type: text/html; charset=utf-8');

		if(isset($_POST['favorite']) OR isset($_POST['rate']) OR isset($_POST['statu'])){
			var_dump($_POST);
			$post = new Books;
			$post->id = $_POST['customer_books_id'];
			$post->favorite = $_POST['favorite'];
			$post->rate = $_POST['rate'];
			$post->statu = $_POST['statu'];
			$post->save2();
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
			{
				header('Content-Type: application/json');
			}else
			{
				header("Location: ?a=detail&id=".$post->id);
				die();
			}
		}	
	}

	public static function shelf()
	{
		header('Content-Type: text/html; charset=utf-8');
		if ($_GET['ab']=='getshelf') {
			$shelfid = (int)$_GET['id'];
			$shelfbooks = Books::getShelfInfo($shelfid);
			echo json_encode($shelfbooks,JSON_UNESCAPED_UNICODE);
			return $shelfbooks;
		}else if ($_GET['ab']=='addtoshelf') {
			if (!empty($_POST)) {
				$array_count = array_count_values($_POST);
				$postArray = array_unique($_POST);
				foreach ($array_count as $key => $value) {
					if ($value%2==0) {
						foreach ($postArray as $key2 => $value2) {
							if ($key==$value2) {
								unset($postArray[$key2]);
							}
						}
					}
				}
			}
			Books::addToShelf($postArray, $_GET['id']);
		}else{
			throw new Exception("Error Processing Request", 1);
		}
	}

	public static function newshelf(){
		header('Content-Type: text/html; charset=utf-8');
		if($_POST['name']!==""){
			$post = new Books;
			$post->name = $_POST['name'];
			$post->note = $_POST['statement'];
			$post->saveshelf($_GET['userId']);
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
			{
				header('Content-Type: application/json');
			}else
			{
				header("Location: ?a=detail&id=".$post->id);
				die();
			}
		}else{
			throw new Exception("Error Processing Request", 1);

		}

	}

	public static function newpost()
	{
		header('Content-Type: text/html; charset=utf-8');
		if( $_POST['name']!=="" AND $_POST['author']!=="" AND $_POST['publisher']!=="" AND $_POST['name']!== "undefined" AND $_POST['author']!=="undefined" AND $_POST['publisher']!=="undefined"){
			$post = new Books;
			$post->img = $_POST['img'];
			$post->name = $_POST['name'];
			$post->publisher = $_POST['publisher'];
			$post->author = $_POST['author'];
			$post->subtitle = $_POST['subtitle'];
			$post->isbn = $_POST['isbn'];
			$post->genre = $_POST['genre'];
			$post->favorite = $_POST['favorite'];
			$post->statu = $_POST['statu'];
			$post->translator = $_POST['translator'];
			$post->edition = $_POST['edition'];
			$post->note = $_POST['note'];
			$post->lend_to = $_POST['lend_to'];
			$post->borrow_from = $_POST['borrow_from'];
			$post->pagenumber = $_POST['pagenumber'];
			$post->shelf = $_POST['shelf'];
			$post->rate = $_POST['rate'];
			$post->series = $_POST['series'];
			if ($_GET['path']=="updatebook") {
				$post->id = $_POST['book_id'];
			}
			$post->save($_GET['userId']);
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
			{
				header('Content-Type: application/json');
			}else
			{
				header("Location: ?a=detail&id=".$post->id);
				die();
			}
		}else{
			throw new Exception("Error Processing Request", 1);
		}
	}

	public static function remove()
	{
		header('Content-Type: text/html; charset=utf-8');
		Books::remove($_GET['id'],$_GET['path'],$_GET['bsid']);
	}
	public static function newUser()
	{
		header('Content-Type: text/html; charset=utf-8');
		if (!isset($_POST['password'])) {
			$_POST['password']=null;
			Books::adduserFb($_POST['id'],$_POST['img'],$_POST['name'],$_POST['email'],$_POST['password']);
		}else{
			Books::adduser($_POST['img'],$_POST['name'],$_POST['email'],$_POST['password']);
		}
	}
}

?>