
<!DOCTYPE html><html lang="en">
  <head>
        <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link href="https://i-share.carli.illinois.edu/vf-eiu/themes/bootprint3/css/compiled.css?_=1511385145" media="all" rel="stylesheet" type="text/css">
<link href="https://i-share.carli.illinois.edu/vf-eiu/themes/bootstrap3/css/print.css?_=1507749433" media="print" rel="stylesheet" type="text/css">
<link href="https://i-share.carli.illinois.edu/vf-eiu/themes/carli/css/carli.css?_=1507749433" media="all" rel="stylesheet" type="text/css">
<link href="https://i-share.carli.illinois.edu/vf-eiu/themes/eiu/css/institution.css?_=1507749433" media="all" rel="stylesheet" type="text/css">
<link href="https://i-share.carli.illinois.edu/vf-eiu/themes/carli/images/carli-favicon.ico" rel="shortcut icon" type="image/x-icon">


<style type="text/css">


.save-Record {visibility:hidden;}
.savedlists {visibility:hidden;}
.checkbox {visibility:hidden;}
.Record-checkbox {visibility:hidden;}
.checkbox-select-item {visibility:hidden;}
.callnumber {visibility:hidden;}
.location {visibility:hidden;}
.hideifdetailed {visibility:hidden;}
.Book {visibility:hidden; }
.fulltext {padding-right: 5px;}
.openurlcontrols {visibility:hidden; }
.Record-number {visibility:hidden; }

.Hidden-print {width: 0; height: 0;}

.Electronic {float: left; margin-left: 5px;}
.Movie {float: left; padding-left: 5px;}
.fa { 
	font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
	font-size: 13px;
	line-height: 1.42857143;
	color: #333;
}

.media-body {width: 500px;}
.result {width: 500px;}

.media-left {width: 150px;
}

.Textualmaterial {visibility:hidden; }


<?php foreach (range(0, 100) as $number) {
    echo "#result". $number ." {background-color: #FFF;}";}
?>




</style>

</head>

<body>


<?php

/*this is designed to work with CARLI's VuFind 4.1 implementation. The style above are necessary to hide certain elements. This would have been more elegant written to an array, but I'm lazy and bad with arrays.*/



//function to make pretty capital letters and take out space before :. totally stolen from the web.
function to_title_case( $rawtitle ) {
     /* Words that should be entirely lower-case */
     $articles_conjunctions_prepositions = array(
          'a','an','the',
          'and','but','or','nor',
          'if','then','else','when',
          'at','by','from','for','in',
          'off','on','out','over','to','into','with'
     );
     /* Words that should be entirely upper-case (need to be lower-case in this list!) I do health and technology. your acronyms may vary. */
     $acronyms_and_such = array(
         'asap', 'unhcr', 'wpse', 'ptsd', 'adhd', 'html', 'mri', 'sql', 'php', 'copd', 'fbi', 'fda', 'er', 'brca', 'cvS'
     );
    
	 //take out space after colon
$rawtitle=str_replace(' : ', ': ', $rawtitle);
	$rawtitle=str_replace(' / ', '', $rawtitle); 
	
	  /* split title string into array of words */
     $words = explode( ' ', mb_strtolower( $rawtitle ) );
     /* iterate over words */
     foreach ( $words as $position => $word ) {
         /* re-capitalize acronyms */
         if( in_array( $word, $acronyms_and_such ) ) {
             $words[$position] = mb_strtoupper( $word );
         /* capitalize first letter of all other words, if... */
         } elseif (
             /* ...first word of the title string... */
             0 === $position ||
             /* ...or not in above lower-case list*/
             ! in_array( $word, $articles_conjunctions_prepositions ) 
         ) {
             $words[$position] = ucwords( $word );
         }
     }         
     /* re-combine word array */
     $rawtitle = implode( ' ', $words );
     /* return title string in title case */
	 
	 //capitalize first word ofter colon
     $words = explode( ': ', $rawtitle);
	 foreach ( $words as $position => $word )
	{$words[$position] = ucfirst( $word );}
	$rawtitle = implode( ': ', $words );
	
	
	 

	return $rawtitle;
	
}


