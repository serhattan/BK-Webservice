<?php
include_once "./dbinfo.php";

class Books{
	public $id;
	public $img;
	public $name;
	public $author;
	public $favorite;
	public $statu;
	public $rate;
	public $series;
	public $subtitle;
	public $genre;
	public $publisher;
	public $isbn;
	public $note;
	public $lend_to;
	public $borrow_from;
	public $translator;
	public $edition;
	public $pagenumber;
	public $shelf;

//buradaki bilgileri bir sabit olarak tanımlayacağım ve dışarı alacağım
	protected $con;
	private $host = host;
	private $dbname = dbname;
	private $username = user;
	private $password = password;

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

	public function initById($id,$userId){

		$postById = $this->con->query("SELECT * FROM customer_books INNER JOIN books ON customer_books.book_id = books.id WHERE customer_books.id=$id AND customer_books.customer_id=$userId")->fetchAll(PDO::FETCH_OBJ);

		$postByIdShelf = $this->con->query("SELECT book_shelf.id, book_shelf.customer_books_id,book_shelf.customerShelf_id FROM book_shelf INNER JOIN customer_books ON book_shelf.customer_books_id = customer_books.id WHERE customer_books.id=$id AND customer_books.customer_id=$userId")->fetchAll(PDO::FETCH_OBJ);
		if (!empty($postById)) {
			$this->id=$postById[0]->id;
			$this->book_id=$postById[0]->book_id;
			$this->img=$postById[0]->img;
			$this->name=$postById[0]->name;
			$this->author=$postById[0]->author;
			$this->favorite=$postById[0]->favorite;
			$this->rate=$postById[0]->rate;
			$this->statu=$postById[0]->statu;
			$this->subtitle=$postById[0]->subtitle;
			$this->genre=$postById[0]->genre;
			$this->publisher=$postById[0]->publisher;
			$this->isbn=$postById[0]->isbn;
			$this->note=$postById[0]->note;
			$this->lend_to=$postById[0]->lend_to;
			$this->borrow_from=$postById[0]->borrow_from;
			$this->translator=$postById[0]->translator;
			$this->edition=$postById[0]->edition;
			$this->series=$postById[0]->series;
			$this->pagenumber=$postById[0]->pagenumber;
			if (!empty($postByIdShelf)) {
				$this->shelf=$postByIdShelf[0]->customerShelf_id;
			}else{
				$this->shelf="0";
			}
		}
	}

	private function insert($userId,$img, $name, $author, $publisher,  $subtitle, $isbn, $genre, $favorite, $statu, $translator, $edition, $note, $lend_to, $borrow_from, $pagenumber, $shelf)
	{
		$addbooks = $this->con->prepare("INSERT INTO books(img, name, author, publisher, subtitle, isbn, genre) VALUES (?,?,?,?,?,?,?)"); 
		$isAddedBooks = $addbooks->execute(array($img, $name, $author, $publisher, $subtitle, $isbn, $genre));
		if ($isAddedBooks) {
			$this->id = $this->con->lastInsertId();
		}else{
			alert("Bir sorun oluştu ve kitabınızı ekleyemedik");
			return false;
		}
		
		$addcbooks = $this->con->prepare("INSERT INTO customer_books(customer_id, book_id, favorite, statu, rate, translator, series, edition, note, lend_to, borrow_from, pagenumber) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");

		$isAddedCbooks = $addcbooks->execute(array($userId, $this->id, $favorite, $statu, $this->rate, $translator, $this->series, $edition, $note, $lend_to, $borrow_from, $pagenumber));
		if ($isAddedCbooks) {
			$this->id = $this->con->lastInsertId();
		}else{alert("Bir sorun oluştu ve kitabınızı ekleyemedik");return false;}
		
		$addbooksheld = $this->con->prepare("INSERT INTO  book_shelf(customer_books_id,customerShelf_id) VALUES(?,?)");
		$isAddedBbookshelf = $addbooksheld->execute(array($this->id, $shelf));
	}

