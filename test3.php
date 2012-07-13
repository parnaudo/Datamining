
<?php 
//include("lib/init.php");
$test=new eulerProblem;
$math=new math;
//$test->problem1();
$int=600851475143;

$test=6;
$i=0;
$number=$math->isPrime($test);


$primeFactors=array();
for($test=1;$test < sqrt($int);$test++){
	$primeTest=$math->IsPrime($test);
	if($primeTest!==0){
		//echo "LOOKING FOR $int divided by $test<br>";	
		$divisibleTest=$math->modulus($int,$test);
		if($divisibleTest==0){
			$primeFactors[]=$test;
		}
	}
	
}

$max=max($primeFactors);
echo $max;
class math{
	function modulus($numerator,$denominator){
		$test=$numerator%$denominator;
		return $test;
	}
function IsPrime($Num)
{
if((($Num%2)==0 AND $Num>2) || $Num<2) return false;

for($Divisor = 3; $Divisor <=sqrt($Num); $Divisor =2)
{ 
if(($Num%$Divisor)==0)
{
return false;
}
}
return true;
}
}
class eulerProblem{
	function problem1(){
		$sum=0;
		for($i=0;$i<1000;$i++){
			$test3=$i%3;
			$test5=$i%5;
			if($test3==0 || $test5==0){
			//echo $i;
				$sum=$sum+$i;
			
			}
			
		}
		echo $sum;
		return $sum;
	}
	function problem2(){
		$fib1=1;
		$fib2=2;
		$sumTest=0;
		$evenFib=array(); 
		while($sumTest < 4000000){
			$sumTest=$fib1+$fib2;
			if($fib1%2==0){
				if(in_array($fib1,$evenFib)){
					
				}
				else{
					$evenFib[]=$fib1;
				}
			}
			elseif($fib2%2==0){
				if(in_array($fib2,$evenFib)){
				
				}
				else{
					$evenFib[]=$fib2;
				}
			}
			else{
			
			}
			if($fib1<$fib2){
				$fib1=$sumTest;
			}
			else{
				$fib2=$sumTest;
			}	
		}
		var_dump($evenFib);
		$sum=array_sum($evenFib);
		echo $sum;
		return $sum;
	}	
}
/*
$edgeTypes="SELECT DISTINCT class FROM edge";
$edgeCount=getRowCount($edgeTypes);
certainty($edgeCount);
*/
?>