<?php
  function getUserList() {
    $ts_pw = posix_getpwuid(posix_getuid());
    $ts_mycnf = parse_ini_file($ts_pw['dir'] . "/replica.my.cnf");

    $mysqli = new mysqli('enwiki.labsdb', $ts_mycnf['user'], $ts_mycnf['password'], 'enwiki_p');

    if ($mysqli->connect_error) {
      echo "Connection failed: " . $mysqli->connect_error;
      return;
    }

    // Fetches the list of featured articles.
    $sql1 = "SELECT cl_from from categorylinks where cl_to = 'Featured_articles'";
    $res1 = $mysqli->query($sql1);

    if ($res1 == false) {
      echo 'The query failed.';
      return;
    }

    while($row1 = $res1->fetch_assoc()) {
      $pageid = $row1["cl_from"];

      // Fetches the top 10 contributors of a featured article.
      $sql2 = "SELECT rev_user from revision where rev_page = '$pageid' group by rev_user order by sum(rev_len) desc limit 10";
      $res2 = $mysqli->query($sql2);

      if ($res2 == false) {
        echo 'The query failed.';
        return;
      }

      while($row2 = $res2->fetch_assoc()) {
        $users[$row2["rev_user"]]++;
      }

    }

    $myfile = fopen("data.txt", "w");

    foreach ($users as $key => $value) {
      $txt = $key."=".$value."\n";
      fwrite($myfile, $txt);
    }
    
    fclose($myfile);
    
    $mysqli->close($myfile);
    
    return;
  }

  getUserList();
?> 