//the tag to look up is set in the URL. 
//$url="https://i-share.carli.illinois.edu/vf-eiu/Tag/Home?lookfor=eiu_health_careers";


if (isset($_GET['tag']))
{$tag=$_GET['tag'];}


$url="https://i-share.carli.illinois.edu/vf-eiu/Tag/Home?lookfor=" . $tag;



$dom = new DOMDocument();

$dom->loadHTMLFile($url);

$xpath = new DOMXPath($dom);



$divs = $xpath->query('//div[@class="result ajaxItem"] ');

foreach ($divs as $div) {
   

   
   $string= $dom->saveXML($div);
   
//remove line breaks because it makes the replacements easier later   
   $string=preg_replace("/\r|\n/", "", $string );
   
   //remove whitespace
    $string=preg_replace("/\s+/", " ", $string );
	
	//add the begining of the URLs since we are not on CARLI's server

$string=preg_replace("/\<i class.*?\<\/i>/", "", $string);


//$string=preg_replace("/\<script =Type=\"text\/javascript\" Src=\"\/vf-eiu\/themes\/bootstrap3\/js\/openurl.js?_\= /", " ", $string);

//$string=preg_replace("/\<script type=\"text\/javascript\" src=\"\/vf\-eiu\/themes\/bootstrap3\/js\/openurl\.js\?_\=\d{10}\"\/\>/", " ", $string);


   $string=str_replace("/vf-eiu/Cover/", "https://i-share.carli.illinois.edu/vf-eiu/Cover/", $string);
   $string=str_replace("/vf-eiu/Author/", "https://i-share.carli.illinois.edu/vf-eiu/Author/", $string);
    $string=str_replace("/vf-eiu/Record/", "https://i-share.carli.illinois.edu/vf-eiu/Record/", $string);
	
	//set cover image to a consistant width

	 $string=str_replace("alt=\"Cover Image\"", "alt=\"cover image\" width=\"100px\"", $string);
 
 
 //add a space after tag close. This makes the capitalizitaion function work later.
   $string=str_replace("\">", "\"> ", $string);
   
  
   
   //capialize and remove slashes
       $string=to_title_case($string);

//fix ampersands

  $string=str_replace("&amp;", "&", $string);
	   
//we trashed the case, so fix that sonlinks work again	   
 $string=str_replace("eiudb", "EIUdb", $string);

  $string=str_replace("record", "Record", $string);
 
 
  $string=str_replace("author", "Author", $string);

 
     $string=str_replace("Recordcover", "recordcover", $string);
	 
	$string=str_replace("/Author/home?Author", "/Author/Home?author", $string); 
	
	
	 //hide this since it's empty
	 
	   $string=str_replace("<strong>located:</strong>", "", $string);
	 
	//capitalize first letters after tags

	 $string=preg_replace_callback('/"full">  ./',  function($match) {return strtoupper($match[0]);}, $string);
	 
	 $string=preg_replace_callback('/"true"\/> . /',  function($match) {return strtoupper($match[0]);}, $string);
	 
	//open links in new window
	 $string=str_replace("Data-view=\"FULL\">", "Data-view=\"FULL\" target=\"_blank\">", $string);

$string=str_replace("%2c\">", "%2c\" target=\"_blank\">", $string);

$string=str_replace(".\">", ".\" target=\"_blank\">", $string);


$string=str_replace("Contributor Biographical Information", "", $string);

$string=str_replace("Textual Material", "", $string);


$string=str_replace("Address at Time Of Purl Creation", "", $string);

$string=str_replace("Eiu", "EIU", $string);


//put line breaks back in 
   
   $string=str_replace(">", ">\n", $string);
   
   
//hide the diacritics because we broke them   
   $string = iconv("UTF-8", "UTF-8//IGNORE", $string);
  
   
   echo "</div>";
  
   echo $string;
   
}
   

?>

</body>



</html>