	public function save($userId){
		if (!is_null($this->id) && !empty($this->id)) {
			return $this->update();
		}
		else{
			return $this->insert(
				$userId,
				$this->img,
				$this->name,
				$this->author,
				$this->publisher,
				$this->subtitle,
				$this->isbn,
				$this->genre,
				$this->favorite,
				$this->statu,
				$this->translator,
				$this->edition,
				$this->note,
				$this->lend_to,
				$this->borrow_from,
				$this->pagenumber,
				$this->shelf
				);
			//  $this->$series
		}
	}
	public function save2(){
		if (!is_null($this->id) && !empty($this->id)) {
			return $this->update2();
		}
	}
	public function saveshelf($userId){
		$addshelf = $this->con->prepare("INSERT INTO customer_shelfs(customer_id, name, statement) VALUES (?,?,?)"); 
		$isAddedshelf = $addshelf->execute(array($userId, $this->name, $this->note));
		if (!$isAddedshelf) {
			throw new Exception("Shelf Add Failed", 1);
		}
	}
	public function adding($data_array,$id){
		$addedBefore=$this->con->query("SELECT * FROM book_shelf")->fetchAll(PDO::FETCH_OBJ);
		$arraycount=count($data_array);
		for ($i=0; $i < count($addedBefore); $i++) { 
			for ($a=0; $a < $arraycount; $a++) { 
				if(($addedBefore[$i]->customer_books_id==$data_array[$a]) && ($addedBefore[$i]->customerShelf_id==$id)){
					unset($data_array[$a]);
				}	
			}
		}
		$addbooks = $this->con->prepare("INSERT INTO book_shelf(customer_books_id, customerShelf_id) VALUES (?,?)");
		foreach ($data_array as $key => $value) {
			$isAddedBooks = $addbooks->execute(array($value, $id));
		}
	}

	public function getinfo($info,$email,$userId){
		switch ($info) {
			case 'login':
			$login_query=$this->con->prepare("SELECT * FROM customer WHERE email = '".$email."'");
			$login_query->execute();
			$row = $login_query->fetch();
			return $row;
			break;
			case 'lendto':
			$lend_to_query=$this->con->query("SELECT * FROM customer_books WHERE (lend_to != 'undefined') && lend_to != 'null' && customer_id=$userId ORDER BY id DESC")->fetchAll(PDO::FETCH_OBJ);
			return $lend_to_query;
			break;
			case 'borrowfrom':
			$borrow_from_query=$this->con->query("SELECT * FROM customer_books WHERE (borrow_from != 'undefined') && borrow_from != 'null' && customer_id=$userId ORDER BY id DESC")->fetchAll(PDO::FETCH_OBJ);
			return $borrow_from_query;
			break;
			case 'notes':
			$notes_query=$this->con->query("SELECT * FROM customer_books WHERE (note != 'undefined') && note != 'null' && customer_id=$userId ORDER BY id DESC")->fetchAll(PDO::FETCH_OBJ);
			return $notes_query;
			break;
			case 'reading':
			$reading_query=$this->con->query("SELECT * FROM customer_books WHERE statu='2' && customer_id=$userId ORDER BY id DESC")->fetchAll(PDO::FETCH_OBJ);
			return $reading_query;
			break;
			case 'willread':
			$willread_query=$this->con->query("SELECT * FROM customer_books WHERE statu='3' && customer_id=$userId ORDER BY id DESC")->fetchAll(PDO::FETCH_OBJ);
			return $willread_query;
			break;
			case 'read':
			$read_query=$this->con->query("SELECT * FROM customer_books WHERE statu='1' && customer_id=$userId ORDER BY id DESC")->fetchAll(PDO::FETCH_OBJ);
			return $read_query;
			break;
			default:
			throw new Exception('Method Not Supported', 405);
			break;
		}
		return $selfObj;
	}

