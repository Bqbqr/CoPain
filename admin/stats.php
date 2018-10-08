<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Bqbqr">

    <title>Commande Pain</title>

    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/bootstrap-theme.min.css" rel="stylesheet">


    <script src="../js/jquery-1.11.2.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="http://code.highcharts.com/highcharts.js"></script>
    <script src="http://code.highcharts.com/modules/exporting.js"></script>
    <script type="text/javascript">

    $(document).ready(function() {       
    });


        </script>
  </head>

<?php
// Connection MySQL 
include('../secure/config.php');
$bdd=mysqli_connect($SQLhost, $SQLlogin, $SQLpass, $SQLdb) or die(mysql_error());
$result= mysqli_query($bdd,"SELECT MONTH(date) as month,nom,SUM(quantity) as quantity FROM orders as o INNER JOIN ordercontent as oc ON o.id=oc.numorder INNER JOIN article as a ON oc.article=a.id GROUP BY MONTH(date),nom;") or die(mysqli_error($bdd));

while ($row = mysqli_fetch_array($result)) {
   $data[$row['nom']][$row['month']] = $row['quantity'];
   $date[]=$row['month'];
}
$date= array_unique($date, SORT_REGULAR);
?>



  <body role="document">
    <?php
      include('header.php');
    ?>

    <div class="container theme-showcase" role="main">
      <div id="graph" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    </div> <!-- /container -->
    <script type="text/javascript">
      var chart = new Highcharts.Chart({
            chart: {
              type: 'column',
              renderTo: 'graph'
            },
            yAxis: {
                title: {
                    text: 'Quantité'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            xAxis:{
              categories:[
                <?php
                foreach($date as $value){
                  echo "'$value',";
                }
              ?>
              ]
            },
            series: [
            <?php
              foreach($data as $key => $value){
                echo "{\nname:'$key',\ndata:[";
                foreach($date as $tmpdate){
                  if(!array_key_exists($tmpdate, $value))
                    echo "0,";
                  else
                    echo "$value[$tmpdate],";
                }
                echo "],\n},";
              }
              ?>
            ]
      });
    </script>

  </body>
</html>