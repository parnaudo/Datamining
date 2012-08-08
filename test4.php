<?php


include("lib/init.php");
$Start = getTime();
$table="RheumatologyHCPs";
deduplicateAuthorsTEST($table);
//1262038
function deduplicateAuthorsTEST($table){
        $atomQuery="select atomId from authors where atomId!=0 group by atomId having count(id) > 1 order by atomId ASC";
        $result=mysql_query($atomQuery);
        while($row=mysql_fetch_array($result)){
                $authorQuery="SELECT * from authors where atomId=".$row['atomId'];
                $authorResult=mysql_query($authorQuery);
                $infoQuery="SELECT * from $table where atomId=".$row['atomId'];
                $infoResult=mysql_query($infoQuery);
                $infoRow=mysql_fetch_array($infoResult);
                echo $infoRow['lastName']." ".$infoRow['firstName']." ".$infoRow['middleName']. "<BR>";
                $lengthTest=0;
                $keepId=0;
                $deleteId=0;
                $rejectArray=array();
                $testArray=array();
                $nameArray=array();
                while($authorRow=mysql_fetch_array($authorResult)){
                        echo "<BR>";
                        $nameTest='';
                        $match=0;
                        $middleInitial=substr($infoRow['middleName'],0,1);
                        $firstInitial=substr($infoRow['firstName'],0,1);
                        $initialTest=$firstInitial." ".$middleInitial;
                        
                        $test=strlen($authorRow['foreName']);
                        $nameTest=str_replace('.','',$infoRow['firstName']." ".$infoRow['middleName']);
                        echo $authorRow['foreName']." VS nametest: ".$nameTest;
                        if(stripos($nameTest,$authorRow['foreName'])!==FALSE){
                                echo " MATCH<BR>";
                                $match=1;
                                
                        }
                        elseif(stripos($initialTest,$authorRow['foreName'])!==FALSE){
                                echo " MATCH 2<BR>";
                                $match=1;
                        }
                        else{
                                $rejectArray[$authorRow['id']]=$authorRow['foreName']; 
                                $updateAuthor="UPDATE authors set duplicateFlag=0, atomId=0 where id=".$authorRow['id'];
                                $updateInstance="UPDATE coAuthorInstance SET query='' where coAuthor=".$authorRow['id'] ;
                               mysql_query($updateAuthor);
                               mysql_query($updateInstance);
                                echo $updateAuthor;
                                echo $updateInstance;
                        }
                        if($match==1){        
                                $test=strlen($authorRow['foreName']);
                                $testArray[$authorRow['id']]=$test;
                                $nameArray[$authorRow['id']]=$authorRow['foreName'];       
                        }
                        
        
                }
        
                 if(sizeof($testArray)==0){
                        foreach($rejectArray as $key=>$value){
                                if(stripos($value,$infoRow['firstName'])!==FALSE){
                                        $updateAuthor="UPDATE authors set atomId=".$infoRow['atomId']." where id=".$key;
                                        $updateInstance="UPDATE coAuthorInstance SET query='".$value."' where coAuthor=".$authorRow['id'] ;
                                        echo $updateAuthor." BREAK<BR>";
                                        echo $updateInstance;
                                        mysql_query($updateAuthor);
                                       mysql_query($updateInstance);
                                        break;
                                }
                        }
                  }
                  else{
                 //if we don't have a middle name, why not see if we can find one?
                                if($infoRow['middleName']==''){
                                        ECHO "NONEAT";
                                        $middleNameArray=array();
                                        foreach($rejectArray as $key=>$value){
                                                $value=trim($value);
                                                echo ".$value.";
                                                $length=strlen($value);
                                                $cutoff=strpos($value,' ');
                                                if($cutoff==FALSE){
                                                        $middle='';
                                                }
                                                else{
                                                        $middle=substr($value,$cutoff,$length);
                                                        $middleNameArray[]=$middle;
                                                }   
                                        }
                                        echo "MIDDLE NAME ARRAY: ";
                                        var_dump($middleNameArray);
                                        if(sizeof(array_unique($middleNameArray))==1){
                                                //make sure names are the same
                                                $middleMax = array_search(max($rejectArray), $rejectArray);
                                                echo $middleMax;
                                                $cutoff=strpos($rejectArray[$middleMax],' ');
                                                $testString=substr($rejectArray[$middleMax],0,$cutoff);
                                                if(in_array($testString,$nameArray)!==FALSE){
                                                        foreach($rejectArray as $key=>$value){
                                                            $test=strlen($value);
                                                            $testArray[$key]=$test;
                                                            $nameArray[$key]=$value;
                                                        }
                                                }
                                                else{
                                                        echo "NOT OK";
                                                }
                                                var_dump($rejectArray);
                                                var_dump($nameArray);

                                                 var_dump($nameArray);
                                                unset($rejectArray);
                                        }
                                }

                  
                        $maxIndex = array_search(max($testArray), $testArray);
                                if(sizeof($nameArray)==1){
                                        $updateAuthor="UPDATE authors set duplicateFlag=0 where id=".$maxIndex;
                                        mysql_query($updateAuthor); 
                                        echo $updateAuthor;
                                        
                                }
                                foreach($nameArray as $key=>$value){
                                        if($key!==$maxIndex){
                                        
                                                $updateInstance="UPDATE coAuthorInstance SET coAuthor=".$maxIndex.", query='".$nameArray[$maxIndex]."' where coAuthor=".$key;
                                                $updateAuthor="UPDATE authors set duplicateFlag=0 where id=".$maxIndex;
                                                $deleteAuthor="DELETE FROM authors where id=".$key ;
                                           echo $updateInstance."<BR>";
                                           echo $updateAuthor."<BR>";
                                           echo $deleteAuthor."<BR>";
                                             mysql_query($updateAuthor);  
                                             mysql_query($updateInstance);
                                             mysql_query($deleteAuthor);
                                        }
                                }
                        
                  }
                /*
                if(hasDuplicates($testArray)==TRUE){
                        echo "DUPLICATES";
                        print_r($nameArray);
                        foreach($testArray as $key=>$value){
                                $updateAuthor="UPDATE authors set duplicateFlag=1 where id=".$key;
                                echo $updateAuthor;
                                mysql_query($updateAuthor);
                        }
                }
                else{
                        foreach($nameArray as $key=>$value){
                                if($key!==$maxIndex){
                                        $updateInstance="UPDATE coAuthorInstance SET coAuthor=".$maxIndex.", query='".$nameArray[$maxIndex]."' where coAuthor=".$key;
                                        $deleteAuthor="DELETE FROM authors where id=".$key ;
                                        mysql_query($updateInstance);
                                        mysql_query($deleteAuthor);
                                }
                        }
                }
                */

        }
}
//clearTable('edgeCache');
echo "TESTING";
?>
