<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Bqbqr">

    <title>Commande Pain</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/bootstrap-theme.min.css" rel="stylesheet">

    <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
    <script src="js/jquery-1.11.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() { 
      var croissantptd = 0;
      var pacptd = 0;
      var bagptd = 0;
      //Test pour la askBox

      function doConfirm(msg, croissant, pac, cancel) {
          var confirmBox = $("#confirmBox");
          confirmBox.find(".message").text(msg);
          confirmBox.find(".croissantV,.pacV, .cancel").unbind().click(function () {
              confirmBox.hide();
          });
          confirmBox.find(".croissantV").click(croissant);
          confirmBox.find(".pacV").click(pac);
          confirmBox.find(".cancel").click(cancel);
          confirmBox.show();
      }

      $(document).on("click", ".petitdej", function(){
        var id=$(this).attr('id');
        var price=$(this).attr('price');
        doConfirm("Quelle Viennoiserie?", 
          function croissantV(){
            croissantptd+=1;
            bagptd++;
            addArticle(id,price);

          }, function pacV() {
            pacptd+=1;
            bagptd++;
            addArticle(id,price);
          }, function cancel() {
              //Nothing done.
          });
      });



      //Fin du test
      //Fonction d'ajout d'article
      function addArticle(id, price){
        //On récupère la table
          var table = document.getElementById("paniert");
          var row = -1;
          //On cherche dans la table si ça existe déja
          for(var i=0; i<$('#paniert tr').length;i++){
            var tmp=table.rows[i].cells[1].innerHTML;
            if (tmp.toString().indexOf(id) > '-1')
              row=i;
          }

          //Si ça existe, on incrémente
          if(row!='-1'){
            table.rows[row].cells[0].innerHTML=parseInt(table.rows[row].cells[0].innerHTML)+1;
            table.rows[row].cells[2].innerHTML=(parseFloat(table.rows[row].cells[2].innerHTML)+parseFloat(price)).toFixed(2);
          }
          else{
            //Création d'une tr en dernier (-1) ou on modifie la ligne
            var row = table.insertRow(row);

            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);

            cell1.innerHTML="1";
            cell2.innerHTML=id;
            cell3.innerHTML=price;

          }
          var sum=0.0;
          for(var i=1; i<$('#paniert tr').length;i++){
            sum+=parseFloat(table.rows[i].cells[2].innerHTML);

          }
          document.getElementById("total").value=sum.toFixed(2)+" €";
        }


      function resetPanier(){
        var table = document.getElementById("paniert");

        $.ajax({

              url: 'index.php', // Le nom du fichier indiqué dans le formulaire
              success: function(html) { // Je récupère la réponse du fichier PHP
                $("#panierh").load("index.php #panierh");
              },
              error: function(html){
                alert(html);
              }
          });
        document.getElementById("total").value="";
        document.getElementById("name").value="";
        document.getElementById("emplacement").value="";
      }

      //Clic sur reset==> Reset du panier
      $(document).on("click", "#reset", function(){
        resetPanier();
      });

      //Clic sur un article, appel de l'ajout d'article après récup des valeurs
      $(document).on("click", ".clic-article", function(){
        //Simplification: On récupère le prix et l'article, on envoie tout ça à la fonction qui se charge d'ajouter.
        var id=$(this).attr('id');
        var price=$(this).attr('price');
        addArticle(id,price);
      });


      //Validation de la commande
      $(document).on("click", "#valider", function(){
        var name=$('#name').val();
        var emplacement=$('#emplacement').val();
        var verif = "true";

        var patt = new RegExp(/^[a-zA-Z][0-9]{1,2}$/);
        var res = patt.test(emplacement);
        if(name == '' || emplacement == '') {
            alert('Les champs doivent êtres remplis');
            return;
        }
        else if(res==false){
            alert("L'emplacement doit être une lettre et un (ou deux) chiffres. Pitch need to be Letter Number");
            return;
        } 

        //Verification de l'existence du nom dans la bdd:
        /*/ TESTING ZNE
        $.ajax({
          type: "GET",
          dataType: "json",
          async: false,
          url: "alreadyhere.php?nom="+name+"&emplacement="+emplacement,
          success: function(data) {
            verif=data["value"];
          },
          error: function(html){
            alert(html);
          }
        });
        //END OF TESTING ZONE*/
         $.ajax({
            type: "GET",
            dataType: "json",
            url: "alreadyhere.php?nom="+name+"&emplacement="+emplacement
            }).done(function(data) {
            verif=data["value"];
            console.log(verif);
            }).fail(function(data) {
            alert('error');
            }).always(function(data) {
            console.log(data);
        });
        console.log(verif);
        
        if(verif!="true"){
          if (!confirm("Doublon pour "+verif+" ! Commander quand même? "+verif+" as already done an order, add it anyway?")) {
            return;   
          }
        }

        var bag="0"; var trad="0"; var cro="0"; var pac="0"; var ptd="0";

        var table = document.getElementById("paniert");
        // Récupérer les valeurs dans le tableau 
        for(var i=1; i<$('#paniert tr').length;i++){
          var tmp =table.rows[i].cells[1].innerHTML;
          switch(tmp) {
            case "Baguette":
                bag=table.rows[i].cells[0].innerHTML;
                break;
            
            case "Tradition":
                trad=table.rows[i].cells[0].innerHTML;
                break;
            
            case "Croissant":
                cro=table.rows[i].cells[0].innerHTML;
                break;
            
            case "Pain au Chocolat":
                pac=table.rows[i].cells[0].innerHTML;
                break;

            case "Petit Déjeuner":
                ptd=table.rows[i].cells[0].innerHTML;
                break;
          }
        }
        bag=bag+Math.floor((bagptd+1)/3);
        cro=parseInt(cro)+parseInt(croissantptd);
        pac=parseInt(pac)+parseInt(pacptd);
        $.ajax({
            url: "action.php?nom="+name+"&emplacement="+emplacement+"&baguette="+bag+"&tradition="+trad+"&croissant="+cro+"&pac="+pac+"&petitdej="+ptd, // Le nom du fichier indiqué dans le formulaire
            success: function(html) { // Je récupère la réponse du fichier PHP
              //alert(html);
              $('#validation').show();
              $('#validation').delay(5000).fadeOut(1000);
              resetPanier();
              bagptd=0;
              croissantptd=0;
              pacptd=0;
            },
            error: function(html){
              alert(html);
            }
        });

      });

    });


        </script>
  </head>

