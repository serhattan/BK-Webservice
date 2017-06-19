<?php

class Books{

	public $id;
	public $image;
	public $name;
	public $author;
	public $subtitle;
	public $genre;
	public $publisher;
	public $isbn;


	protected $con;
	private $host = "localhost";
	private $dbname ="mycloudlibrary";
	private $username = "root";
	private $password = "";

	public function __construct(){
		try{
			$this->con = new PDO("mysql:host=".$this->host.";dbname=".$this->dbname.";charset=UTF8;", $this->username, $this->password);
		}catch (PDOException $e){
			die("Veritabanı bağlantı hatası: ".$e->getMessage());
		}
	}

	public function __destruct(){
		$this->con = null;
	}

	public function initById($id){
		$postById = $this->con->query("SELECT * FROM customer_books JOIN books ON customer_books.book_id = books.id WHERE customer_books.id=$id")->fetchAll(PDO::FETCH_OBJ);
		$this->id=$postById[0]->id;
		$this->image=$postById[0]->img;
		$this->name=$postById[0] ->name;
		$this->author=$postById[0] ->author;
		$this->favorite=$postById[0]->favorite;
		$this->rate=$postById[0]->rate;
		$this->statu=$postById[0]->statu;
		$this->subtitle=$postById[0] ->subtitle;
		$this->genre=$postById[0] ->genre;
		$this->publisher=$postById[0] ->publisher;
		$this->isbn=$postById[0] ->isbn;
	}

	private function insert($image, $name, $author, $subtitle, $genre, $publisher, $isbn){
		$add = $this->con->prepare("INSERT INTO books(img, name, author, subtitle, genre, publisher, isbn) VALUES (?,?,?,?,?,?,?)");
		$isAdded = $add->execute(array($image, $name, $author, $subtitle, $genre, $publisher, $isbn));

		if ($isAdded) {
			$this->id = $this->con->lastInsertId();
			return true;
		}
		return false;
	}

	private function update(){
		$update = $this->con->prepare("UPDATE books SET img = :imgRef, name = :nameRef, author = :authorRef, subtitle = :subtitleRef, genre = :genreRef, publisher = :publisherRef, isbn = :isbnRef WHERE id = :idRef");

		$isUpdated = $update->execute(
			array(
				"imgRef" => $this->image,
				"nameRef" => $this->name,
				"authoRef" => $this->author,
				"subtitleRef" => $this->subtitle,
				"genreRef" => $this->genre,
				"publisherRef" => $this->publisher,
				"isbnRef" => $this->isbn,
				"idRef" => $this->id,
				)
			);
		if($isUpdated)
			return true;

		return false;
	}

	public function save(){
		if (!is_null($this->id) && !empty($this->id)) {
			return $this->update();
		}
		else{
			return $this->insert($this->image, $this->name, $this->author, $this->subtitle, $this->genre, $this->publisher, $this->isbn); 
		}
	}
	
	public function getPosts($orderBy="LAST")
	{
		if ($orderBy==="FIRST") $orderByAtQuery = "ASC";
		elseif ($orderBy==="LAST") $orderByAtQuery = "DESC";
		else $orderByAtQuery = "DESC";

		$posts = $this->con->query("SELECT customer_books.id, books.img, books.name, books.author, books.publisher FROM customer_books INNER JOIN books ON customer_books.book_id = books.id WHERE customer_books.customer_id=1")->fetchAll(PDO::FETCH_OBJ);
		return $posts;
	}

	public function getAllPosts($orderBy = "FIRST"){

		if ($orderBy==="FIRST") $orderByAtQuery = "ASC";
		elseif ($orderBy==="LAST") $orderByAtQuery = "DESC";
		else $orderByAtQuery = "DESC";

		$posts = $this->con->query("SELECT * FROM book ORDER BY created_at".$orderByAtQuery)->fetchAll(PDO::FETCH_OBJ);
		return $posts;
	}

	public static function find($id){
		$returnObj = new self;
		$returnObj->initById($id);
		return $returnObj;
	}

	public static function all($orderBy = "FIRST"){
		$selfObj = new self;
		return $selfObj->getAllPosts($orderBy);
	}

	public static function get($orderBy = "FIRST"){
		$selfObj = new self;
		return $selfObj->getPosts($orderBy);
	}

}