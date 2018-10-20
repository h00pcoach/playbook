      <?php
            //Variables for connecting to your database.
            //These variable values come from your hosting account.
            $hostname = "hoopcoach.db.4016974.hostedresource.com";
            $username = "hoopcoach";
            $dbname = "hoopcoach";

            //These variable values need to be changed by you before deploying
            $password = "PlayBook11!";
        
            //Connecting to your database
            mysql_connect($hostname, $username, $password) OR DIE ("Unable to 
            connect to database! Please try again later.");
            mysql_select_db($dbname);
			
			?>