<?php
echo '<br><a href="logout.php">User logout</a><br>';
if(!isset($_COOKIE['userid']))
    die("Please login first.");
$id = $_COOKIE['userid'];
echo "<br><b>The following stores are in the database.</b>";

define("IN_CODE",1);
include "dbconfig.php";

$con = mysqli_connect($host, $username, $password, $dbname) //connection handler
  or die("<br>Cannot connect to DB:$dbname on $host, error: " . mysqli_connect_error());


$sql="select * from CPS3740.Stores";
$result = mysqli_query($con, $sql); //this runs the query on the database

//echo "<br>SQL: $sql\n";  //show this only for debugging
if ($result) {
    if (mysqli_num_rows($result)>0) {
        echo "<TABLE border=1>\n";
        echo "<TR><TH>ID<TH>Name<TH>Address<TH>City<TH>State<TH>Zipcode<TH>Location(Latitude,Longitude";
        while($row = mysqli_fetch_array($result)){
            $ID = $row['sid'];
            $Name = $row['Name'];
            $Address = $row['address'];
            $City = $row['city'];
            $State = $row['State'];
            $Zip = $row['Zipcode'];
            $Lat = $row['latitude'];
            $Long = $row['longitude'];
            echo "<TR><TD>$ID<TD>$Name<TD>$Address<TD>$City<TD>$State<TD>$Zip<TD>($Lat,$Long)";
        }
        echo "</TABLE>\n";
    }
    else
        echo "<br>No record found\n";
}
else 
echo "Something is wrong with SQL:" . mysqli_error($con);
mysqli_free_result($result);
mysqli_close($con);
?>