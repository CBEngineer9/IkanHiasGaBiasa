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
                echo `<script>alert($message)</script>`;
        }
?>