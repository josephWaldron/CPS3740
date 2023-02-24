<?php
if(isset($_POST['username']))
$user=$_POST['username'];
else
die("Please enter username first!");

if(isset($_POST['password'])){
$pass=$_POST['password'];
}
else
die("Please enter password first!");

echo '<br><a href="logout.php">User logout</a>';

echo '<br>';
$ip =$_SERVER['REMOTE_ADDR'];
echo "<br>Your IP: $ip";
$IPv4=explode(".",$ip);     //check if your from kean using ip
if($IPv4[0]==10)
    echo "<br>You are from Kean University wifi domain.\n";
else
    echo "<br>You are not from Kean University wifi domain.\n";
//add browser info
$info = $_SERVER['HTTP_USER_AGENT'];
echo "<br>Your browser and OS: $info";


define("IN_CODE",1);
include "dbconfig.php";
 
$con = mysqli_connect($host, $username, $password, $dbname) //connection handler
      or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_error());
 

$sql="select * from CPS3740.Customers where login = '$user'";
$result = mysqli_query($con, $sql); //this runs the query on the database
 
//echo "<br>SQL: $sql\n";  //show this only for debugging
 
if ($result) {
	if (mysqli_num_rows($result)>0) {
        $row = mysqli_fetch_array($result);
        $user_password =$row['password'];
        if($user_password == $pass){
            $id = $row['id'];
            $name=$row['name'];
            setcookie('userid', $row['id'], time()+600);      //create a cookie
            echo "<br>Welcome customer: <b>$name</b>\n";
            $today = date('Y-m-d');
            $age =  date_diff(date_create($row['DOB']), date_create($today));
            echo "<br>";
            echo 'Age is '.$age->format('%y');
            $street = $row['street'];
            $city = $row['city'];
            $zip = $row['zipcode'];
            echo "<br>Address: $street, $city, $zip";
            $img = $row['img'];
            echo "<br><img src='data:image/jpeg;base64,". base64_encode($img) . "'/>\n";
            $sql="select count(mid) as x from CPS3740_2022F.Money_waldrojo where cid = $id";
            $result = mysqli_query($con, $sql);
            if($result){
                if(mysqli_num_rows($result)>0){
                    $row = mysqli_fetch_array($result);
                    $transaction = $row['x'];
                }
                echo "<br>There are <b>$transaction</b> transactions for customer <b>$name</b>:";
                $sql="select mid,code,concat(case when type = 'W' then 'Withdraw' when type = 'D' then 'Deposit' end) as type,amount, sid, mydatetime, note from CPS3740_2022F.Money_waldrojo where cid = $id";
                $result = mysqli_query($con, $sql); //this runs the query on the database
                $balance = 0;
                if($result){
                    if(mysqli_num_rows($result)>0){
                        echo "<TABLE border=1>\n";
		                echo "<TR><TH>ID<TH>Code<TH>Type<TH>Amount<TH>Source<TH>Date Time<TH>Note";
                        while($row = mysqli_fetch_array($result)){
                            $mid = $row['mid'];
                            $code = $row['code'];
                            $type = $row['type'];
                            $amount = $row['amount'];
                            $sid = $row['sid'];
                            //get source
                            $sql="select name from CPS3740.Sources where id = $sid";
                            $resultSource = mysqli_query($con, $sql);
                            $rowSource = mysqli_fetch_array($resultSource);
                            $source = $rowSource['name'];
                            $datetime = $row['mydatetime'];
                            $note = $row['note'];
                            if ($type =="Withdraw"){
	        	                $color="red";
                                $negative = TRUE;
                                $balance -= $amount;
                            }
	                        else{
	        	                $color="blue";
                                $negative = FALSE;
                                $balance += $amount;
                            }
                            if($negative){
                                echo "<TR><TD>$mid<TD>$code<TD><font color='$color'>$type</font><TD>-$amount<TD>$source<TD>$datetime<TD>$note\n";
                            }
                            else
                            echo "<TR><TD>$mid<TD>$code<TD><font color='$color'>$type</font><TD>$amount<TD>$source<TD>$datetime<TD>$note\n";
                        }   
                        echo "</TABLE>\n";
                    }
                    
                }
                if($balance > 0)
                    $color = "blue";
                else
                $color = "red";
                echo "<br>Total balance: <font color='$color'>$balance</font>\n";
            }
            else{
                echo "<br>There are no transactions for user: $name";
            }
            //button for adding transaction

            
            echo "<form method='post' action='add_transaction.php'>";
            echo "<input type='hidden' name='id' value='$id'>";
            echo "<button type='submit'>Add Transaction</button>";
            echo "</form>";
            echo "<TD><a href='display_transaction.php'>Display and update transaction</a>";
            echo "<br><a href='display_stores.php' target=_blank>Display stores</a>";
            echo "<TR><TD colspan=2><form action='search_transaction.php' method='get'>";
            echo "Keyword: <input type='text' name='keywords'  required='required'>";
            echo "<input type='submit' value='Search transaction'></form>";
            }
        else{
            echo"<font color='red'>";
            echo "<br>Login $user exists, but password does not match.\n";
        }
        mysqli_free_result($result);
    }
    else{
        echo"<font color='red'>";
        echo "<br>Login $user doesn't exist in te database.\n";
    }
    }
else {
    echo "Something is wrong with SQL:" . mysqli_error($con);	
   }

   mysqli_close($con);
?>