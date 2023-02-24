<?php

    echo '<br><a href="logout.php">User logout</a><br>';
    if(!isset($_COOKIE['userid']))
        die("Please login first.");
    $id = $_COOKIE['userid'];
    $transaction_code = $_POST['code'];
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
    $con = mysqli_connect($host, $username, $password, $dbname) //connection handler
      or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_error());
 
    $sql="select code from CPS3740_2022F.Money_waldrojo where code = '$transaction_code'";
    $result = mysqli_query($con, $sql); //this runs the query on the database
    //echo "<br>SQL: $sql\n";
    if($result){
        if(mysqli_num_rows($result)>0){
            die("Error! Transaction code already in database.");
        }
    }
    if(isset($_POST['type'])){
        $type = $_POST['type'];
    }
    else
    die("Please select deposit or withdraw");
    $amount = $_POST['amount'];

    if($amount < 0)
        die("Amount must be a positive number.");
    if($_POST['source_id'] != ''){
        $source = $_POST['source_id'];
    }
    else
    die("Please select source");
    if(isset($_POST['note'])){
        $note = $_POST['note'];
    }
    else
    $note = '';
    $balance = $balance;
    
    if($type == 'W' & (intval($balance) < intval($amount)))
        die("<font color='red'>Error! Balance is $balance and withdraw amount is $amount. Not enough money!");
        
    $sql="insert into CPS3740_2022F.Money_waldrojo(code, cid, sid, type, amount, mydatetime, note) values('$transaction_code', $id, $source, '$type', $amount, now(), '$note')";
    //echo "<br>SQL: $sql\n";
    $result = mysqli_query($con, $sql); //this runs the query on the database
    echo "<br>Sucessfully added the transaction";
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

mysqli_free_result($result);
mysqli_close($con);
    
?>