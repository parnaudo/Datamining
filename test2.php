<?php 
include("lib/init.php");
$Start = getTime(); 
$table="education";
$attribute=array('residency','fellowship','medschool');

$words  = array();
$getEducation="SELECT distinct name,count(id)from education where years !='' group by name having count(id) > 1  order by count(id) desc,name, years";
$educationResult=mysql_query($getEducation);
$educationRow=mysql_fetch_array($educationResult);
while($educationRow=mysql_fetch_array($educationResult)){
	$break=strpos($educationRow['name'],' ');
	$words[]=substr($educationRow['name'],0,$break);
}
/*
$getEducation="SELECT distinct name,count(id)from education where years !='' and name like '%johns%' group by name having count(id) > 1  order by count(id) desc,name, years";
$educationResult=mysql_query($getEducation);
while($educationRow=mysql_fetch_array($educationResult)){
	$input=substr($educationRow['name'],0,15);
	fuzzySearch($input,$words);
}
*/
var_dump($words);
foreach($words as $word){
$getEducation="SELECT distinct name,count(id)from education where years !='' and name like '%".$word."%' group by name having count(id) > 1  order by count(id) desc,name, years";
echo $getEducation."<BR>";
$educationResult=mysql_query($getEducation);
while($educationRow=mysql_fetch_array($educationResult)){
	$input=substr($educationRow['name'],0,15);
	//fuzzySearch($input,$words);
}


}
// array of words to check against

function fuzzySearch($input,$words){
	// no shortest distance found, yet
	$shortest = -1;
	
	// loop through words to find the closest
	foreach ($words as $word) {
	
	    // calculate the distance between the input word,
	    // and the current word
	    $lev = levenshtein($input, $word);
	
	    // check for an exact match
	    if ($lev == 0) {
	
	        // closest word is this one (exact match)
	        $closest = $word;
	        $shortest = 0;
	
	        // break out of the loop; we've found an exact match
	        break;
	    }
	
	    // if this distance is less than the next found shortest
	    // distance, OR if a next shortest word has not yet been found
	    if ($lev <= $shortest || $shortest < 0) {
	        // set the closest match, and shortest distance
	        $closest  = $word;
	        $shortest = $lev;
	    }
	}
	
	echo "Input word: $input\n";
	if ($shortest == 0) {
	    echo "Exact match found: $closest\n";
	} else {
	    echo "Did you mean: $closest? $shortest\n";
	}
	echo "<BR>";
}
$End = getTime(); 
echo "Time taken = ".number_format(($End - $Start),2)." secs";

?>