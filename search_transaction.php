<?php
    echo '<br><a href="logout.php">User logout</a><br>';
    if(!isset($_COOKIE['userid']))
        die("Please login first.");
    $id = $_COOKIE['userid'];
    $input = $_GET['keywords'];
    define("IN_CODE",1);
    include "dbconfig.php";
 
    $con = mysqli_connect($host, $username, $password, $dbname) //connection handler
      or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_error());
 
    $sql="select name from CPS3740.Customers where id = '$id'";
    $result = mysqli_query($con, $sql); //this runs the query on the database
    if($result){
        if(mysqli_num_rows($result)>0){
            $row = mysqli_fetch_array($result);
            $name = $row['name'];
        }
    }
    if($input == '*'){
        $sql="select count(mid) as x from CPS3740_2022F.Money_waldrojo where cid = $id";
        $result = mysqli_query($con, $sql); //this runs the query on the database
    }
    else{
        $sql="select count(mid) as x from CPS3740_2022F.Money_waldrojo where cid = $id AND note like '%$input%'";
        $result = mysqli_query($con, $sql); //this runs the query on the database
    }
    if($result){
        if(mysqli_num_rows($result)>0){
            $row = mysqli_fetch_array($result);
            $transaction = $row['x'];
        }
        if(intval($transaction) == 0)
            die ("There are no transactions for user: $name with keyword $input");
        echo "<br>There are <b>$transaction</b> transactions for customer <b>$name</b> with the keyword $input:";
        if($input == '*'){
        $sql="select mid,code,concat(case when type = 'W' then 'Withdraw' when type = 'D' then 'Deposit' end) as type,amount, sid, mydatetime, note from CPS3740_2022F.Money_waldrojo where cid = $id";
        $result = mysqli_query($con, $sql); //this runs the query on the database
        }
        else{
            $sql="select mid,code,concat(case when type = 'W' then 'Withdraw' when type = 'D' then 'Deposit' end) as type,amount, sid, mydatetime, note from CPS3740_2022F.Money_waldrojo where cid = $id AND note like '%$input%'";
            $result = mysqli_query($con, $sql); //this runs the query on the database
        }
        $balance = 0;
        if($result){
            if(mysqli_num_rows($result)>0){
                echo "<TABLE border=1>\n";
                echo "<TR><TH>ID<TH>Code<TH>Type<TH>Amount<TH>Date Time<TH>Note<TH>Source";
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
                        echo "<TR><TD>$mid<TD>$code<TD><font color='$color'>$type</font><TD>-$amount<TD>$datetime<TD>$note<TD>$source\n";
                    }
                    else
                    echo "<TR><TD>$mid<TD>$code<TD><font color='$color'>$type</font><TD>$amount<TD>$datetime<TD>$note<TD>$source\n";
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
    
    mysqli_free_result($result);
    mysqli_close($con);
?>