<?php 


// on se connecte à MySQL 
$db = mysql_connect('localhost', 'adminpain', 'rockmyroot'); 

// on sélectionne la base 
mysql_select_db('pain',$db); 

?> 


  <body role="document">

    <!-- Fixed navbar -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand">Commande Pain</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="index.php">Liste</a></li>
            <li><a href="admin.php">Admin</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container theme-showcase" role="main">
    <h2>Commande de pain - Bread Order</h2>
      <div class="row">
        <div class="col-md-3">
          <span class="label label-info">Merci d'entrer votre nom ainsi que votre emplacement</span></br>
          <span class="label label-info">Please enter your name and your pitch</span></br></br>
          <div class="input-group">
            <input type="text" class="form-control" id="name" value="" placeholder="Nom/Name">
            <input type="text" class="form-control" id="emplacement" value="" placeholder="Emplacement/Pitch">
          </div>


          <div id="panierh"class="highlight"><pre>
            <table id="paniert" class="table table-striped table-panier">
                <thead>
                  <tr>
                    <th>Qté</th>
                    <th>Nom</th>
                    <th>Prix</th>
                  </tr>
                </thead>
            </table>
            
            <button id="reset" type="button" class="btn" aria-label="Left Align">
              <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>
            </button>

          </pre></div>
          <div class="input-group">
            <span class="input-group-addon" id="sizing-addon2">Total:</span>
            <input id="total" value="0.00" type="text" class="form-control" aria-describedby="sizing-addon1" readonly>
          </div>
          <button id="valider" type="button" class="btn btn-primary btn-lg">Commander</button>
          <div id="validation" class="alert alert-success" role="alert" style="display:none;">
            <p> Commande validée avec succès! Order done! </p>
          </div>
          
        </div>

        <div class="col-md-9">
          <div class="btn-group-vertical article clic-article" role="group"  aria-label="..." width="30%" price="0.90" id="Baguette">
            <input type="image" src="img/baguette-blanche.jpg"  class="btn btn-default">
            <button type="button" class="btn btn-article">Baguette</button>
          </div>
          <div class="btn-group-vertical article clic-article" role="group"  aria-label="..." width="30%" price="1.30" id="Tradition">
            <input type="image"  src="img/S7929a_28218a.jpg"  class="btn btn-default">
            <button type="button" class="btn btn-article">Tradition</button>
          </div>

          <div class="btn-group-vertical article" role="group"  aria-label="..." width="30%" >
            <div id="confirmBox">
              <div class="message">Que désirez vous avec votre petit déjeuner?</div>
              <span class="button croissantV">Croissant</span>
              <span class="button pacV">Pain au Chocolat</span>
              <span class="button cancel">Annuler</span>
            </div> 
            <div class="btn-group-vertical petitdej" price="7.50" id="Petit Déjeuner">
              <input type="image" src="img/petit-dejeuner-français.jpg" class="btn btn-default">
              <button type="button" class="btn btn-article">Petit Déjeuner</button>
            </div>
          </div>
           
          <div class="btn-group-vertical article clic-article" role="group"  aria-label="..." width="30%" price="0.80" id="Pain au Chocolat">
            <input type="image" src="img/painauchocolat.png" class="btn btn-default">
            <button type="button" class="btn btn-article">Pain au Chocolat</button>
          </div>
          <div class="btn-group-vertical article clic-article" role="group"  aria-label="..." width="30%" price="0.80" id="Croissant">
            <input type="image" src="img/52358-11.png" class="btn btn-default">
            <button type="button" class="btn btn-article">Croissant</button>
          </div>
        </div>
      </div>
      <!-- div test -->

    </div> <!-- /container -->
  </body>
</html>
