<?php

session_start();

if(isset($_GET['logout'])){

    $logout_message = "<div class='msgln'><span class='left-info'>User <b class='user-name-left'>". $_SESSION['name'] ."</b> has left the game.</span><br></div>";
    file_put_contents("log.html", $logout_message, FILE_APPEND | LOCK_EX);

    session_destroy();
    header("Location: index.php");
}
if(isset($_POST['enter'])){
    if($_POST['name'] != ""){
        $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
    }
    else{
        echo '<span class="error">Please type in a name</span>';
    }
}

function loginForm(){
    echo
    '<div id="loginform">
    <p>Please enter your name to continue!</p>
    <form action="index.php" method="post">
      <label for="name">Name &mdash;</label>
      <input type="text" name="name" id="name" />
      <input type="submit" name="enter" id="enter" value="Enter" />
    </form>
  </div>';
}

?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Reaction game</title>
        <meta name="description" content="Users" />
        <link rel="stylesheet" href="styles.css" />
    </head>
    <body>
<?php
if(!isset($_SESSION['name'])){
    loginForm();
}
else {
?>
<div id="wrapper">
    <div id="menu">
        <p class="welcome">Welcome, <b><?php echo $_SESSION['name']; ?></b></p>
        <p class="End Session"><a id="exit" href="#">End session</a></p>
    </div>
</div>
<div id="chatbox">
    <?php
    if(file_exists("log.html") && filesize("log.html") > 0){
        $contents = file_get_contents("log.html");
        echo $contents;
    }
    ?>
</div>
<h1>Test your reaction time!</h1>
<p>Click on the boxes and circles as quickly as you can. Your reaction time will be posted below: </p>
<p id="printReactionTime"></p>
<div id="box"></div>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">

        function getRandomColor() {

            var letters = "0123456789ABCDEF".split('');
            var color = "#";
            for (var i = 0; i < 6; i++) {
                color += letters[Math.round(Math.random() * 15)];
            } //ends for loop
            return color;


        } // ends getRandomColor Function


        var clickedTime; var createdTime; var reactionTime;

        function makeBox() {
            var time=Math.random();
            time=time*3000;

            setTimeout(function() {

                if (Math.random()>0.5) {

                    document.getElementById("box").style.borderRadius="100px";

                } else {

                    document.getElementById("box").style.borderRadius="0";
                }

                var top= Math.random();
                top= top*300;
                var left= Math.random();
                left= left*500;

                document.getElementById("box").style.top = top + "px";
                document.getElementById("box").style.left = left + "px";

                document.getElementById("box").style.backgroundColor=getRandomColor();

                document.getElementById("box").style.display="block";

                createdTime=Date.now();

            }, time);

        }

        document.getElementById("box").onclick=function() {

            clickedTime=Date.now();

            reactionTime=(clickedTime-createdTime)/1000;

            $.ajax({
                type: 'POST',
                url: 'react.php',
                data: {'variable': reactionTime},
            });

            document.getElementById("printReactionTime").innerHTML="Your Reaction Time is: " + reactionTime + "seconds";

            this.style.display="none";

            makeBox();

        }

        makeBox();

        // jQuery Document
        $(document).ready(function () {
            $("box").click(function () {
                $.post("react.php");
                return false;
            });

            function loadLog() {

                $.ajax({
                    url: "log.html",
                    cache: false,
                    success: function (html) {
                        $("#chatbox").html(html); //Insert chat log into the #chatbox div

                    }
                });
            }

            setInterval (loadLog, 2500);

            $("#exit").click(function () {
                var exit = confirm("Are you sure you want to end the session?");
                if (exit == true) {
                    window.location = "index.php?logout=true";
                }
            });
        });
    </script>
    </body>
</html>
<?php
}
?>
