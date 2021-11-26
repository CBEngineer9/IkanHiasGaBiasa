<?php
        session_start();
        // $servername = "localhost";
        // $user = "proyekpw";
        // $pass = "proyekpw";
        // $dbname = "proyekpw";

        // SERVER
        $servername = "103.55.39.181:3306";
        $dbuser = "ikanhia1_proyekpw";
        $dbpass = "ikanhia1_proyekpw";
        $dbname = "ikanhia1_proyekpw";

        function alert(String $message = "")
        {
                echo "<script>alert(\"".$message."\")</script>";
        }
        
        /**
        * Check $_FILES[][name]
        *
        * @param string $filename - Uploaded file name.
        * @author Yousef Ismaeil Cliprz
        * @return bool true if file is illegal
        */
        function check_file_uploaded_name ($filename)
        {
                (bool) ((preg_match("`^[-0-9A-Z_\.]+$`i",$filename)) ? true : false);
        }

        /**
        * Check $_FILES[][name] length.
        *
        * @param string $filename - Uploaded file name.
        * @author Yousef Ismaeil Cliprz.
        * @return bool true if file is illegal
        */
        function check_file_uploaded_length ($filename)
        {
                return (bool) ((mb_strlen($filename,"UTF-8") > 225) ? true : false);
        }
?>