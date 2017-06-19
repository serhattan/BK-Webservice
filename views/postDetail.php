
<img src="<?=$post->image;?>" alt="Resim olmadı">
<h1><?=$post->name?></h1>
<p><?=$post->publisher?></p>
<p><?=$post->author;?></p>
<select id="favorite">
	<option value="1" <?if($post->favorite=="1") echo"selected"?>>&#9825</option>
	<option value="2" <?if($post->favorite=="2") echo"selected"?>>&#10084</option>
</select>
<select id="rate">
	<option value="1" <?if($post->rate=="1") echo"selected"?>>&#9733</option>
	<option value="2" <?if($post->rate=="2") echo"selected"?>>&#9733&#9733</option>
	<option value="3" <?if($post->rate=="3") echo"selected"?>>&#9733&#9733&#9733</option>
	<option value="4" <?if($post->rate=="4") echo"selected"?>>&#9733&#9733&#9733&#9733</option>
	<option value="5" <?if($post->rate=="5") echo"selected"?>>&#9733&#9733&#9733&#9733&#9733</option>
</select>
<select id="statu">
	<option value="1" <?if($post->statu=="1") echo"selected"?>>Okudum</option>
	<option value="2" <?if($post->statu=="2") echo"selected"?>>Okuyorum</option>
	<option value="3" <?if($post->statu=="3") echo"selected"?>>Okuyacağım</option>
</select>
<p><?=$post->subtitle;?></p>
<hr>
<small><a href="?a=editpost&id=$post->id?>">Bu içeriği düzenle</a></small>
<hr>
<a href="./?a=kitaplarim">Ana Sayfaya Dön</a>