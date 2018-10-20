<?php
      //Variables for connecting to your database.
      //These variable values come from your hosting account.

      $hostname = "localhost";

      // LIVE
      // $username = "hoopcoac_mainus";
      $dbname = "hoopcoac_basketball";
      // $dbname = "bas1330408070797";

      // $password = "@hoopcoach";

      // LOCAL
      $username = "bjtactor";
      $password = "rump";

      //Connecting to your database
      mysql_connect($hostname, $username, $password) OR DIE ("Unable to
      connect to database! Please try again later.");
      mysql_select_db($dbname);

?>
