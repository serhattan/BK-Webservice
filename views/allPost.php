<?php
var_dump($postsOnPage[0]->name);
foreach ($postsOnPage as $post): ?>
<img src="<?=$post->img?>" alt="Resim olmadı">
<pre>
<a href="?a=detail&id=<?=$post->id;?>">More Info</a>
<h1><?=$post->name?></h1>
<h3><?=$post->author?></h3>
<h3><?=$post->publisher?></h3>
<hr>
<?php endforeach; ?>
<a href="?a=newpost">+++ Yeni İçerik</a>