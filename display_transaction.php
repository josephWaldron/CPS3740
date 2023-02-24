<?php
    echo '<br><a href="logout.php">User logout</a><br>';
    if(!isset($_COOKIE['userid']))
        die("Please login first.");
    $id = $_COOKIE['userid'];
    
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
                    echo "<br>You can only update <b>Note</b> column.<br>";
                    echo "<form action='update_transaction.php' method='post'>";
              
                  echo "<TABLE border=1>\n";
                  echo "<TR><TH>ID<TH>Code<TH>Type<TH>Amount<TH>Source<TH>Date Time<TH>Note<TH>Delete";
                  $i = 0;
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
                            echo "<input type='hidden' name='cid[$i]' value='$id'><input type='hidden' name='sid[$i]' value='$sid'><input type='hidden' name='mid[$i]' value='$mid'>";
                          echo "<TR><TD>$mid<TD>$code<TD><font color='$color'>$type</font><TD>-$amount<TD>$source<TD>$datetime<td bgcolor='yellow'><input type='text' value='$note' name=note[$i] style='background-color:yellow;'><TD><input type='checkbox' name='cdelete[$i]' value='0'>";
                          $i++;
                      }
                      else{
                        echo "<input type='hidden' name='cid[$i]' value='$id'><input type='hidden' name='sid[$i]' value='$sid'><input type='hidden' name='mid[$i]' value='$mid'>";
                      echo "<TR><TD>$mid<TD>$code<TD><font color='$color'>$type</font><TD>$amount<TD>$source<TD>$datetime<td bgcolor='yellow'><input type='text' value='$note' name=note[$i] style='background-color:yellow;'><TD><input type='checkbox' name='cdelete[$i]' value = '0'>";
                     $i++; 
                    }
                  }   
                  echo "</TABLE>\n";
              }
              
          }
          if($balance > 0)
              $color = "blue";
          else
          $color = "red";

          echo "<br>Total balance: <font color='$color'>$balance</font>\n";
          echo "<br><input type='submit' value='Update Transaction' /></td>";
          echo "</form>";
      }
      else{
          echo "<br>There are no transactions for user: $name";
      }
      mysqli_free_result($result);
      mysqli_close($con);

?>