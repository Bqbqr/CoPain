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
      var array= {};
      
      //Askbox cas spéciaux
      function doConfirm(msg, cancel) {
          var confirmBox = $("#confirmBox");
          confirmBox.find(".message").text(msg);
          confirmBox.find(".option, .cancel").unbind().click(function () {
              confirmBox.hide();
          });
          confirmBox.find(".cancel").click(cancel);
          confirmBox.show();
      }

      //Fonction ajout d'article dans le tableau général
      function addTab(key){
        if(key in array){
          array[key]++;
        }else
        {
          array[key]=1;          
        }
      }

      //Fonction d'ajout d'article
      function addArticle(id, price, object){
        //On récupère la table
        var table = document.getElementById("paniert");
        var row = -1;

        //On cherche dans la table si ça existe déja
        for(var i=0; i<$('#paniert tr').length;i++){
          var tmp=table.rows[i].cells[1].innerHTML;
          if (tmp.toString().indexOf(id) > '-1')
            row=i;
        }

        //Vérification de la dispo ici ?
        

        //Si ça existe, on incrémente
        if(row!='-1'){
          table.rows[row].cells[0].innerHTML=parseInt(table.rows[row].cells[0].innerHTML)+1;
          table.rows[row].cells[2].innerHTML=(parseFloat(table.rows[row].cells[2].innerHTML)+parseFloat(price)).toFixed(2);
        }
        else{

          //Création d'une tr en dernier (-1) où l'on modifie la ligne
          var row = table.insertRow(row);

          var cell1 = row.insertCell(0);
          var cell2 = row.insertCell(1);
          var cell3 = row.insertCell(2);
          var cell4 = row.insertCell(3);

          cell1.innerHTML="1";
          cell2.innerHTML=id;
          cell3.innerHTML=price;
          //Ajout de la "corbeille" pour supprimer la ligne.
          cell4.innerHTML='<button type="button" object="'+object+'" value="'+id+'"class="btn btn-xs cancel"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>';

        }
        //Maj de la somme
        var sum=0.0;
        for(var i=1; i<$('#paniert tr').length;i++){
          sum+=parseFloat(table.rows[i].cells[2].innerHTML);
        }
        document.getElementById("total").value=sum.toFixed(2)+" €";
      }

      //Vidage du panier
      function resetPanier(){
        var table = document.getElementById("paniert");
        array={};

        $.ajax({
              url: 'index.php',
              success: function(html) {
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

      //Suppression d'une ligne
      $(document).on("click", ".cancel", function(){
        var id=$(this).attr('value');
        var key=$(this).attr('object');
        //On récupère la table
        var table = document.getElementById("paniert");
        var row = -1;
        //On cherche dans la table si ça existe déja
        for(var i=0; i<$('#paniert tr').length;i++){
          var tmp=table.rows[i].cells[1].innerHTML;
          if (tmp.toString().indexOf(id) > '-1')
            row=i;
        }
        array[key]=0;
        table.deleteRow(row);

      }); 

      $(document).on("click", ".optionarticle", function(){
        var id=$(this).attr('id');
        var price=$(this).attr('price');
        doConfirm("Quelle Viennoiserie?", 
          function cancel() {
              //Rien, on ferme juste la fenêtre, c'est la fonction suivante qui prends le relais
          });
      });

      $(document).on("click", ".option", function(){
        var idObjet=$(this).attr('value');
        var article=$(this).attr('article')

        var tmp;
        if(article in array)
          tmp=array[article];
        else
          tmp={};

        //Ajout de l'option dans l'array spécifique
        if(idObjet in tmp)
          tmp[idObjet]++;
        else
          tmp[idObjet]=1;
        array[article]=tmp;

        //Ajout dans le tableau
        addArticle($(this).attr('produit'),$(this).attr('price'),article);

      });


      //Clic sur reset==> Reset du panier
      $(document).on("click", "#reset", function(){
        resetPanier();
      });

      //Clic sur un article, appel de l'ajout d'article après récup des valeurs
      $(document).on("click", ".clic-article", function(){
        //Simplification: On récupère le prix et l'article, on envoie tout ça à la fonction qui se charge d'ajouter.
        var id=$(this).attr('id');
        var price=$(this).attr('price');
        //Ajout dans la liste.
        addTab($(this).attr('article'));
        addArticle(id,price,$(this).attr('article'));
      });


      //Validation de la commande
      $("#valider").on("click" , function(){
        var name=$('#name').val();
        var emplacement=$('#emplacement').val();
       
        //On transforme l'emplacement en majuscule, et en 3 caractères
        emplacement=emplacement.toUpperCase();
        emplacement=emplacement.replace(/\s/g, '');
        if(emplacement.length==2)
          emplacement = emplacement.slice(0, 1)+0+emplacement.slice(1,2);

        //Test si emplacement de la forme LettreChiffreChiffre
        var patt = new RegExp(/^[a-zA-Z][0-9]{1,2}$/);
        var res = patt.test(emplacement);
        if(name == '' || emplacement == '') {
            alert('Les champs doivent êtres remplis');
            return;
        }
        else if(res==false){
            alert("L'emplacement doit être une lettre et un (ou deux) chiffres. Pitch need to be Letter+Number");
            return;
        }
        var oldBtn = $("#valider").html();
        $("#valider").html('Loading...').attr('disabled', true);
        //Verification de l'existence du nom dans la bdd:
        $.ajax({
          type: "GET",
          dataType: "json",
          url: "isInDb.php?nom="+name+"&emplacement="+emplacement,
          success: function(data) {
            $("#valider").html(oldBtn).attr('disabled', false);
            var verif=data["value"];
            if(verif=="double"){
              var ask;
              ask="Une commande pour "+name+" existe déjà:\nAn Order for" +name+" already exists:\n"
              for (var k in data){
                  if (data.hasOwnProperty(k) && k!="value" && k!="numorder") {
                    ask=ask+data[k]+" "+k+"\n";
                  }
              }
              ask=ask+"Voulez vous remplacer cette commande?\nDo you want to replace this order?";
              if (!confirm(ask)) {
                return;   
              }else{
                deleteOrder(data["numorder"]);
              }
            }
            else if(verif!="true"){
              if (!confirm("Doublon pour "+verif+" ! Commander quand même? "+verif+" as already done an order, add it anyway?")) {
                return;   
              }
            }
            doYourBusiness(name, emplacement);
          },
          error: function(html){
            $("#valider").html(oldBtn).attr('disabled', false);
            alert("Erreur, veuillez réessayer ou commander au bar \nError, Please try again or order at the bar");
          }
        });

        
        });

        //Suppression d'une commande
        function deleteOrder(id) {
          $.ajax({
            url: 'admin/del.php?value=1&id='+id, // Le nom du fichier indiqué dans le formulaire
            success: function(html) { // Je récupère la réponse du fichier PHP
              //Nothing
            },
            error: function(html){
              alert(html);
            }
          });
        }
       
        //Fonction requête pour ajouter une fonction.
        function doYourBusiness(name, emplacement) {
          array["name"]=name;
          array["pitch"]=emplacement;
          if(Object.keys(array).length<=2){
            alert("Attention, commande vide! Order is Empty!");
            return;
          }
          $.ajax({        
            type: "POST",
            url: "addOrder.php",
            data: { 'parameters' : array},
            success: function(html){
              if(html=="404"){
                alert("Erreur de transmission, veuillez réessayer\nAn error has occurred, please try again");
                return;
              }
              $('#validation').show();
              $('#valider').hide();
              $('#validation').delay(5000).fadeOut(1000);
              $('#valider').delay(5000).fadeIn(1000);

              resetPanier();
            },
            error: function(html){
              alert(html);
            }
          }); 
        }

    });
        </script>
  </head>

<?php 


// on se connecte à mysqli 
include('secure/config.php');
include('admin/utils.php');
$db=mysqli_connect($SQLhost, $SQLlogin, $SQLpass,$SQLdb) or die(mysqli_error());

?> 


  <body role="document">

  <!-- Fixed navbar -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
          </button>
          <a href="index.php" class="navbar-brand">Commande Pain</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="allergies.html">Allergènes & Poids</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container theme-showcase" role="main">
    <h2>Commande de pain - Bread Order</h2>
    <h3>
        <?php
          echo "Pour le ".date_fr_arg(date('Y-m-d',strtotime(date("y-m-d")  . "+1 days")));
        ?>
    </h3>
    <p>
      Le pain est disponible dès 8h30.
      </p>
      <div class="row">
        <div class="col-md-3">
          <span class="label label-info">Entrer votre nom ainsi que votre emplacement</span></br>
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
                    <th></th>
                  </tr>
                </thead>
            </table>
            
            <button id="reset" type="button" class="btn" aria-label="Left Align">
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
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
          <?php
            $sql = 'SELECT listorder, nom, oa.article AS article, prix, img, quantity, stock, objet FROM article a INNER JOIN objetsInArticle oa on a.id=oa.article INNER JOIN objet o on o.id=oa.objet WHERE actif=1 AND stock!=0 ORDER BY listorder, quantity;'; 
            $req = mysqli_query($db,$sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysqli_error()); 

            while($data = mysqli_fetch_assoc($req)){
              //Ici on gère le cas spécial de produits multiples type petit déjeuner.
              if($data['quantity']==-1){
                echo '<div class="btn-group-vertical article" role="group"  aria-label="..." width="30%" >';
                echo '<div id="confirmBox">';
                echo '<div class="message">Que désirez vous avec votre '.$data['nom'].'?</div>';
                echo '<span class="button option" produit="'.$data['nom'].'" article="'.$data['article'].'" price="'.$data['prix'].'" value="'.$data['objet'].'">'.$data['objet'].'</span>';
                
                //Tricky part, had to check choices available for this one BEFORE closing everything...
                $dataminus1=$data;
                while($data = mysqli_fetch_assoc($req)){
                  if($dataminus1['nom']==$data['nom'] && $data['quantity']==-1){
                    echo '<span class="button option" produit="'.$data['nom'].'" article="'.$data['article'].'" price="'.$data['prix'].'" value="'.$data['objet'].'">'.$data['objet'].'</span>';
                  }
                  else if($dataminus1['nom']==$data['nom'] && $data['quantity']!=-1){
                    $data = mysqli_fetch_assoc($req);
                    break;
                  }
                  else
                    break;
                }
                echo '<span class="button cancel">Annuler</span></div>';
                echo '<div class="btn-group-vertical optionarticle" price="'.$dataminus1['prix'].'" article="'.$data['article'].'" id="'.$dataminus1['nom'].'">';
                echo '<input type="image" src="img/'.$dataminus1['img'].'"  class="btn btn-default">';
                echo '<button type="button" class="btn btn-article">'.$dataminus1['nom'].' '.$dataminus1['prix'].' €</button>';
                echo '</div>';
                echo '</div>';

              }
              // Fin des cas spéciaux

              //Cas spéciaux où on se retrouve avec un data vide a cause de la boucle précédente
              if($data['img']=="")  break;

              echo '<div class="btn-group-vertical article clic-article" role="group"  aria-label="..." width="30%" price="'.$data['prix'].'" article="'.$data['article'].'" id="'.$data['nom'].'">';
              echo '<input type="image" src="img/'.$data['img'].'"  class="btn btn-default">';
              echo '<button type="button" class="btn btn-article">'.$data['nom'].' '.$data['prix'].' €</button>';
              echo '</div>';
              
            }

          ?>
        </div>
      </div>
      <!-- div test -->

    </div> <!-- /container -->
  </body>
</html>
