<!DOCTYPE html>
<html lang="en">
  <head>
    <title>FA count</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://tools-static.wmflabs.org/cdnjs/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://tools-static.wmflabs.org/cdnjs/ajax/libs/jquery/3.2.1/jquery.js"></script>
    <script src="https://tools-static.wmflabs.org/cdnjs/ajax/libs/twitter-bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>
    <link rel = "stylesheet" type = "text/css" href = "index.css" />
  </head>

  <body>
    <div class="jumbotron text-center">
      <h1>Featured Article - Micro Task</h1> 
      <p>Enter username</p> 
      <form class="form-inline" id="myForm" action="" method="post">
        <div class="input-group">
          <input name="searchTerm" type="text" class="form-control" size="50" placeholder="Username" required>
          <div class="input-group-btn">
            <button class="btn btn-default" type="submit"> 
              <i class="glyphicon glyphicon-search"></i>
            </button>
          </div>
        </div>
      </form>
    </div>

    <?php
      function getCount() {
        if(isset($_POST['searchTerm']))  { 
          $ts_pw = posix_getpwuid(posix_getuid());
          $ts_mycnf = parse_ini_file($ts_pw['dir'] . "/replica.my.cnf");

          $mysqli = new mysqli('enwiki.labsdb', $ts_mycnf['user'], $ts_mycnf['password'], 'enwiki_p');

          if ($mysqli->connect_error) {
            echo "Connection failed: " . $mysqli->connect_error;
            return;
          }

          $username = $_POST["searchTerm"];

          $sql = $mysqli->prepare("SELECT user_id FROM user WHERE user_name = ? limit 1");
          $sql->bind_param('s', $username);
          $sql->execute();
          $res = $sql->get_result();

          if ($res == false) {
            echo 'The query failed.';
            return;
          }

          if($res->num_rows == 0) {
            echo "<div class='panel panel-default col-xs-4' > <div class='panel-body'> Username doesn't exists </div></div>";
            return;
          }

          $userid = $res->fetch_assoc()["user_id"];

          $cnt = 0;
          
          if(!file_exists("data.txt")) {
            echo "File not found";
            return;
          } 
          else {
            $myfile = fopen("data.txt", "r");
          }
          
          while(!feof($myfile)) {
            $str = fgets($myfile);
            $text = explode("=", $str);
            if($text[0] == $userid) {
              $cnt = $text[1];
              break;
            }
          }

          fclose($myfile);

          echo "<div class='panel panel-default col-xs-4' > <div class='panel-body'> Username: ".$username."<br>Userid: ".$userid."<br>Number of featured articles where the user is among the top 10 editors: ".$cnt."</div></div>"; 

          $mysqli->close();
        }
          return;
      }

      getCount();
    ?>
    
  </body>
</html>
