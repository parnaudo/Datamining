<?php
include("../lib/init.php");
$dataminer=new dataMiner;
$paperInfo=$dataminer->efetch(22808958);
//var_dump($paperInfo);
foreach($paperInfo['meshTerms']->MeshHeading as $meshText){
	 $pieces[]=$meshText[0]->DescriptorName;	
}
$testMesh=implode('; ',$pieces);
echo $testMesh;
//var_dump($pieces);
?>