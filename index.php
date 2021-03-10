<?php

require_once("include/define.php");

?>

<html>

<head>
<title>Aaron Mathisen - Patron</title>
</head>

<body>

<div>
Aaron Mathisen - Patron
<br />
<a target="_blank" href="https://www.showclix.com/static/puzzle.html">Puzzle</a>
</div>

<br /><br />

<div>
    <form name="inputForm" method="post">
        Number of rows <select name="initialRows">
            <?php
                for($x = 1; $x < DEFAULTMAXROWS+1; $x++) {
                    echo "<option value='" . $x . "'";
                    if((!isset($_POST['initialRows']) && $x == DEFAULTROWS) || (isset($_POST['initialRows']) && $x == $_POST['initialRows']))
                        echo " SELECTED ";
                    echo ">" . $x . "</option>";
                }
            ?>
        </select>
        Number of seats per row <select name="initialSeats">
            <?php
                for($x = 1; $x < DEFAULTMAXSEATS+1; $x++) {
                    echo "<option value='" . $x . "'";
                    if((!isset($_POST['initialSeats']) && $x == DEFAULTSEATS) || (isset($_POST['initialSeats']) && $x == $_POST['initialSeats']))
                        echo " SELECTED ";
                    echo ">" . $x . "</option>";
                }
            ?>
        </select>
        Maximum tickets per request <select name="initialMaxTickets">
            <?php
                for($x = 1; $x < DEFAULTMAXSEATS+1; $x++) {
                    echo "<option value='" . $x . "'";
                    if((!isset($_POST['initialMaxTickets']) && $x == DEFAULTMAXTICKETS) || (isset($_POST['initialMaxTickets']) && $x == $_POST['initialMaxTickets']))
                        echo " SELECTED ";
                    echo ">" . $x . "</option>";
                }
            ?>
        </select>
        <br /><br />
        Seating Chart input
        <br />
        <textarea rows="10" cols="50" name="seatingChartInput"><?php echo (isset($_POST['seatingChartInput']) ? $_POST['seatingChartInput'] :"");?></textarea>
        <br /><br />
        <input type="submit" value="Generate Seating Chart">
    </form>
</form>
</div>

<div id="seatingChartOutput">
</div>

</body>

<?php
    if(isset($_POST['seatingChartInput'])) {
        require_once("class/seatingChart.php");
        $this_chart = new seatingChart($_POST);
        echo $this_chart->getPrettyOutput();
    }
        
?>
</html>