<?php
    echo '<br><a href="logout.php">User logout</a><br>';
    if(!isset($_COOKIE['userid']))
        die("Please login first.");
    $id = $_COOKIE['userid'];
    
    define("IN_CODE",1);
    include "dbconfig.php";
 
    $con = mysqli_connect($host, $username, $password, $dbname) //connection handler
      or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_error());
      $size = count($_POST["cid"]);
      for($i=0; $i<count($_POST['cid']); $i++){
      $cid[$i] = $_POST['cid'][$i]; //for customer id
      $sid[$i] = $_POST['sid'][$i]; //for sources?
      $mid[$i] = $_POST['mid'][$i]; //primary on money table
      $note[$i] = $_POST['note'][$i];  //note
      $delete[$i] = isset($_POST['cdelete'][$i]);
    }

    $i = 0;
    $deleted = 0;
    $update = 0;
    while($i < $size){
      if($delete[$i]==1){
        $sql="delete from CPS3740_2022F.Money_waldrojo where mid = '$mid[$i]'";
        $result = mysqli_query($con, $sql);
        if($result)
        echo"<br>Sucessfully delete transaction code: $sql";
        $deleted++;

      }
      else{
      $sql="select note from CPS3740_2022F.Money_waldrojo where sid = '$sid[$i]' and note = '$note[$i]'";
      $result = mysqli_query($con, $sql);
      $samenote = FALSE;
      if($result){
        if(mysqli_num_rows($result)>0){
          $row = mysqli_fetch_array($result);
          $temp = $row['note'];
          if($temp == $note[$i]){
            $samenote = TRUE;
          }
        }
      }
      if(!$samenote){
      $sql="update CPS3740_2022F.Money_waldrojo set note='$note[$i]', mydatetime = now() where mid = $mid[$i] and note !='$note[$i]'";
      if($con->query($sql) === TRUE){
        echo "<br>Sucessfully updated transaction code: $sql";
        $update ++;
      }
    }
  }
      $i++;
      }

    
    
    echo "<br>Finish deleting $deleted records and updating $update transactions";

mysqli_close($con);
?>