	private function update2(){
		$update2sql= $this->con->prepare("UPDATE customer_books SET favorite = :favorite, rate = :rate, statu = :statu WHERE id= :id");
		$isupdated2=$update2sql->execute(
			array(
				"favorite" => $this->favorite,
				"rate" => $this->rate,
				"statu" => $this->statu,
				"id" => $this->id,
				)
			);
		if($isupdated2)
			return true;
		return false;
	}

	private function update(){

		$update = $this->con->prepare("UPDATE books, customer_books SET 
			books.name = :name,
			books.author =:author,
			books.publisher = :publisher,
			books.subtitle = :subtitle,
			books.isbn = :isbn,
			books.genre = :genre,
			customer_books.favorite = :favorite,
			customer_books.rate = :rate,
			customer_books.statu = :statu,
			customer_books.note = :note,
			customer_books.lend_to = :lend_to,
			customer_books.borrow_from = :borrow_from,
			customer_books.translator = :translator,
			customer_books.edition = :edition,
			customer_books.series = :series,
			customer_books.pagenumber = :pagenumber
			WHERE books.id = :id AND customer_books.book_id = :id");

		$isUpdated = $update->execute(
			array(
				"name" => $this->name,
				"author" => $this->author,
				"publisher" => $this->publisher,
				"subtitle" => $this->subtitle,
				"isbn" => $this->isbn,
				"genre" => $this->genre,
				"favorite" => $this->favorite,
				"rate" => $this->rate,
				"statu" => $this->statu,
				"note" => $this->note,
				"lend_to" => $this->lend_to,
				"borrow_from" => $this->borrow_from,
				"translator" => $this->translator,
				"edition" => $this->edition,
				"series" => $this->series,
				"pagenumber" => $this->pagenumber,
				"id" => $this->id
				)
			);
		if($isUpdated){
			return true;
		}else{
			throw new Exception("Error Processing Request", 1);	
		}

	}

	public function getPosts($path,$userId)
	{
		switch ($path) {
			case 'kitaplarim':
			$posts = $this->con->query("SELECT customer_books.id, books.img, books.name, books.author, books.publisher, customer_books.statu, customer_books.rate FROM customer_books INNER JOIN books ON customer_books.book_id = books.id WHERE customer_books.customer_id=$userId  ORDER BY id DESC")->fetchAll(PDO::FETCH_OBJ);
			return $posts;
			exit;
			break;
			case 'raflarim':
			$posts = $this->con->query("SELECT * FROM `customer_shelfs` WHERE customer_id=$userId  ORDER BY id DESC")->fetchAll(PDO::FETCH_OBJ);
			return $posts;
			exit;
			break;
			case 'fav':
			$posts = $this->con->query("SELECT customer_books.id, books.img, books.name, books.author, books.publisher,customer_books.favorite,customer_books.rate FROM customer_books INNER JOIN books ON customer_books.book_id = books.id WHERE customer_books.customer_id=$userId && customer_books.favorite=2  ORDER BY customer_books.rate DESC")->fetchAll(PDO::FETCH_OBJ);
			return $posts;
			exit;
			break;
			case 'databaseBooks':
			$posts = $this->con->query("SELECT id,name,author,publisher,isbn,img FROM books")->fetchAll(PDO::FETCH_OBJ);
			return $posts;
			exit;
			break;
			case 'profilInfo':
			$posts = $this->con->query("SELECT img,name FROM customer WHERE id=$userId")->fetchAll(PDO::FETCH_OBJ);
			return $posts;
			exit;
			break;
			default:
			$posts = $this->con->query("SELECT customer_books.id, books.img, books.name, books.author, books.publisher, customer_books.statu, customer_books.rate  FROM customer_books INNER JOIN books ON customer_books.book_id = books.id WHERE customer_books.customer_id=$userId")->fetchAll(PDO::FETCH_OBJ);
			return $posts;
			exit;
			break;
		}
	}
	public function removed($id,$path,$bsid)
	{
		switch ($path) {
			case 'bookremove':
			$bookid= $this->con->query("SELECT * FROM customer_books WHERE id=".$id)->fetchAll(PDO::FETCH_OBJ);
			if ($bookid[0]->book_id>12212) {
				$removesql = $this->con->prepare("DELETE FROM books WHERE id=:bookid");				
				$remove = $removesql->execute(array('bookid' => $bookid[0]->book_id));
			}else{
				$removesql = $this->con->prepare("DELETE FROM customer_books WHERE id=:id");
				$remove = $removesql->execute(array('id' => $id));
			}
			break;

			case 'shelfremove':
			$removesql = $this->con->prepare("DELETE FROM customer_shelfs WHERE id=:id");
			$remove = $removesql->execute(array('id' => $id));
			break;

			case 'bookremoveinshelf':
			$removesql = $this->con->prepare("DELETE FROM book_shelf WHERE customer_books_id=:cbid AND customerShelf_id=:bsid");
			$remove = $removesql->execute(array('cbid' => $id, 'bsid' =>$bsid));
			break;

			default:
			throw new Exception("Error Processing Request", 1);
			break;
		}
	}
	public function addBook($bookId,$userId)
	{
		$addcbooks = $this->con->prepare("INSERT INTO customer_books(customer_id, book_id) VALUES (?,?)");
		$isAddedCbooks = $addcbooks->execute(array($userId, $bookId));
	}

	public function getShelfBooks($id){

		$books = $this->con->query("SELECT books.img,books.name,books.author,books.publisher,customer_books.id,book_shelf.customerShelf_id FROM customer_books INNER JOIN books ON customer_books.book_id = books.id INNER JOIN book_shelf ON customer_books.id = book_shelf.customer_books_id WHERE book_shelf.customerShelf_id = $id")->fetchAll(PDO::FETCH_OBJ);
		return $books;
	}
	public function newuser($id,$img,$name,$email,$password){
		if ($password==null){
			$addcustomer = $this->con->prepare("INSERT INTO customer(id,img, name,email) VALUES (?,?,?,?)");
			$isAddedCustomer = $addcustomer->execute(array($id,$img,$name,$email));
		}else{
			$addcustomer = $this->con->prepare("INSERT INTO customer(img,name,email,password) VALUES (?,?,?,?)");
			$isAddedCustomer = $addcustomer->execute(array($img,$name,$email,$password));
			if ($isAddedCustomer) {
				echo "okay";
			}else{
				throw new Exception("Error Processing Request", 1);
			}
		}
	}

	public static function find($id,$userId){
		$selfObj = new self;
		$selfObj->initById($id,$userId);
		return $selfObj;
	}

	public static function getShelfInfo($id){
		$selfObj = new self;
		return $selfObj->getShelfBooks($id);
	}

	public static function addToShelf($data,$id){
		$selfObj = new self;
		return $selfObj-> adding($data,$id);
	}

	public static function get($path,$userId){
		$selfObj = new self;
		return $selfObj->getPosts($path,$userId);
	}

	public static function choice($info,$email,$userId){
		$selfObj = new self;
		return $selfObj->getinfo($info, $email, $userId);
	}
	public static function remove($id,$path,$bsid){
		$selfObj = new self;
		return $selfObj->removed($id,$path,$bsid);
	}
	public static function insertBook($bookId,$userId){
		$selfObj = new self;
		return $selfObj->addBook($bookId,$userId);
	}
	public static function adduserFb($id,$img,$name,$email,$password){
		$selfObj = new self;
		$info=$selfObj->newuser($id,$img,$name,$email,$password);
	}
	public static function adduser($img,$name,$email,$password){
		$selfObj = new self;
		$info=$selfObj->newuser("0",$img,$name,$email,$password);
	}
}