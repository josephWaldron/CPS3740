<?php
echo '<br><a href="logout.php">Logout</a><br>';
if(!isset($_COOKIE['userid']))
	die("Please login first.");
$id = $_COOKIE['userid'];
define("IN_CODE",1);
include "dbconfig.php";
$con = mysqli_connect($host, $username, $password, $dbname) //connection handler
      or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_error());
 
echo '<br><font size=4><b>Add Transaction</b></font>';
$sql="select name, amount, type from CPS3740_2022F.Money_waldrojo inner join CPS3740.Customers on CPS3740_2022F.Money_waldrojo.cid = CPS3740.Customers.id where CPS3740.Customers.id = $id";
$result = mysqli_query($con, $sql); //this runs the query on the database
$balance = 0;
if($result){
      if(mysqli_num_rows($result)>0){
            while($row = mysqli_fetch_array($result)){
                  $amount = $row['amount'];
                  $name = $row['name'];
                  $type = $row['type'];
                  if ($type =='W')
                  $balance -= $amount;
                  else
                  $balance += $amount;
            }
      }
}
else {
      echo "Something is wrong with SQL:" . mysqli_error($con);	
     }
echo "<br><b>$name</b> current balance is <b>$balance</b>";
$sql="select * from CPS3740.Sources";
$result = mysqli_query($con, $sql); //this runs the query on the database
$sourceids = array();
$sourcenames = array();
if($result){
      if(mysqli_num_rows($result)>0){
            //$row = mysqli_fetch_array($result)
            $i = 0;
            while($row = mysqli_fetch_array($result)){
            $sourceids[$i] = $row['id'];
            $sourcenames[$i] = $row['name'];
            $i++;
            }
      }
}
else {
      echo "Something is wrong with SQL:" . mysqli_error($con);	
     }


echo "<br>";
echo "<form name='input' action='insert_transaction.php' method='post' required='required'>";
echo "Transaction code: <input type='text' name='code'required='required'>";
echo "<br><input type='radio' name='type' value='D'>Deposit";
echo "<input type='radio' name='type' value='W'3>Withdraw";
echo "<br> Amount: <input type='number' name='amount' required='required'><input type='hidden' name='balance' value=237.00004999999874'>";
echo "<br>Select a Source: <SELECT name='source_id'>";
echo "<option value=''></option>";
echo "<option value=$sourceids[0]>$sourcenames[0]</option>";
echo "<option value=$sourceids[1]>$sourcenames[1]</option>";
echo "<option value=$sourceids[2]>$sourcenames[2]</option>";
echo "<option value=$sourceids[3]>$sourcenames[3]</option>";
echo "<option value=$sourceids[4]>$sourcenames[4]</option>";
echo "<option value=$sourceids[5]>$sourcenames[5]</option>";
echo "</SELECT>";
echo "<BR>Note: <input type='text' name='note'>";
echo "<br>";
echo "<input type='submit' value='Submit'>";
echo "</form>";

mysqli_free_result($result);
mysqli_close($con);
?>